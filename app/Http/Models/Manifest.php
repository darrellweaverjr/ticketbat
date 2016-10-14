<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Manifest class
 *
 * @author ivan
 */
class Manifest extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'manifest_emails';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the show_time record associated with the manifest.
     */
    public function show_time()
    {
        return $this->belongsTo('App\Http\Models\ShowTime','show_time_id');
    }
}
