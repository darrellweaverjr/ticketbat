<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Deal class
 *
 * @author ivan
 */
class ShowContract extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'show_contracts';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //PERSONALIZED FUNCTIONS
    public function set_file($file)
    {
        if($this->file != '')
            Util::remove_file ($this->agreement);
        $this->file = Util::upload_file ($file,'contracts');
    }
}
