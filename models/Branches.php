<?php


class Branches extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'Branches';
    
    protected $primaryKey = 'BusinessEntityID';
    
    public static $rules=[
        'BranchName'=>'required',
        
        ];
    
    
}