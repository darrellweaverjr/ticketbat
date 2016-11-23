<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
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
            //get all records        
            $manifests = Manifest::all()->groupBy('show_time_id');
            $show_times = [];
            $info = DB::table('show_times')
                ->join('shows', 'shows.id', '=', 'show_times.show_id')
                ->select('show_times.id', 'shows.name', 'show_times.show_time')
                ->get()->toArray();
            foreach ($info as $s)
                $show_times[$s->id] = $s;
            //return view
            return view('admin.manifests.index',compact('manifests','show_times'));
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
                        $data = 'The system could not load the information from the DB: it has not a valid format.';
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
                    $data = 'The format is not valid.';
                    return View::make('command.report_manifest', compact('data','format'))->render();
                }
            }
        } catch (Exception $ex) {
            throw new Exception('Error Manifests View: '.$ex->getMessage());
        }
    } 
    
}
