<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Category class
 *
 * @author ivan
 */
class Category extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the bands for the category.
     */
    public function bands()
    {
        return $this->hasMany('App\Http\Models\Band','category_id');
    }
    /**
     * Get the shows for the category.
     */
    public function shows()
    {
        return $this->hasMany('App\Http\Models\Show','category_id');
    }
}
