<?php


class BusinessEntity extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'BusinessEntity';
    
    protected $primaryKey = 'BusinessEntityID';
    
    protected $fillable=[];
    
    public static $rules=[
        'name'=>'required',
        
        ];
    
    
}