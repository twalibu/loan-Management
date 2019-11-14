<?php

namespace App\Http\Controllers;

use Input;
use Excel;
use Alert;
use Sentinel;
use Redirect;

use App\Group;
use App\Contact;
use Illuminate\Http\Request;
use App\Http\Requests\GroupFormRequest as GroupFormRequest;
use App\Http\Requests\GroupAddFormRequest as GroupAddFormRequest;
use App\Http\Requests\GroupEditFormRequest as GroupEditFormRequest;

class BulkController extends Controller
{
    public function __construct()
    {
        // Admin Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:communications.access');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $groups = Group::where('tenant_id', $tenant)->get();

        return view('communications.tenant.bulk.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('communications.tenant.bulk.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $group = New Group;

        $group->name            = $request->name;
        $group->tenant_id       = $tenant;

        $group->save();

        $group = $group->id;        
        $auto = $request->file;
        $phone_numbers = $request->phone_numbers;

        /* Check If File */
        if($auto){
            // Import A User Provided File
            $file = Input::file('file');
            Excel::load($file, function($reader) use($group) {

                // Getting All Policies
                $dbseeder = $reader->select(array('contact'))->get();

                foreach ($dbseeder as $entry) {
                    $contact = new Contact;

                    $contact->phone_number      = $entry->contact;
                    $contact->group_id          = $group;

                    $contact->save();
                }
            });
        }

        /* Check Manual Input */
        if($phone_numbers){
            $numbers = explode(PHP_EOL, $request->phone_numbers);

            foreach ($numbers as $number) {
                $contact = new Contact;

                $contact->phone_number      = $number;
                $contact->group_id          = $group;

                $contact->save();
            }
        }

        Alert::success('Contact Group Registered!');

        return Redirect::to('bulk');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function add($id)
    {
        $group = Group::findorfail($id);

        return view('communications.tenant.bulk.add', compact('group'));
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postAdd(GroupAddFormRequest $request, $id)
    {
        $group = Group::findorfail($id);

        $group = $group->id;        
        $auto = $request->file;
        $phone_numbers = $request->phone_numbers;

        /* Check If File */
        if($auto){
            // Import A User Provided File
            $file = Input::file('file');
            Excel::load($file, function($reader) use($group) {

                // Getting All Policies
                $dbseeder = $reader->select(array('contact'))->get();

                foreach ($dbseeder as $entry) {
                    $contact = new Contact;

                    $contact->phone_number      = $entry->contact;
                    $contact->group_id          = $group;

                    $contact->save();
                }
            });
        }

        /* Check Manual Input */
        if($phone_numbers){
            $numbers = explode(PHP_EOL, $request->phone_numbers);

            foreach ($numbers as $number) {
                $contact = new Contact;

                $contact->phone_number      = $number;
                $contact->group_id          = $group;

                $contact->save();
            }
        }

        Alert::success('Contact Added To Group!');

        return Redirect::to('bulk');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = Group::findorfail($id);

        return view('communications.tenant.bulk.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $group = Group::findorfail($id);

        return view('communications.tenant.bulk.edit', compact('group'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GroupEditFormRequest $request, $id)
    {
        $group = Group::findorfail($id);

        $group->name = $request->name;

        $group->save();

        Alert::success('Group Editted Successfully!');

        return Redirect::to('bulk');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $group = Group::findorfail($id);

        $contacts = Contact::where('group_id', $group->id)->get();

        foreach ($contacts as $contact) {
            $contact->delete();
        }

        $group->delete();

        Alert::success('Group Deleted Successfully!');

        return Redirect::to('bulk');
    }
}
