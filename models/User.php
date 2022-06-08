<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Zizaco\Entrust\HasRole;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait,
		HasRole; // Add this trait to your user model
	

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'Users';
        
        protected $primaryKey = 'BusinessEntityID';

        
        public static $loginRules=[
        
        'username'=>'required',
        'password'=>'required',
    
        ];

 
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

}
