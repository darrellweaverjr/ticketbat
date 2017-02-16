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
                                        ->where(DB::raw('shows.venue_id IN ('.Auth::user()->venues_edit.') OR shows.audit_user_id'),'=',Auth::user()->id)
                                        ->groupBy('show_times.id','manifest_emails.created')
                                        ->orderBy(DB::raw('DATE_FORMAT(manifest_emails.created,"%Y-%m-%d")'),'desc')
                                        ->orderBy('manifest_emails.show_time_id','desc')
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
                                        ->orderBy(DB::raw('DATE_FORMAT(manifest_emails.created,"%Y-%m-%d")'),'desc')
                                        ->orderBy('manifest_emails.show_time_id','desc')
                                        ->get();
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
                    return PDF::loadHTML($manifest_pdf->render())->setPaper('a4', 'portrait')->setWarnings(false)->download('TicketBat Admin - manifests - '.$id.'.pdf');
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
    
}
