<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Location class
 *
 * @author ivan
 */
class Location extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'locations';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the customers for the location.
     */
    public function customers()
    {
        return $this->hasMany('App\Http\Models\Customer','location_id');
    }
    /**
     * Get the users for the location.
     */
    public function users()
    {
        return $this->hasMany('App\Http\Models\User','location_id');
    }
    /**
     * Get the venues for the location.
     */
    public function venues()
    {
        return $this->hasMany('App\Http\Models\Venue','location_id');
    }
    //PERSONALIZED
    /**
     * Get the geocode from an address.
     */
    public static function geocode($address)
    {
        $url = sprintf("https://maps.googleapis.com/maps/api/geocode/json?address=%s&key=AIzaSyCtdQ-loglt2UYnHVkJX7fmFnxeph5YRGk", urlencode($address));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $geoloc = json_decode(curl_exec($ch), true);
        if(count($geoloc) && isset($geoloc['results']) && isset($geoloc['results'][0]))
            return $geoloc['results'][0]['geometry']['location'];
        return array();
    }
    /**
     * Set lng and lat for the current location.
     */
    public function set_lng_lat()
    {
        $address = $this->address.' '.$this->city.' '.$this->state.' '.$this->country.' '.$this->zip;
        $geocode = Location::geocode($address);
        if(count($geocode))
        {
            $this->lng = $geocode['lng'];
            $this->lat = $geocode['lat'];
        }
        else $this->lng = $this->lat = 0;
    }
}
