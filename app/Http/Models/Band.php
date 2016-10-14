<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Band class
 *
 * @author ivan
 */
class Band extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bands';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the category record associated with the band.
     */
    public function category()
    {
        return $this->belongsTo('App\Http\Models\Category','category_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * The show_bands that belong to the band.
     */
    public function show_bands()
    {
        return $this->belongsToMany('App\Http\Models\Show','show_bands','band_id','show_id');
    }
}
