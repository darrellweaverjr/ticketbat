<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Manifest;
use App\Http\Models\Util;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Command\ReportManifestController;

/**
 * Manage ACLs
 *
 * @author ivan
 */
class ManifestController extends Controller{

    /**
     * List all manifest and return default view.
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
            $manifests = [];
            if(in_array('View',Auth::user()->user_type->getACLs()['MANIFESTS']['permission_types']))
            {
                if(Auth::user()->user_type->getACLs()['MANIFESTS']['permission_scope'] != 'All')
                {
                    $manifests = DB::table('manifest_emails')
                                        ->join('show_times', 'show_times.id', '=' ,'manifest_emails.show_time_id')
                                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                        ->select('manifest_emails.*', 'shows.name', 'show_times.show_time')
                                        ->whereBetween('manifest_emails.created', [$start_date,$end_date])
                                        ->where(function($query)
                                        {
                                            $query->whereIn('shows.venue_id',[Auth::user()->venues_edit])
                                                  ->orWhere('shows.audit_user_id','=',Auth::user()->id);
                                        })
                                        ->groupBy('show_times.id','manifest_emails.created')
                                        ->orderBy('show_times.show_time','desc')
                                        ->orderBy('manifest_emails.created','desc')
                                        ->get();
                }//all
                else
                {
                    $manifests = DB::table('manifest_emails')
                                        ->join('show_times', 'show_times.id', '=' ,'manifest_emails.show_time_id')
                                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                        ->select('manifest_emails.*', 'shows.name', 'show_times.show_time')
                                        ->whereBetween('manifest_emails.created', [$start_date,$end_date])
                                        ->groupBy('show_times.id','manifest_emails.created')
                                        ->orderBy('show_times.show_time','desc')
                                        ->orderBy('manifest_emails.created','desc')
                                        ->get();
                }
                //search if email was sent or not
                foreach ($manifests as $m)
                {
                    if(Util::isJSON($m->email) && isset(json_decode($m->email,true)['sent']))
                        $m->sent = (!empty(json_decode($m->email,true)['sent']))? 1 : 0;
                }
            }
            //return view
            return view('admin.manifests.index',compact('manifests','start_date','end_date'));
        } catch (Exception $ex) {
            throw new Exception('Error Manifests Index: '.$ex->getMessage());
        }
    }
    /**
     * View manifest in csv or in pdf.
     *
     * @return csv or pdf
     */
    public function view($format,$id)
    {
        try {
            $manifest = Manifest::find($id);
            //check email data sent
            if($manifest && isset($manifest->email))
            {
                //check format
                if($format==='csv')
                {
                    if(Util::isJSON($manifest->email))
                    {
                        $data = json_decode($manifest->email, true);
                        $manifest_csv = View::make('command.report_manifest', compact('data','format'));
                        return Util::downloadCSV($manifest_csv,'TicketBat Admin - manifests - '.$id);
                    }
                    else
                    {
                        $format='plain';
                        $data = '<script>alert("The system could not load the information from the DB. It has not a valid format.");window.close();</script>';
                        return View::make('command.report_manifest', compact('data','format'))->render();
                    }
                }
                else if($format==='pdf')
                {
                    if(Util::isJSON($manifest->email))
                        $data = json_decode($manifest->email, true);
                    else
                    {
                        $format='plain';
                        $data = $manifest->email;
                    }
                    $manifest_pdf = View::make('command.report_manifest', compact('data','format'));
                    return PDF::loadHTML($manifest_pdf->render())->setPaper('a4', 'landscape')->setWarnings(false)->download('TicketBat Admin - manifests - '.$id.'.pdf');
                }
                else
                {
                    $format='plain';
                    $data = '<script>alert("The system could not load the information from the DB. It has not a valid format.");window.close();</script>';
                    return View::make('command.report_manifest', compact('data','format'))->render();
                }
            }
            else
            {
                $format='plain';
                $data = '<script>alert("The system could not load the information from the DB. There is not that manifest.");window.close();</script>';
                return View::make('command.report_manifest', compact('data','format'))->render();
            }
        } catch (Exception $ex) {
            throw new Exception('Error Manifests View: '.$ex->getMessage());
        }
    }

    /**
     * Resend email.
     *
     * @return boolean
     */
    public function send()
    {
        try {
            //init
            $input = Input::all();
            if(isset($input) && isset($input['id']) && isset($input['action']) && in_array($input['action'],[0,1,2]))
            {
                if($input['action']==2 && empty($input['email']))
                    return ['success'=>false,'msg'=>'There was an error.<br>Your must enter a valid email.'];
                $manifest = Manifest::find($input['id']);
                if($manifest && !empty($manifest->email) && Util::isJSON($manifest->email))
                {
                    $emails = null;
                    if($input['action']==1)
                    {
                        $mail = DB::table('manifest_emails')
                                        ->join('show_times', 'show_times.id', '=' ,'manifest_emails.show_time_id')
                                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                        ->select('shows.emails')
                                        ->where('manifest_emails.id','=',$manifest->id)
                                        ->first();
                        $emails = $mail->emails;
                    }
                    else if($input['action']==2)
                        $emails = $input['email'];

                    if($manifest->send($emails,null))
                        return ['success'=>true,'msg'=>'The email was sent successfully!'];
                    return ['success'=>false,'msg'=>'There was an error sending the email.'];
                }
                return ['success'=>false,'msg'=>'There was an error.<br>That manifest is not longer in the system.'];
            }
            return ['success'=>false,'msg'=>'There was an error.<br>Your must select all valid options.'];

        } catch (Exception $ex) {
            throw new Exception('Error Manifests Send: '.$ex->getMessage());
        }
    }
    /**
     * Generate previous manifest.
     *
     * @return boolean
     */
    public function generate()
    {
        try {
            //init
            $input = Input::all();
            if(isset($input) && !empty($input['date']) && strtotime($input['date']))
            {
                $control = new ReportManifestController($input['date']);
                $response = $control->init();
                if($response)
                    return ['success'=>true,'msg'=>'The manifest was generated!'];
                return ['success'=>false,'msg'=>'There was an error generating the manifest.'];
            }
            return ['success'=>false,'msg'=>'There was an error.<br>Your must select all valid date.'];

        } catch (Exception $ex) {
            throw new Exception('Error Manifests Generate: '.$ex->getMessage());
        }
    }

}
