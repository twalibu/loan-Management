<?php

namespace App\Http\Controllers\Acl\Tenant;

use DB;
use Alert;
use Session;
use Reminder;
use Sentinel;
use App\Http\Requests;
use Centaur\AuthManager;
use Illuminate\Http\Request;
use Centaur\Mail\CentaurPasswordReset;
use App\Http\Controllers\Controller;
use App\Http\Requests\TenantPasswordResetRequestForm as TenantPasswordResetRequestForm;
use App\Http\Requests\TenantPasswordResetCreateRequestForm as TenantPasswordResetCreateRequestForm;

class PasswordController extends Controller
{
    /** @var Centaur\AuthManager */
    protected $authManager;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(AuthManager $authManager)
    {
        $this->middleware('sentinel.guest');
        $this->authManager = $authManager;
    }

    /**
     * Show the password reset request form
     * @return View
     */
    public function getRequest()
    {
        return view('acl.tenant.auth.reset');
    }

    /**
     * Send a password reset link
     * @return Response|Redirect
     */
    public function postRequest(TenantPasswordResetRequestForm $request)
    {
        // Fetch the user in question
        $user = Sentinel::findUserByCredentials(['email' => $request->get('email')]);

        // Only send them an email if they have a valid, inactive account
        if ($user) {
            // Generate a new code
            $reminder = Reminder::create($user);

            $data = [
                'code' => $reminder->code,
                'email' => $user->email,
                'last_name' => $user->last_name
            ];
            $email = $user->email;

            $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
            $beautymail->send('mails.tenants.reset', $data, function($message) use($email)
            {
                $message
                    ->from('no-reply@Case-alert.pro')
                    ->to($email)
                    ->subject('Password Reset | Case Alert!');
            });
        }

        $message = 'Instructions for changing your password will be sent to your email address if it is associated with a valid account.';

        if ($request->ajax()) {
            return response()->json(['message' => $message, 'code' => $code], 200);
        }

        Alert::success($message, 'Password Reset')->persistent('Close');
        return redirect('/login');
    }


    /**
     * Show the password reset form if the reset code is valid
     * @param  Request $request
     * @param  string  $code
     * @return View
     */
    public function getReset(Request $request, $code)
    {
        // Is this a valid code?
        if (!$this->validatePasswordResetCode($code)) {
            // This route will not be accessed via ajax;
            // no need for a json response
            Alert::error('Invalid or expired password reset code, please request a new link', 'Invalid Link')->persistent('Close');
            return redirect('login');
        }

        return view('acl.tenant.auth.password')
            ->with('code', $code);
    }

    /**
     * Process a password reset form submission
     * @param  Request $request
     * @param  string  $code
     * @return Response|Redirect
     */
    public function postReset(TenantPasswordResetCreateRequestForm $request, $code)
    {
        // Attempt the password reset
        $result = $this->authManager->resetPassword($code, $request->get('password'));

        if ($result->isFailure()) {
            return $result->dispatch();
        }

        Alert::success('Password Reset Succeessful. now you can Login with your new Password', 'Password Reset Complete')->persistent('Close');
        // Return the appropriate response
        return $result->dispatch(route('auth.login.form'));
    }

    /**
     * @param  string $code
     * @return boolean
     */
    protected function validatePasswordResetCode($code)
    {
        return DB::table('reminders')
                ->where('code', $code)
                ->where('completed', false)->count() > 0;
    }
}
