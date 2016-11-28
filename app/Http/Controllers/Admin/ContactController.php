<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            //get all records        
            $contacts = Contact::orderBy('created','desc')->get();
            //return view
            return view('admin.contacts.index',compact('contacts'));
        } catch (Exception $ex) {
            throw new Exception('Error Contact Logs Index: '.$ex->getMessage());
        }
    } 
    
}
