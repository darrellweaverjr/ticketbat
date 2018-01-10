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
    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo('App\Http\Models\Category', 'id_parent');
    }
    /**
     * Get the children category.
     */
    public function children()
    {
        return $this->hasMany('App\Http\Models\Category','id_parent','id');
    }
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
    //PERSONALIZED FUNCTIONS
    /**
     * Get the purchase receipt info.
     */
    public static function get_categories($concat = '-')
    {
        $categories = [];
        $concat = $concat.'&emsp;';
        $level = 0;
        $cats = Category::orderBy('name')->get();
        
        function subs($cat, $l, $concat)
        {
            $cat->name = str_repeat($concat, ++$l).$cat->name;
            $cat_subs = [];
            $children = $cat->children()->orderBy('name')->get();
            foreach ($children as $c)
                $cat_subs = array_merge($cat_subs, subs($c, $l, $concat)); 
            return array_merge([$cat], $cat_subs);  
        }
        
        foreach ($cats as $c)
            if($c->id_parent == $level)
                $categories = array_merge($categories, subs($c, $level, $concat)); 

        return $categories;
    }
}
