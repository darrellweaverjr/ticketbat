<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Artisan;

/**
 * Manage ACLs
 *
 * @author ivan
 */
class CommandController extends Controller{
    /**
     * List all acls and return default view.
     *
     * @return view
     */
    public function index()
    {
        try {
            //init
            $input = Input::all(); 
            $commands = [];
                
            $commands[] = ['command'=>'Report:manifest','values'=>[]];
            $commands[] = ['command'=>'Report:sales','values'=>['days'=>1,'onlyadmin'=>1]];
            $commands[] = ['command'=>'Report:sales_receipt','values'=>['days'=>1]];
            $commands[] = ['command'=>'Report:financial','values'=>['weeks'=>0]];
            $commands[] = ['command'=>'Report:consignment','values'=>[]];

            $commands[] = ['command'=>'Promo:announced','values'=>['days'=>1]];

            $commands[] = ['command'=>'Shoppingcart:clean','values'=>['days'=>10]];
            $commands[] = ['command'=>'Shoppingcart:recover','values'=>['hours'=>4]];
            $commands[] = ['command'=>'Contract:update_tickets','values'=>[]];
            $commands[] = ['command'=>'Consignments:check','values'=>[]];
              
            if(isset($input) && isset($input['command']))
            {
                foreach ($commands as $c)
                {
                    if($c['command'] == $input['command'])
                    {
                        foreach ($c['values'] as $k=>$i)
                        {
                            if(isset($input[$k]))
                                $c['values'][$k] = $input[$k];
                        }
                        $exitCode = Artisan::call( $c['command'], $c['values'] );
                        if($exitCode)
                            return ['success'=>true, 'msg'=>'Command executed successfully!'];
                        return ['success'=>false, 'msg'=>'Command executed with failure!'];
                    }
                }
                return ['success'=>false, 'msg'=>'Command not found!'];
            }
            else
            {
                //return view
                return view('admin.commands.index',compact('commands'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Command Index: '.$ex->getMessage());
        }
    } 
}
