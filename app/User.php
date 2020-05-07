<?php

namespace App;

use App\Transformers\UserTransformer;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    // transformer linking to the model
    public $transformer = UserTransformer::class;

    const VERIFIED_USER    = '1';
    const UNVERIFIED_USER  = '0';

    const ADMIN_USER       = 'true';
    const REGULAR_USER     = 'false';

    protected $table = 'users';
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'email', 
        'password',
        'verified',
        'verification_token',
        'admin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'verification_token',
        'remember_token',
    ];
    /*
        3:30 PM 1-2-2018
        => if you want to hide some filled into the eloquent output of a paticular model just add this one
    */

    /*
        Mutators for the name
        set[Name_of_Field]Attibute => mutator syntax
    */
    public function setNameAttribute($name)
    {
        $this->attributes['name'] = strtolower($name);
    }

    /*
        Accessor for the name 
        get[Name_of_Field]Attribute => accessors syntax
    */
    public function getNameAttribute($name)
    {
        return ucwords($name);
    }

    /*
        Mutators for the email
        set[Name_of_Field]Attibute => mutator syntax
    */
    public function setEmailAttribute($email)
    {
        $this->attributes['email'] = strtolower($email);
    }

    /*
        Checking if the particular object instance was verified 
    */
    public function isVerified()
    {
        return $this->verified == User::VERIFIED_USER;
    }

    /*
        Checking if the user was an admin or not
    */
    public function isAdmin()
    {
        return $this->verified == User::ADMIN_USER;
    }    

    /*
        Generating some VerificationToken Indeed
    */
    public static function generateVerificationCode()
    {
        return str_random(40);
    }
}
