<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Manifest;
use App\Http\Models\ShowTime;

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
            if(isset($input) && isset($input['id']))
            {
                //get selected record
//                $band = Band::find($input['id']);  
//                if(!$band)
//                    return ['success'=>false,'msg'=>'There was an error getting the band.<br>Maybe it is not longer in the system.'];
//                $shows = [];
//                foreach($band->show_bands as $s)
//                    $shows[] = [$s->name,$s->pivot->n_order];
//                $band->image_url = 'https://www.ticketbat.com'.$band->image_url; //$band->image_url = asset($band->image_url);
//                return ['success'=>true,'band'=>array_merge($band->getAttributes(),['shows[]'=>$shows])];
            }
            else
            {
                //get all records        
                $manifests = Manifest::all()->groupBy('show_time_id');
//                $manifests = DB::table('manifest_emails')
//                                    ->join('show_times', 'show_times.id', '=', 'manifest_emails.show_time_id')
//                                    ->join('shows', 'shows.id', '=', 'show_times.show_id')
//                                    ->orderBy('show_times.show_time', 'desc')
//                                    ->groupBy('manifest_emails.show_time_id')
//                                    ->select('manifest_emails.*', 'shows.name', 'show_times.show_time')
//                                    ->get();
                
                $show_times = [];
                $info = DB::table('show_times')
                    ->join('shows', 'shows.id', '=', 'show_times.show_id')
                    ->select('show_times.id', 'shows.name', 'show_times.show_time')
                    ->get()->toArray();
                foreach ($info as $s)
                    $show_times[$s->id] = $s;
                //return view
                return view('admin.manifests.index',compact('manifests','show_times'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Manifests Index: '.$ex->getMessage());
        }
    } 
    
}
