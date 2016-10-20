<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Mail\EmailSG;

class PromoAnnounced extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Promo:announced {days=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for send out an email to all customers that have ever purchased a ticket at that venue';

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
            $days = $this->argument('days');   
            $promo_venues = array();
            $just_announced = DB::select('  SELECT v.id AS venue_id,v.name AS venue, s.name, s.slug, s.presented_by,
                                            (SELECT i.url FROM show_images si INNER JOIN images i ON i.id= si.image_id 
                                            WHERE si.show_id = s.id and i.image_type = "Logo" LIMIT 1) AS image_url, 
                                            (SELECT DATE_FORMAT(st.show_time, "%W, %M %D, %Y") FROM show_times st WHERE st.show_id = s.id ORDER BY st.show_time LIMIT 1) AS show_time
                                            FROM shows s 
                                            INNER JOIN venues v ON v.id =s.venue_id 
                                            WHERE v.enable_weekly_promos = 1 AND s.is_active = 1 AND s.created > (NOW() - INTERVAL '.$days.' DAY)'); 
            //create progress bar
            $progressbar = $this->output->createProgressBar(count($just_announced));
            foreach ($just_announced as $ja) 
            {
                if(!isset($promo_venues[$ja->venue_id]))
                {
                    $promo_venues[$ja->venue_id] = array('announced'=>[],'week'=>[]);   
                    $this_week = DB::select('SELECT s.name, s.slug, v.name AS venue, 
                                            (SELECT i.url FROM show_images si INNER JOIN images i ON i.id= si.image_id 
                                            WHERE si.show_id = s.id and i.image_type = "Logo" LIMIT 1) AS image_url, 
                                            (SELECT DATE_FORMAT(st.show_time, "%W, %M %D, %Y") FROM show_times st 
                                            WHERE st.show_id = s.id AND st.show_time > NOW() AND st.show_time < NOW()+INTERVAL '.$days.' DAY ORDER BY st.show_time LIMIT 1) AS show_time
                                            FROM shows s INNER JOIN venues v ON v.id = s.venue_id
                                            WHERE s.is_active = 1 AND s.created < (NOW() - INTERVAL '.$days.' DAY) AND s.venue_id = ?',array($ja->venue_id)); 

                    foreach ($this_week as $tw) $promo_venues[$ja->venue_id]['week'][] = $tw;
                }
                $promo_venues[$ja->venue_id]['announced'][] = $ja; 
                //advance progress bar
                $progressbar->advance();
            }
            //finish progress bar
            $progressbar->finish(); 
            
            $contacts = DB::select('SELECT DISTINCT u.email,CONCAT(u.first_name," ",u.last_name) AS name, GROUP_CONCAT(v.id) AS venues_id FROM users u 
                                    INNER JOIN purchases p ON u.id= p.user_id 
                                    INNER JOIN show_times st ON st.id = p.show_time_id 
                                    INNER JOIN shows s ON s.id = st.show_id
                                    INNER JOIN venues v ON s.venue_id = v.id
                                    WHERE v.enable_weekly_promos = 1
                                    GROUP BY u.email ');
            //create progress bar
            $progressbar = $this->output->createProgressBar(count($contacts));     
            foreach ($contacts as $c) 
            {   
                $promos = [];
                $promo = array_intersect(explode(",", $c->venues_id), array_keys($promo_venues));
                foreach ($promo as $key => $v) $promos = array_merge($promos, $promo_venues[$v]); 
                if(count($promos))
                {
                    $format = 'announced';
                    $view_announced = View::make('command.promo_announced', compact('promos','format'));
                    $html_announced = $view_announced->render(); 
                    
                    $format = 'week';
                    $view_week = View::make('command.promo_announced', compact('promos','format'));
                    $html_week = $view_week->render(); 
                   
                    if($html_announced || $html_week)
                    {
                        $email = new EmailSG(env('MAIL_PROMO_FROM'),$c->email,env('MAIL_PROMO_SUBJECT'));
                        //$email->cc(env('MAIL_PROMO_CC'));
                        $email->body('promos_announced',array('announced'=>$html_announced,'week'=>$html_week));
                        $email->category('Promotion');
                        $email->template('5f35738f-46a4-419a-a9f0-8d5318de7e7f');
                        $email->send(); 
                    }    
                } 
                //advance progress bar
                $progressbar->advance();
            }
            //finish progress bar
            $progressbar->finish(); 
            return true;
        } catch (Exception $ex) {
            throw new Exception('Error sending promos with PromoAnnounced: '.$ex->getMessage());
        }
    }
}
