<?php

namespace App\Http\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Mail\EmailSG;

/**
 * Users class
 *
 * @author ivan
 */
class User extends Authenticatable
{
    protected $fillable = [
        'email', 'password',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password'];
    /**
     * Encrypt the password before check md5/bcrypt.
     *
     * @return password
     */
    public function getAuthPassword()
    {
        return bcrypt($this->password);
    }
    /**
     * Overrides the method to ignore the remember token.
    */
    public function setAttribute($key, $value)
    {
        $isRememberTokenAttribute = $key == $this->getRememberTokenName();
        if (!$isRememberTokenAttribute)
        {
          parent::setAttribute($key, $value);
        }
    }
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the user_type record associated with the user.
     */
    public function user_type()
    {
        return $this->belongsTo('App\Http\Models\UserType','user_type_id');
    }
    /**
     * Get the location record associated with the user.
     */
    public function location()
    {
        return $this->belongsTo('App\Http\Models\Location','location_id');
    }
    /**
     * Get the purchases for the user.
     */
    public function purchases()
    {
        return $this->hasMany('App\Http\Models\Purchase','user_id');
    }
    /**
     * Get the transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany('App\Http\Models\Transaction','user_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * The user_discounts that belong to the user.
     */
    public function user_discounts()
    {
        return $this->belongsToMany('App\Http\Models\Discount','user_discounts','user_id','discount_id');
    }
    /**
     * The user_images that belong to the user.
     */
    public function user_images()
    {
        return $this->belongsToMany('App\Http\Models\Image','user_images','user_id','image_id');
    }
    //PERSONALIZED
    /**
     * Set a random passoword for current user.
     */
    public function set_password($new_password=null)
    {
        if(!$new_password)
        {
            $length = 10;
            $new_password = substr(bcrypt(bin2hex(uniqid())),-1*$length);
            $this->slug = $new_password;
        }
        $this->password = md5($new_password);
    }
    /**
     * Set the slug for the current user.
     */
    public function set_slug()
    {
        $this->slug = preg_replace('/[^a-z0-9\-]/', '', strtolower(str_replace(' ','-',$this->first_name.'-'.$this->last_name)));
    }
    //PERSONALIZED FUNCTIONS
    /*
     * send welcome email
     */
    public function update_customer($customer=null)
    {
        try {
            $current = date('Y-m-d H:i:s');
            //get customer
            $customer = (!empty($customer))? $customer : Customer::where('email',$this->email)->first();
            if(!$customer)
            {
                $customer = new Customer;
                $customer->email = $this->email;
                $location = new Location;
                $location->created = $current;
            }
            else
            {
                $location = $customer->location;
            }
            //update location
            $location->address = $this->location->address;
            $location->city = $this->location->city;
            $location->state = $this->location->state;
            $location->zip = $this->location->zip;
            $location->country = $this->location->country;
            $location->lng = $this->location->lng;
            $location->lat = $this->location->lat;
            $location->updated = $current;
            $location->save();
            //update customer
            $customer->location()->associate($location);
            $customer->first_name = $this->first_name;
            $customer->last_name = $this->last_name;
            $customer->email = $this->email;
            $customer->phone = $this->phone;
            $customer->updated =$current;
            $customer->save();
            return $customer->id;
        } catch (Exception $ex) {
            return false;
        }
    }
    /*
     * send welcome email
     */
    public function welcome_email($first_purchase=false)
    {
        try {
            //send email
            $email = new EmailSG(null,$this->email,'TicketBat Team - Welcome to TicketBat!');
            $email->category('Primary');
            $email->body('welcome',['username'=>$this->email,'password'=> $this->slug,'first_purchase'=>$first_purchase]);
            $email->template('a7b5c451-4d26-4292-97cd-239880e7dd20');
            return $email->send();
        } catch (Exception $ex) {
            return false;
        }
    }
    /*
     * set new user when purchase
     */
    public static function customer_set($info,$current)
    {
        try {
            //init set
            $send_welcome_email = false;
            //if it is a seller dont update the users table and no customers one
            if(Auth::check() && in_array(Auth::user()->user_type_id,explode(',',env('POS_OPTION_USER_TYPE'))))
            {
                //get customer
                $customer = Customer::where('email',trim($info['email']))->first();
                if(!$customer)
                {
                    $customer = new Customer;
                    $customer->email = trim($info['email']);
                    $location = new Location;
                    $location->address = Auth::user()->location->address;
                    $location->city = Auth::user()->location->city;
                    $location->state = Auth::user()->location->state;
                    $location->zip = Auth::user()->location->zip;
                    $location->country = Auth::user()->location->country;
                    $location->lng = Auth::user()->location->lng;
                    $location->lat = Auth::user()->location->lat;
                    $location->created = $current;
                    $location->updated = $current;
                    $location->save();
                    $customer->location()->associate($location);
                    $customer->first_name = Auth::user()->first_name;
                    $customer->last_name = Auth::user()->last_name;
                    $customer->phone = Auth::user()->phone;
                    $customer->save();
                }
                if(empty($customer->id))
                    return ['success'=>false, 'send_welcome_email'=>0, 'msg'=>'There is an error setting up the customer information.'];
                return ['success'=>true, 'send_welcome_email'=>0, 'user_id'=>Auth::user()->id, 'customer_id'=>$customer->id];
            }
            //if it is a admin dont update the users table, only the customers one
            else if(Auth::check() && in_array(Auth::user()->user_type_id,explode(',',env('SELLER_OPTION_USER_TYPE'))))
            {
                //get customer
                $customer = Customer::where('email',trim($info['email']))->first();
                if(!$customer)
                {
                    $customer = new Customer;
                    $customer->email = trim($info['email']);
                    $location = new Location;
                    $location->address = Auth::user()->location->address;
                    $location->city = Auth::user()->location->city;
                    $location->state = Auth::user()->location->state;
                    $location->zip = Auth::user()->location->zip;
                    $location->country = Auth::user()->location->country;
                    $location->lng = Auth::user()->location->lng;
                    $location->lat = Auth::user()->location->lat;
                    $location->created = $current;
                    $location->updated = $current;
                    $location->save();
                    $customer->location()->associate($location);
                }
                //create customer
                $customer->first_name = ucwords(trim($info['first_name']));
                $customer->last_name = ucwords(trim($info['last_name']));
                $customer->phone = (!empty($info['phone']))? $info['phone'] : null;
                $customer->save();
                if(empty($customer->id))
                    return ['success'=>false, 'send_welcome_email'=>0, 'msg'=>'There is an error setting up the customer information.'];
                return ['success'=>true, 'send_welcome_email'=>0, 'user_id'=>Auth::user()->id, 'customer_id'=>$customer->id];
            }
            else
            {
                //set up user and customer
                $user = User::where('email','=',trim($info['email']))->first();
                if(!$user)
                {
                    //send welcome email
                    $send_welcome_email = true;
                    //create user
                    $user = new User;
                    $user->user_type_id = 3;    //customer
                    $user->is_active = 1;
                    $user->force_password_reset = 0;
                    $user->email = trim($info['email']);
                    $user->audit_user_id = 2;   //website-account
                    $location = new Location;
                    $location->created = $current;
                }
                else
                    $location = $user->location;
                //save location
                if(!empty($info['address']) && !empty($info['city']) && !empty($info['region']) && !empty($info['zip']) && !empty($info['country']))
                {
                    $location->address = $info['address'];
                    $location->city = $info['city'];
                    $location->state = strtoupper($info['region']);
                    $location->zip = $info['zip'];
                    $location->country = $info['country'];
                    $location->set_lng_lat();
                }
                else
                {
                    $location->address =  $location->city = 'Unknown';
                    $location->state = 'NA';
                    $location->country = 'US';
                }
                $location->save();
                //save user
                $user->location()->associate($location);
                $user->first_name = ucwords(trim($info['first_name']));
                $user->last_name = ucwords(trim($info['last_name']));
                $user->phone = (!empty($info['phone']))? $info['phone'] : null;
                $user->save();
                //send email welcome
                if($send_welcome_email)
                    $send_welcome_email = ($user->welcome_email(true))? 1 : -1;
                else
                    $send_welcome_email = 0;
                //erase temp pass
                $user->set_slug();
                //get customer
                $customer_id = $user->update_customer();
                if(!$customer_id)
                    return ['success'=>false, 'send_welcome_email'=>$send_welcome_email, 'msg'=>'There is an error setting up the customer information.'];
                return ['success'=>true, 'send_welcome_email'=>$send_welcome_email, 'user_id'=>$user->id, 'customer_id'=>$customer_id];
            }
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error setting up the customer information!'];
        }
    }
    
}
