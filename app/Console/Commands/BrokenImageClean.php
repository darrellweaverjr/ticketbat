<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Image;

class BrokenImageClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BrokenImage:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for cleaning all the broken images saved in old /uploads folder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            //create progress bar
            $progressbar = $this->output->createProgressBar(7);
            
            //bands
            DB::table('bands')
                    ->where('image_url','like','/uploads/%')
                    ->update(['image_url'=>null]);
            $progressbar->advance(); 
            //banners
            DB::table('banners')
                    ->where('file','like','/uploads/%')
                    ->delete();
            $progressbar->advance(); 
            //deals
            DB::table('deals')
                    ->where('image_url','like','/uploads/%')
                    ->delete();
            $progressbar->advance(); 
            //shows
            DB::table('shows')
                    ->where('sponsor_logo_id','like','/uploads/%')
                    ->update(['sponsor_logo_id'=>null]);
            $progressbar->advance(); 
            //sliders
            DB::table('sliders')
                    ->where('image_url','like','/uploads/%')
                    ->delete();
            $progressbar->advance();
            //stages
            DB::table('stages')
                    ->where('image_url','like','/uploads/%')
                    ->update(['image_url'=>'']);
            $progressbar->advance();
            //images
            $images = DB::table('images')
                    ->select('id')
                    ->where('url','like','/uploads/%')
                    ->get();
            foreach ($images as $i)
            {
                DB::table('package_images')->where('image_id', $i->id )->delete();
                DB::table('show_awards')->where('image_id', $i->id )->delete();
                DB::table('show_images')->where('image_id', $i->id )->delete();
                DB::table('stage_image_ticket_type')->where('image_id', $i->id )->delete();
                DB::table('user_images')->where('image_id', $i->id )->delete();
                DB::table('venue_images')->where('image_id', $i->id )->delete();
            }
            DB::table('images')
                    ->where('url','like','/uploads/%')
                    ->delete();
            $progressbar->advance();
            
            //finish progress bar
            $progressbar->finish();
        } catch (Exception $ex) {
            throw new Exception('Error cleaning images with BrokenImageClean Command: '.$ex->getMessage());
        }
    }
}
