<?php

// namespace App;


use Nicolaslopezj\Searchable\SearchableTrait;
use Zizaco\Entrust\EntrustPermission;
class Permission extends EntrustPermission
{
    use SearchableTrait;
    protected $table = 'permissions';
    
   

    protected $searchable = [
        'columns' => [
            'permissions.name' => 100,
            'permissions.display_name' => 100,
        ],
    ];
    public function permission_role()
    {
        return $this->belongsToMany('App\permission_role');
    }

    protected $fillable = ['name','display_name'];
}
