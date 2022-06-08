<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class role_user extends Model
{
    public function role()
    {
        return $this->hasMany('App\models\Role');
    }
    public function user()
    {
        return $this->hasMany('App\models\User');
    }
    protected $fillable = ['user_id', 'role_id'];
}
