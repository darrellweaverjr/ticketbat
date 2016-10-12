<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EmailSG;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        echo '<a href="../logout">logout</a><br><br><br>';
        
        /*$email = new EmailSG('nick@ticketbat.com','ivan@ticketbat.com,ivankbc333@gmail.com','Welcome to TicketBat!');
        $email->body('welcome',array('username'=>'ivankbc333@gmail.com','password'=>'mycontrasena'));
        $email->template('a7b5c451-4d26-4292-97cd-239880e7dd20');*/
        //$response = $email->send();
        
        
        
        dd(\App\Http\Models\Customer::find(5)->location   );
       
        /*
        public static function getACLs($id)
	{
		$acl_codes = array();
		$acls = (array) DB::select("Select permission_id, user_type_id, p.code, ut.user_type, group_concat(permission_type) as permission_types, permission_scope
			From user_type_permissions upt
			Inner join permissions p on p.id = upt.permission_id
			Inner join user_types ut on ut.id = upt.user_type_id and ut.id = ?
			Group by permission_id, user_type_id", array($id));
		if($acls)
		{
			foreach($acls as &$row)
			{
				$row->permission_types = explode(",", $row->permission_types);
				$acl_codes[$row->code] = $row->permission_scope;
			}
		}
		return compact("acls", "acl_codes");
	}
        */
        
        
        dd('TicketBat Admin request successfully');
    }
}
