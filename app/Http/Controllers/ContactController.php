<?php

namespace App\Http\Controllers;

use Alert;
use Redirect;

use App\Contact;

use Illuminate\Http\Request;
use App\Http\Requests\ContactEditFormRequest as ContactEditFormRequest;

class ContactController extends Controller
{
    public function __construct()
    {
        // Admin Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:communications.access');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contact = Contact::findorfail($id);

        return view('communications.bulk.editContact', compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContactEditFormRequest $request, $id)
    {
        $contact = Contact::findorfail($id);

        $contact->phone_number = $request->phone_number;

        $contact->save();

        $group = $contact->group_id;

        Alert::success('Contact Editted Successfully!');

        return redirect()->action(
            'BulkController@show', ['id' => $group]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contact = Contact::findorfail($id);

        $group = $contact->group_id;

        $contact->delete();

        Alert::success('Contact Deleted Successfully!');

        return redirect()->action(
            'BulkController@show', ['id' => $group]
        );
    }
}
