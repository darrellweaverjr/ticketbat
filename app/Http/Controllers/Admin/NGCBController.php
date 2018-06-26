<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

/**
 * Manage ACLs
 *
 * @author ivan
 */
class NGCBController extends Controller{
    /**
     * Return default view.
     *
     * @return view
     */
    public function index()
    {
        try {
                //return view
                return view('admin.ngcb.index');
           
        } catch (Exception $ex) {
            throw new Exception('Error NGCB Index: '.$ex->getMessage());
        }
    }
}
