<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Band class
 *
 * @author ivan
 */
class RestaurantMenu extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'restaurant_menu';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Get the parent Menu.
     */
    public function parent()
    {
        return $this->belongsTo('App\Http\Models\RestaurantMenu', 'parent_id');
    }
    /**
     * Get the children menu.
     */
    public function children()
    {
        return $this->hasMany('App\Http\Models\RestaurantMenu','parent_id','id');
    }
    //RELATIONSHIPS ONE-MANY
    //RELATIONSHIPS MANY-MANY
    //PERSONALIZED FUNCTIONS
    /**
     * Get the purchase receipt info.
     */
    public static function get_menu($concat = '-')
    {
        $menus = [];
        $concat = $concat.'&emsp;';
        $level = 0;
        $menu = RestaurantMenu::orderBy('name')->get();
        
        function subs($me, $l, $concat)
        {
            $me->name = str_repeat($concat, ++$l).$me->name;
            $me_subs = [];
            $children = $me->children()->orderBy('name')->get();
            foreach ($children as $m)
                $me_subs = array_merge($me_subs, subs($m, $l, $concat)); 
            return array_merge([$me], $me_subs);  
        }
        
        foreach ($menu as $m)
            if($m->parent_id == $level)
                $menus = array_merge($menus, subs($m, $level, $concat)); 

        return $menus;
    }
}
