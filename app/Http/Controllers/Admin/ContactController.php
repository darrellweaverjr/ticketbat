<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Contact;
use App\Http\Models\Util;

/**
 * Manage Contacts
 *
 * @author ivan
 */
class ContactController extends Controller{
    
    /**
     * List all contact logs and return default view.
     *
     * @return view
     */
    public function index()
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && isset($input['start_date']) && isset($input['end_date']))
            {
                //input dates 
                $start_date = date('Y-m-d H:i:s',strtotime($input['start_date']));
                $end_date = date('Y-m-d H:i:s',strtotime($input['end_date']));
            }
            else
            {
                //default dates 
                $start_date = date('Y-m-d H:i:s', strtotime('-30 DAY'));
                $end_date = date('Y-m-d H:i:s');
            }
            //if user has permission to view
            $contacts = [];
            $status = Util::getEnumValues('contacts','status');
            if(in_array('View',Auth::user()->user_type->getACLs()['CONTACTS']['permission_types']))
            {
                if(Auth::user()->user_type->getACLs()['CONTACTS']['permission_scope'] == 'All')
                {
                    $contacts = Contact::whereBetween('contacts.created', [$start_date,$end_date])->orderBy('created','desc')->get();
                }
            }
            //return view
            return view('admin.contacts.index',compact('contacts','status','start_date','end_date'));
        } catch (Exception $ex) {
            throw new Exception('Error Contact Logs Index: '.$ex->getMessage());
        }
    } 
    
    /**
     * Updated purchase.
     *
     * @void
     */
    public function save()
    {
        try {
            //init
            $input = Input::all();
            //save all record
            if($input && !empty($input['id']) && !empty($input['status']))
            {
                $contact = Contact::find($input['id']);
                if($contact)
                {
                    $contact->status = $input['status'];
                    $contact->save();
                    return ['success'=>true,'msg'=>'The item has been successfully updated!'];
                }
                return ['success'=>false,'msg'=>'There was an error updating the contact.<br>That item is not longer in the system.'];
            }
            return ['success'=>false,'msg'=>'There was an error updating the contact.<br>You must select a valid item and contact.'];
        } catch (Exception $ex) {
            throw new Exception('Error Purchases Save: '.$ex->getMessage());
        }
    }
    
}
