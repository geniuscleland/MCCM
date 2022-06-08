<?php

// namespace App;

use Illuminate\Database\Eloquent\Model;

class permission_role extends model
{
    protected $table = 'permission_role'; 
    
    public function permission()
    {
        return $this->hasMany('Permission');
    }
}
