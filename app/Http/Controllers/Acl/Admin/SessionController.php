<?php

namespace App\Http\Controllers\Acl\Admin;

use View;
use Event;
use Config;
use Sentry;
use Session;
use Request;
use Redirect;

use Sentinel\FormRequests\LoginRequest;
use Illuminate\Support\Facades\Response;
use Sentinel\Traits\SentinelViewfinderTrait;
use Sentinel\Traits\SentinelRedirectionTrait;
use Illuminate\Routing\Controller as BaseController;
use Sentinel\Repositories\Session\SentinelSessionRepositoryInterface;

class SessionController extends BaseController
{
    /**
     * Traits
     */
    use SentinelRedirectionTrait;
    use SentinelViewfinderTrait;

    /**
     * Constructor
     */
    public function __construct(SentinelSessionRepositoryInterface $sessionManager)
    {
        $this->session = $sessionManager;
    }

    /**
     * Show the login form
     */
    public function create()
    {
        // Is this user already signed in?
        if (Sentry::check()) {
            return redirect('admin/dashboard');
        }

        // No - they are not signed in.  Show the login form.
        return $this->viewFinder(config('sentinel.view.session_login'));
    }

    /**
     * Attempt authenticate a user.
     *
     * @return Response
     */
    public function store(LoginRequest $request)
    {
        // Gather the input
        $data = $request->all();

        // Attempt the login
        $result = $this->session->store($data);

        // Did it work?
        if ($result->isSuccessful()) {
            // Login was successful.  Determine where we should go now.
            if (!config('sentinel.views_enabled')) {
                // Views are disabled - return json instead
                return Response::json('success', 200);
            }
            // Views are enabled, so go to the determined route
            $redirect_route = config('sentinel.routing.session_store');

            return Redirect::intended($this->generateUrl($redirect_route));
        } else {
            // There was a problem - unrelated to Form validation.
            if (!config('sentinel.views_enabled')) {
                // Views are disabled - return json instead
                return Response::json($result->getMessage(), 400);
            }
            Session::flash('error', $result->getMessage());

            return Redirect::route('sentinel.session.create')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy()
    {
        $this->session->destroy();

        return $this->redirectTo('session_destroy');
    }
}
