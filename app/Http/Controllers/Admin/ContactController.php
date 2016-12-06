<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Http\Models\Contact;

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
                $start_date = date('Y-m-d H:i:s',getlastmod());
                $end_date = date('Y-m-d H:i:s');
            }
            //get all records        
            $contacts = Contact::whereBetween('contacts.created', [$start_date,$end_date])->orderBy('created','desc')->get();
            //return view
            return view('admin.contacts.index',compact('contacts','start_date','end_date'));
        } catch (Exception $ex) {
            throw new Exception('Error Contact Logs Index: '.$ex->getMessage());
        }
    } 
    
}
