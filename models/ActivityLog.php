<?php


class ActivityLog extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activity_logs';
    
    protected $primaryKey = 'DataID';
    
    
    protected $fillable = ['BusinessEntityID','content_type','content_id','action','description','details','data','language_key','public','developer','ip_address','user_agent'];
    
    public static $rules=[
        'BusinessEntityID'=>'required',        
        ];
    
    
}