<?php

class NMCCandidatesController extends BaseController {
    
   
  public function receipt(){
        
    // if(Auth::user()->hasRole("SuperUser")){
     
 $employees = DB::table('Receipts')
      ->orderBy('StartDate','desc')
                ->get();
      

        ActivityLog::create(array(
            'BusinessEntityID' => Auth::user()->BusinessEntityID,
            'content_id'   => 1,
            'description' => 'Receipts page viewed',
            'details' => 'username : '.Auth::user()->username,
            'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
            'action'      => 'view',
            'created_at'  => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ));

       
            return View::make('branch.receipts.receipts')
             ->with('employees',$employees);
      
    // }else{
    //     return Response::json('404');
    // }
}

public function changeReceipt($dataid=NULL){

    $officer_id = $dataid ;

   

    $branchID = Auth::user()->BusinessEntityID;

    $branchName = null; 

    $fundTypeName = null ;

    $branchName = DB::table('Branches')
    ->select('BranchName')
    ->where ('BusinessEntityID',$branchID)
    ->pluck('BranchName');

    $data = Session::get('selected_dates');

    $the_selected_dates= null;

    $fund_id =null;

    $receipt_version = null;


    if($data){
        
        // if (!empty($data['chosen_id'])){ $chosen_id = $data['chosen_id'];}
         if (!empty($data['p'])){$fund_id = $data['p'];}
        if (!empty($data['s'])){$the_selected_dates = $data['s'];}


   
    }else{

     $data =  Session::forget('selected_dates');

    }


    if($officer_id){



     $Photograph= DB::table('Receipts')
     ->select('ReceiptName')
     ->where ('DataID',$officer_id)
     ->pluck('ReceiptName');

     $AppointmentDate= DB::table('Receipts')
     ->select('StartDate')
     ->where ('DataID',$officer_id)
     ->pluck('StartDate');

     $EndOfServiceDate= DB::table('Receipts')
     ->select('EndDate')
     ->where ('DataID',$officer_id)
     ->pluck('EndDate');



    }else{


      $the_selected_date = implode(",", [$the_selected_dates]);

      $selected_fund_id = implode(",", [$fund_id]);
      
             
      $AppointmentDate = DB::select(DB::raw("SELECT TOP 1 splitdata FROM dbo.fnSplitString('$the_selected_date',',') ORDER BY splitdata ASC"));

     $EndOfServiceDate = DB::select(DB::raw("SELECT TOP 1 splitdata FROM dbo.fnSplitString('$the_selected_date',',') ORDER BY splitdata DESC"));

     $selected_fund_id_type = DB::select(DB::raw("SELECT TOP 1 splitdata FROM dbo.fnSplitString('$selected_fund_id',',')"));


     //dd($EndOfServiceDate);

     foreach($AppointmentDate as $AppointmentDate)
     {

      $AppointmentDate =$AppointmentDate;
     }
   

     foreach($EndOfServiceDate as $EndOfServiceDate)
     {

      $EndOfServiceDate =$EndOfServiceDate;
     }

     foreach($selected_fund_id_type as $selected_fund_id_type)
     {

      $selected_fund_id_type =$selected_fund_id_type;
     }

     $Photograph=null;

    
     if($the_selected_date && $selected_fund_id)
     {

      $fundTypeName = DB::table('FundType')
      ->select('Description')
      ->where ('DataId',$selected_fund_id_type->splitdata)
      ->pluck('Description');
  
      $receipt_version = DB::table('Receipts')
      ->select('ReceiptVersion')
      ->where ('StartDate',$AppointmentDate->splitdata)
      ->where ('EndDate',$EndOfServiceDate->splitdata)
      ->where ('FundTypeID',$selected_fund_id_type->splitdata)
      ->where ('BranchID',$branchID)
      ->orderBy('ReceiptVersion', "DESC")
      ->pluck('ReceiptVersion');

     }

    

    //dd($receipt_version);

    if($receipt_version)
    {
      $receipt_version = $receipt_version + 1;
    } 

    else
    {

      $receipt_version = 1;
    }


    }
    
     return View::make('branch.receipts.load_receipts')
         
      ->with('Photograph', $Photograph)
      ->with('selected_fund_id_type', $selected_fund_id_type)
        ->with('officer_id', $officer_id)
       ->with('AppointmentDate', $AppointmentDate)
            ->with('EndOfServiceDate', $EndOfServiceDate)
           ->with('branchID', $branchID)
           ->with('fundTypeName', $fundTypeName)
           ->with('branchName', $branchName)
           ->with('receipt_version', $receipt_version);
          
  }

 
  public function check_date_duplicate()
  {
    $data=Input::all();

    $branchID = Auth::user()->BusinessEntityID;

    $date_already_chosen = 0;

    $start_date = $data["start_date"];
    $to = $data["to"];

    $officer_data_id = $data["data_id"];

    $selected_fund_id_type = $data["selected_fund_id_type"];

    if(!$officer_data_id){
     

       $check_date = DB::select(DB::raw("SELECT found FROM dbo.fncheckExist($branchID,'$start_date','$to',$selected_fund_id_type)"));

       foreach($check_date as $check_date)
       {
  
        $check_date =$check_date;
       }
  
     
         if($check_date->found){
          $date_already_chosen = $check_date->found;
        }


  
      return Response::json($date_already_chosen);
  
    }
    else
    {

      $check_date = DB::table('Receipts')
      ->select('DataID')
    ->where('StartDate', $start_date)
    ->where('EndDate', $to)
    ->where('BranchID',$branchID)
    ->where('FundTypeID',$selected_fund_id_type)
       ->pluck('DataID');

       if($check_date)
       {

        if($check_date != $officer_data_id)
        {
         $date_already_chosen = 1;
        }

       }

       

       return Response::json($date_already_chosen);

    }
    

    


  }

  public function check_week()
  {
    $data=Input::all();

    $branchID = Auth::user()->BusinessEntityID;

    $date_already_chosen = 0;

    $start_date = $data["start_date"];
    $to = $data["to"];

    

    $selected_fund_id_type = $data["selected_fund_id_type"];


         $check_week = DB::select(DB::raw("SELECT found FROM dbo.fncheckSomeDateExist($branchID,'$start_date','$to',$selected_fund_id_type)"));


     foreach($check_week as $check_week)
     {

      $check_week =$check_week;
     }

     //dd($check_week);


       if($check_week->found){
        $date_already_chosen = $check_week->found;
      }
  
      return Response::json($date_already_chosen);
  
   

  }

  public function delete_unsuccessful_upload()
  {
    $data=Input::all();

    $branchID = Auth::user()->BusinessEntityID;

    $undo_update_id = 0;

    $start_date = $data["start_date"];
    $to = $data["to"];

 
    $selected_fund_id_type = $data["selected_fund_id_type"];


      $delete_record = DB::table('Receipts')
       ->where('StartDate', $start_date)
    ->where('EndDate', $to)
    ->where('BranchID',$branchID)
    ->where('FundTypeID',$selected_fund_id_type)
    ->delete(); 


    DB::table('Giving')
    ->whereBetween('RecordedDate', [$start_date, $to])
    ->where('BranchID',$branchID)
    ->where('FundTypeID',$selected_fund_id_type)
->update(array('isuploaded'=> $undo_update_id,'updated_at'=> date("Y-m-d H:i:s") ));


      return Response::json($undo_update_id);
  
  }

  public function changeReceipt_store(){
    $updateStatus=null;

    $checkSuccess=null;

  

    $branchID = Auth::user()->BusinessEntityID;

  $file = Input::file('student_imgurl');

  $data=Input::all();

   $start_date = $data["start_date"];
   $to = $data["to"];

 $officer_data_id = $data["data_id"];
        $status=1;
     
        $createdBy = $branchID ;

       $file_name = $data["file_name"];

       $selected_fund_id_type = $data["selected_fund_id_type"];

       $total = DB::select(DB::raw("SELECT total FROM dbo.fnCalculateTotal($branchID,'$start_date','$to',$selected_fund_id_type)"));

       foreach($total as $total)
       {
  
        $total =$total;


       }
       

   
  
    if(!$officer_data_id){

    

  

                    

                          $sql1 = "EXEC [dbo].[uspInsertInToReceipts] 
                      
                          @ReceiptName= '".$file_name."',
                          @BranchID= ".$branchID.",
                          @StartDate= '".$start_date."',
                          @EndDate='".$to."',
                        @Status = ".$status.",
                        @fundTypeID = ".$selected_fund_id_type.",
                        @Total = ".$total->total."
                        ";


                    $saveToDb = DB::update($sql1);

                    $check_exist = DB::table('Receipts')
                    ->select('DataID')
                  ->where('StartDate', $start_date)
                  ->where('EndDate', $to)
                  ->where('FundTypeID', $selected_fund_id_type)
                    ->pluck('DataID');
                    

                  if ($check_exist){
                  ActivityLog::create(array(
                    'BusinessEntityID' => Auth::user()->BusinessEntityID,
                    'content_id'   => 1,
                    'description' => 'Receipt StartDate '.$start_date.' EndDate '. $to.' fundID: '.$selected_fund_id_type,
                    'details' => 'username : '.Auth::user()->username,
                    'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
                    'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
                    'action'      => 'Receipt Saved',
                    'created_at'  => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                  ));

                  return Response::json($check_exist);

                  }

       
    }
    
    else{

        

       
       $sql1 = "EXEC [dbo].[uspEditUpdateReceipts] 
       @DataID = ".$officer_data_id.",
      @StartDate= '".$start_date."',
       @EndDate='".$to."',
      @ReceiptName = '".$file_name."' ";


  
  $saveToDb = DB::update($sql1);

  $check_exist = $officer_data_id;

 
  

if ($check_exist){
ActivityLog::create(array(
  'BusinessEntityID' => Auth::user()->BusinessEntityID,
  'content_id'   => 1,
  'description' => 'data id '.$officer_data_id.' edit by: '.$createdBy,
  'details' => 'username : '.Auth::user()->username,
  'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
  'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
  'action'      => 'Image Edit',
  'created_at'  => date("Y-m-d H:i:s"),
  'updated_at' => date("Y-m-d H:i:s")
));

return Response::json($check_exist);

}



    }

}

public function account_search() {
     
  $data = Input::all();


  Session::put('search_account', $data);





  if($data){
      if (!empty('fundtype_search')){$fundtype_search = $data['fundtype_search'];}
      
          if (!empty('start_date')){$start_date = $data['start_date'];}
          if (!empty('to')){$to = $data['to'];}
     }

     if($fundtype_search){
         if($start_date && $to){

           ActivityLog::create(array(
      'BusinessEntityID' => Auth::user()->BusinessEntityID,
      'content_id'   => 1,
      'description' => 'Search accountID : '  .$fundtype_search. ' begindate: ' . $start_date . '  enddate: '. $to,
      'details' => 'Account Searched',
      'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
      'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
      'action'      => 'Account Search',
      'created_at'  => date("Y-m-d H:i:s"),
      'updated_at' => date("Y-m-d H:i:s")
  ));

         }elseif(!$start_date && !$to){
          ActivityLog::create(array(
              'BusinessEntityID' => Auth::user()->BusinessEntityID,
              'content_id'   => 1,
              'description' => 'Search accountID : '  .$fundtype_search,
              'details' => 'Account Searched',
              'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
              'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
              'action'      => 'Account Search',
              'created_at'  => date("Y-m-d H:i:s"),
              'updated_at' => date("Y-m-d H:i:s")
          ));

         }


     }
  

  return Redirect::route('mccm.branch.account');
  
}

public function account(){
  $id = NULL;

  $activeStatus = [NULL => NULL, 0 => 'Inactive', 1 => 'Active'];

  $fundtypes = [NULL => NULL] + DB::table('FundType')->where('Active', 1)->orderBy('Description')->lists('Description', 'DataId');

  $branchID = Auth::user()->BusinessEntityID;

  
  $check_user_id = DB::table('Branches')
  ->select('BusinessEntityID')
->where('BusinessEntityID', $branchID)
->pluck('BusinessEntityID');

if($check_user_id)
{
  $branchID = $check_user_id;
}
else{

  $check_user_id = DB::table('group_users')
  ->select('branch_id')
->where('user_id', $branchID)
->pluck('branch_id');

if($check_user_id)
{
  $branchID = $check_user_id; 
}
else{

  $branchID = null;


}

  
}

  $fundtype_search =  NULL;

  $start_date = NULL;

  $to = NULL;

 if (Input::has('id')){
   $id = Input::get('id');
 }

         $data = Session::get('search_account');

         if($data){
              if (!empty('fundtype_search')){$fundtype_search = $data['fundtype_search'];}
      
              if (!empty('start_date')){$start_date = $data['start_date'];}
              if (!empty('to')){$to = $data['to'];}
            }

            

 if(!$id)
 {

  if ($data) {

    // $default = ini_get('max_execution_time');
    // ini_set('memory_limit', '300M');
    // ini_set('max_execution_time', 3600);

            if($fundtype_search){



                if($start_date && $to){
                   

                    $giving = DB::table('Giving')
                    ->leftJoin('FundType', 'Giving.FundTypeID', '=', 'FundType.DataId')
                    ->select('Giving.DataID','Giving.iActive','isuploaded','RecordedDate','FundTypeID','Amount','Description')
                        ->where('Giving.BranchID', $branchID)
                        ->where('Giving.iActive', 1)
                        ->whereBetween('RecordedDate', [$start_date, $to])
                       ->where('Giving.FundTypeID',$fundtype_search)
                         ->orderBy('RecordedDate', "DESC")
                            ->get();

                   
                    
                   
                }
                else
                {
                  $giving = DB::table('Giving')
                  ->leftJoin('FundType', 'Giving.FundTypeID', '=', 'FundType.DataId')
                  ->select('Giving.DataID','Giving.iActive','isuploaded','RecordedDate','FundTypeID','Amount','Description')
                      ->where('Giving.BranchID', $branchID)
                      ->where('Giving.iActive', 1)
                ->where('Giving.FundTypeID',$fundtype_search)
                       ->orderBy('RecordedDate', "DESC")
                          ->get();
                }

            }


  
   
        }
        else {

          $giving = NULL;

  //         $curr_year = date("Y-m-d", strtotime("-1 week"));

          

  //         $giving = DB::table('Giving')
  //  ->leftJoin('FundType', 'Giving.FundTypeID', '=', 'FundType.DataId')
  //  ->select('Giving.DataID','Giving.iActive','isuploaded','RecordedDate','FundTypeID','Amount','Description')
  //      ->where('BranchID', $branchID)
  //      ->where('iActive', 1)
  //      ->where('Giving.created_at', '>' ,$curr_year)
  //     ->orderBy('FundTypeID')
  //         ->orderBy('RecordedDate')
  //          ->get();


        }

   
}

else
{
    $giving = DB::table('Giving')
    ->select('Giving.DataID','Giving.iActive','RecordedDate','Amount','FundTypeID')
     ->where('DataID', $id)
     ->first();    
}   

if(!$id)
{
  return View::make('branch.receipts.load_account')
   ->with('activeStatus', $activeStatus)
   ->with('fundtypes', $fundtypes)
   ->with('start_date', $start_date)
   ->with('to', $to)
   ->with('fundtype_search', $fundtype_search)
   ->with('giving', $giving);
}
else
{
   return Response::json($giving);
}


}



public function account_store(){

  $data = Input::all();


  $branchID = Auth::user()->BusinessEntityID;

  $date_record = $data['date_record'];
  $fundtype = $data['fundtype'];
  $amount = $data['amount'];
  
  $check_user_id = DB::table('Branches')
  ->select('BusinessEntityID')
->where('BusinessEntityID', $branchID)
->pluck('BusinessEntityID');

if($check_user_id)
{
  $branchID = $check_user_id;
}
else{

  $check_user_id = DB::table('group_users')
  ->select('branch_id')
->where('user_id', $branchID)
->pluck('branch_id');

if($check_user_id)
{
  $branchID = $check_user_id; 
}
else{

  $branchID = 0;


}

  
}
  

  
      if (Input::has('entity_id')){
          $data_id = $data["entity_id"];

 
      }else{
          $data_id = NULL;
      }

     
     

      if($data_id){

        $an_update = DB::table('Giving')
        ->where('DataID',  $data_id)
        ->update(array('FundTypeID' => $fundtype,'RecordedDate' => $date_record,'Amount' => $amount,'updated_at'=>date("Y-m-d h:i:sa")));



      }else{

          $sql1 = "EXEC [dbo].[uspInsertInToGiving] 
      @BranchID= ".$branchID.",
      @FundTypeID= ".$fundtype.",
      @RecordedDate= '".$date_record."',
      @Amount= ".$amount." ";

      
     $saveToDb = DB::update($sql1);

      }
    
      

   
     $check_exist = DB::table('Giving')
     ->select('DataID')
     ->where('BranchID', $branchID)
     ->where('FundTypeID', $fundtype)
     ->where('RecordedDate', $date_record)
      ->pluck('DataID');
     

if ($check_exist){

  if($data_id){

      ActivityLog::create(array(
          'BusinessEntityID' => Auth::user()->BusinessEntityID,
          'content_id'   => 1,
          'description' => 'data_id '.$data_id,
          'details' => 'username : '.Auth::user()->username,
          'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
          'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
          'action'      => 'Money Record Update',
          'created_at'  => date("Y-m-d H:i:s"),
          'updated_at' => date("Y-m-d H:i:s")
      ));

  }
  else{

      ActivityLog::create(array(
          'BusinessEntityID' => Auth::user()->BusinessEntityID,
          'content_id'   => 1,
          'description' => 'record by: '.$branchID,
          'details' => 'username : '.Auth::user()->username,
          'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
          'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
          'action'      => 'Money Record Creation',
          'created_at'  => date("Y-m-d H:i:s"),
          'updated_at' => date("Y-m-d H:i:s")
      ));

  }


 return Response::json($check_exist);

}
    





}

public function check_entry_duplicate()
{
  $data=Input::all();

  $branchID = Auth::user()->BusinessEntityID;

  $check_user_id = DB::table('Branches')
  ->select('BusinessEntityID')
->where('BusinessEntityID', $branchID)
->pluck('BusinessEntityID');

if($check_user_id)
{
  $branchID = $check_user_id;
}
else{

  $check_user_id = DB::table('group_users')
  ->select('branch_id')
->where('user_id', $branchID)
->pluck('branch_id');

if($check_user_id)
{
  $branchID = $check_user_id; 
}
else{

  $branchID = 0;


}

  
}

  $date_already_chosen = 0;

 

  $fundtype = $data["fundtype"];
  $date_record = $data["date_record"];

  $officer_data_id = $data["entity_id"];

  if(!$officer_data_id){
    $check_date = DB::table('Giving')
    ->select('DataID')
  ->where('FundTypeID', $fundtype)
  ->where('RecordedDate', $date_record)
  ->where('BranchID',$branchID)
     ->pluck('DataID');

     if($check_date){
      $date_already_chosen = 1;
    }

    return Response::json($date_already_chosen);

  }
  else
  {

    $check_date = DB::table('Giving')
    ->select('DataID')
    ->where('FundTypeID', $fundtype)
    ->where('RecordedDate', $date_record)
    ->where('BranchID',$branchID)
     ->pluck('DataID');

     if($check_date)
     {

      if($check_date != $officer_data_id)
      {
       $date_already_chosen = 1;
      }

     }

     

     return Response::json($date_already_chosen);

  }
  


}

public function fetch_entry()
{
  $data=Input::all();

  $branchID = Auth::user()->BusinessEntityID;

 

 

  $chosen_id = $data["chosen_id"];

  $data_id = explode( ',', $chosen_id);

  $records = DB::table('Giving')
   ->leftJoin('FundType', 'Giving.FundTypeID', '=', 'FundType.DataId')
   ->select('Giving.DataID','RecordedDate','FundTypeID','Description')
   ->whereIn('Giving.DataID',$data_id)
     ->get();

  
  
  return Response::json($records);
 
  


}

public function seleted_dates_to_upload()
{
  $data=Input::all();

 // dd($data);

  Session::put('selected_dates', $data);

  return Response::json("success");
     
    //  return Redirect::route('mccm.branch.changeReceipt');

}



public function mccm_account(){
 
  $fundtypes = [NULL => NULL] + DB::table('FundType')->where('Active', 1)->orderBy('Description')->lists('Description', 'DataId');

  $branches = [NULL => NULL] + DB::table('Branches')->where('Active', 1)->orderBy('BranchName')->lists('BranchName', 'BusinessEntityID');

  $branchID = NULL;

  $fundtype_search =  NULL;

  $start_date = NULL;

  $to = NULL;

  $data_id =0;

 
         $data = Session::get('search_mccm_account');

         if($data){
              if (!empty('fundtype_search')){$fundtype_search = $data['fundtype_search'];}
              if (!empty('branch')){$branchID = $data['branch'];}
      
              if (!empty('start_date')){$start_date = $data['start_date'];}
              if (!empty('to')){$to = $data['to'];}
            }

            


  if ($data) {

    // $default = ini_get('max_execution_time');
    // ini_set('memory_limit', '300M');
    // ini_set('max_execution_time', 3600);

           

                if($start_date && $to){

                    if($branchID && $fundtype_search)
                    {

                      $giving = DB::select(DB::raw("SELECT * FROM dbo.uFnctFetchSubmittedReceipts($branchID,$fundtype_search,'$start_date','$to',$data_id)"));

                     
                    }
                    elseif(!$branchID && $fundtype_search)
                    {

                      $giving = DB::select(DB::raw("SELECT * FROM dbo.uFnctFetchSubmittedReceipts(NULL,$fundtype_search,'$start_date','$to',$data_id)"));

                     

                    }

                    elseif($branchID && !$fundtype_search)
                    {

                      $giving = DB::select(DB::raw("SELECT * FROM dbo.uFnctFetchSubmittedReceipts($branchID,NULL,'$start_date','$to',$data_id)"));

                     

                    }
                    else 
                    {
                      $giving = DB::select(DB::raw("SELECT * FROM dbo.uFnctFetchSubmittedReceipts(NULL,NULL,'$start_date','$to',$data_id)"));

                     

                    }
                   

                
                    
                   
                }
                
          

           
   
        }
        else {

          $data =  Session::forget('search_mccm_account');

          $giving = DB::select(DB::raw("SELECT * FROM dbo.uFnctFetchSubmittedReceipts(NULL,NULL,NULL,NULL,$data_id)"));


        }

   


  


  return View::make('mccm.receipts.receipts')

   ->with('fundtypes', $fundtypes)
   ->with('start_date', $start_date)
   ->with('to', $to)
   ->with('branches', $branches)
   ->with('branchID', $branchID)
   ->with('fundtype_search', $fundtype_search)
   ->with('giving', $giving);




}

//mccm_account_search

public function mccm_account_search() {
     
  $data = Input::all();


  Session::put('search_mccm_account', $data);





  if($data){
      if (!empty('fundtype_search')){$fundtype_search = $data['fundtype_search'];}
       if (!empty('branch')){$branch = $data['branch'];}
          if (!empty('start_date')){$start_date = $data['start_date'];}
          if (!empty('to')){$to = $data['to'];}
     }

     
  

  return Redirect::route('mccm.account');
  
}

public function view_receipt($id){
   
  $data_id = $id;

  $giving = DB::select(DB::raw("SELECT * FROM dbo.uFnctFetchSubmittedReceipts(NULL,NULL,NULL,NULL,$data_id)"));

  foreach($giving as $giving)
  {
    $giving = $giving;
  }

       $reasons = [NULL=>NULL] + DB::table('VetReceiptReasons')->where('iActive',1)->orderBy('Reason','asc')->lists('Reason', 'DataID');


      ActivityLog::create(array(
          'BusinessEntityID' => Auth::user()->BusinessEntityID,
          'content_id'   => 1,
          'description' => 'Receipt view',
          'details' => 'Receipt ID : '.$data_id,
          'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
          'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
          'action'      => 'View Receipt',
          'created_at'  => date("Y-m-d H:i:s"),
          'updated_at' => date("Y-m-d H:i:s")
      ));

      
      
          return View::make('mccm.receipts.load_receipts', compact('giving'))
                       ->with('reasons',$reasons) ;
    
      
 
}

public function fetch_reasons(){
  $id = NULL;
  if (Input::has('id')){
      $id = Input::get('id');
  }
  $reasons = DB::table('VetReceiptReasons')
      ->select('DataID','Reason')
      ->where('ApproveOrReject','=', $id)
      ->get();
  return Response::json($reasons);
}

public function receipt_approve(){
  // if(Auth::user()->can("action_confirmapproval_indexingapplicant")){
      $validator = Validator::make($data = Input::all(),["id" =>"required"]);

      if ($validator->fails()) {
          dd($validator->errors());
          return Redirect::back()->withErrors($validator)->withInput();
      }

      $id = $data['id'];

              $certificate = DB::table('Receipts')
              ->where('DataID', $id)
     ->update(array('Status' => $data['state'],'Reason'=>$data['reason'] =='' ? null: $data['reason']));

        

         return Response::json('saved');
        
         
      
  
  //}
  // else{
  //         return Response::json('404');
  // }
}

public function branch_account(){
 
  $fundtypes = [NULL => NULL] + DB::table('FundType')->where('Active', 1)->orderBy('Description')->lists('Description', 'DataId');
   $branchID = Auth::user()->BusinessEntityID;

  $fundtype_search =  NULL;

  $start_date = NULL;

  $to = NULL;

  $data_id =0;

 
         $data = Session::get('search_branch_account');

         if($data){
              if (!empty('fundtype_search')){$fundtype_search = $data['fundtype_search'];}
              if (!empty('start_date')){$start_date = $data['start_date'];}
              if (!empty('to')){$to = $data['to'];}
            }

            


  if ($data) {

    // $default = ini_get('max_execution_time');
    // ini_set('memory_limit', '300M');
    // ini_set('max_execution_time', 3600);

                if($start_date && $to){

                    if($branchID && $fundtype_search)
                    {

                      $giving = DB::select(DB::raw("SELECT * FROM dbo.uFnctFetchSubmittedReceipts($branchID,$fundtype_search,'$start_date','$to',$data_id)"));
 
                    }
                    

                    elseif($branchID && !$fundtype_search)
                    {

                      $giving = DB::select(DB::raw("SELECT * FROM dbo.uFnctFetchSubmittedReceipts($branchID,NULL,'$start_date','$to',$data_id)"));

                    }
                    else 
                    {
                      $giving = DB::select(DB::raw("SELECT * FROM dbo.uFnctFetchSubmittedReceipts(NULL,NULL,'$start_date','$to',$data_id)"));

                     

                    }
                  
                }
           
   
        }
        else {

          $data =  Session::forget('search_branch_account');

          $giving = DB::select(DB::raw("SELECT * FROM dbo.uFnctFetchSubmittedReceipts($branchID,NULL,NULL,NULL,$data_id)"));


        }

   
  return View::make('branch.uploaded_receipts.receipts')

   ->with('fundtypes', $fundtypes)
   ->with('start_date', $start_date)
   ->with('to', $to)
  ->with('branchID', $branchID)
   ->with('fundtype_search', $fundtype_search)
   ->with('giving', $giving);

}

public function branch_account_search() {
     
  $data = Input::all();

  Session::put('search_branch_account', $data);

  if($data){
      if (!empty('fundtype_search')){$fundtype_search = $data['fundtype_search'];}
       if (!empty('start_date')){$start_date = $data['start_date'];}
          if (!empty('to')){$to = $data['to'];}
     }

  return Redirect::route('branch.account');
  
}

public function branch_view_receipt($id){
   
  $data_id = $id;

  $giving = DB::select(DB::raw("SELECT * FROM dbo.uFnctFetchSubmittedReceipts(NULL,NULL,NULL,NULL,$data_id)"));

  foreach($giving as $giving)
  {
    $giving = $giving;
  }

  $branchID = Auth::user()->BusinessEntityID;


      ActivityLog::create(array(
          'BusinessEntityID' => Auth::user()->BusinessEntityID,
          'content_id'   => 1,
          'description' => 'branch Receipt view',
          'details' => 'Receipt ID : '.$data_id. 'Branch ID : '.$branchID ,
          'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
          'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
          'action'      => 'branch View Receipt',
          'created_at'  => date("Y-m-d H:i:s"),
          'updated_at' => date("Y-m-d H:i:s")
      ));

      
      
          return View::make('branch.uploaded_receipts.load_receipts', compact('giving')) ;
    
      
 
}

public function get_reasons(){
  $id = NULL;
  if (Input::has('id')){
      $id = Input::get('id');
      //dd($id);
  }
  $reasons = DB::table('Receipts')
      ->leftJoin('VetReceiptReasons', 'Receipts.Reason', '=', 'VetReceiptReasons.DataID')
      ->select('VetReceiptReasons.Reason')
      ->where('Receipts.DataID','=', $id)
      ->first();
  return Response::json($reasons);
}


public function account_entry(){
  $id = NULL;

  $activeStatus = [NULL => NULL, 0 => 'Inactive', 1 => 'Active'];

  $fundtypes = [NULL => NULL] + DB::table('FundType')->where('Active', 1)->orderBy('Description')->lists('Description', 'DataId');

  $fundtype_search =  NULL;

  $start_date = NULL;

  $to = NULL;

 if (Input::has('id')){
   $id = Input::get('id');
 }

         $data = Session::get('search_account_entry');

         if($data){
              if (!empty('fundtype_search')){$fundtype_search = $data['fundtype_search'];}
      
              if (!empty('start_date')){$start_date = $data['start_date'];}
              if (!empty('to')){$to = $data['to'];}
            }

            

 if(!$id)
 {

  if ($data) {

    // $default = ini_get('max_execution_time');
    // ini_set('memory_limit', '300M');
    // ini_set('max_execution_time', 3600);

            if($fundtype_search){



                if($start_date && $to){
                   

                    $giving = DB::table('HQGiving')
                    ->leftJoin('FundType', 'HQGiving.FundTypeID', '=', 'FundType.DataId')
                    ->select('HQGiving.DataID','HQGiving.iActive','RecordedDate','FundTypeID','Amount','Description')
                    ->where('HQGiving.iActive', 1)
                        ->whereBetween('RecordedDate', [$start_date, $to])
                       ->where('HQGiving.FundTypeID',$fundtype_search)
                         ->orderBy('RecordedDate', "DESC")
                            ->get();

                   
                    
                   
                }
                else
                {
                  $giving = DB::table('HQGiving')
                  ->leftJoin('FundType', 'HQGiving.FundTypeID', '=', 'FundType.DataId')
                  ->select('HQGiving.DataID','HQGiving.iActive','RecordedDate','FundTypeID','Amount','Description')
                ->where('HQGiving.iActive', 1)
                ->where('HQGiving.FundTypeID',$fundtype_search)
                       ->orderBy('RecordedDate', "DESC")
                          ->get();
                }

            }


  
   
        }
        else {

          $giving = NULL;

  //         $curr_year = date("Y-m-d", strtotime("-1 week"));

          

  //         $giving = DB::table('Giving')
  //  ->leftJoin('FundType', 'Giving.FundTypeID', '=', 'FundType.DataId')
  //  ->select('Giving.DataID','Giving.iActive','isuploaded','RecordedDate','FundTypeID','Amount','Description')
  //      ->where('BranchID', $branchID)
  //      ->where('iActive', 1)
  //      ->where('Giving.created_at', '>' ,$curr_year)
  //     ->orderBy('FundTypeID')
  //         ->orderBy('RecordedDate')
  //          ->get();


        }

   
}

else
{
    $giving = DB::table('HQGiving')
    ->select('HQGiving.DataID','HQGiving.iActive','RecordedDate','Amount','FundTypeID')
     ->where('DataID', $id)
     ->first();    
}   

if(!$id)
{
  return View::make('mccm.accounts.load_account')
   ->with('activeStatus', $activeStatus)
   ->with('fundtypes', $fundtypes)
   ->with('start_date', $start_date)
   ->with('to', $to)
   ->with('fundtype_search', $fundtype_search)
   ->with('giving', $giving);
}
else
{
   return Response::json($giving);
}


}

public function account_entry_store(){

  $data = Input::all();


  $branchID = 0;

  $date_record = $data['date_record'];
  $fundtype = $data['fundtype'];
  $amount = $data['amount'];
  
 
  

  
      if (Input::has('entity_id')){
          $data_id = $data["entity_id"];

 
      }else{
          $data_id = NULL;
      }

     
     

      if($data_id){

        $an_update = DB::table('HQGiving')
        ->where('DataID',  $data_id)
        ->update(array('FundTypeID' => $fundtype,'RecordedDate' => $date_record,'Amount' => $amount,'updated_at'=>date("Y-m-d h:i:sa")));



      }else{

          $sql1 = "EXEC [dbo].[uspInsertInToGiving] 
      @BranchID= ".$branchID.",
      @FundTypeID= ".$fundtype.",
      @RecordedDate= '".$date_record."',
      @Amount= ".$amount." ";

      
     $saveToDb = DB::update($sql1);

      }
    
      

   
     $check_exist = DB::table('HQGiving')
     ->select('DataID')
   ->where('FundTypeID', $fundtype)
     ->where('RecordedDate', $date_record)
      ->pluck('DataID');
     

if ($check_exist){

  if($data_id){

      ActivityLog::create(array(
          'BusinessEntityID' => Auth::user()->BusinessEntityID,
          'content_id'   => 1,
          'description' => 'data_id '.$data_id,
          'details' => 'username : '.Auth::user()->username,
          'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
          'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
          'action'      => 'Money Record Update',
          'created_at'  => date("Y-m-d H:i:s"),
          'updated_at' => date("Y-m-d H:i:s")
      ));

  }
  else{

      ActivityLog::create(array(
          'BusinessEntityID' => Auth::user()->BusinessEntityID,
          'content_id'   => 1,
          'description' => 'record by: '.Auth::user()->BusinessEntityID,
          'details' => 'username : '.Auth::user()->username,
          'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
          'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
          'action'      => 'Money Record Creation',
          'created_at'  => date("Y-m-d H:i:s"),
          'updated_at' => date("Y-m-d H:i:s")
      ));

  }


 return Response::json($check_exist);

}
    

}

public function account_entry_search() {
     
  $data = Input::all();


  Session::put('search_account_entry', $data);





  if($data){
      if (!empty('fundtype_search')){$fundtype_search = $data['fundtype_search'];}
      
          if (!empty('start_date')){$start_date = $data['start_date'];}
          if (!empty('to')){$to = $data['to'];}
     }

     if($fundtype_search){
         if($start_date && $to){

           ActivityLog::create(array(
      'BusinessEntityID' => Auth::user()->BusinessEntityID,
      'content_id'   => 1,
      'description' => 'Search accountID : '  .$fundtype_search. ' begindate: ' . $start_date . '  enddate: '. $to,
      'details' => 'Account Searched',
      'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
      'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
      'action'      => 'Account Search',
      'created_at'  => date("Y-m-d H:i:s"),
      'updated_at' => date("Y-m-d H:i:s")
  ));

         }elseif(!$start_date && !$to){
          ActivityLog::create(array(
              'BusinessEntityID' => Auth::user()->BusinessEntityID,
              'content_id'   => 1,
              'description' => 'Search accountID : '  .$fundtype_search,
              'details' => 'Account Searched',
              'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
              'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
              'action'      => 'Account Search',
              'created_at'  => date("Y-m-d H:i:s"),
              'updated_at' => date("Y-m-d H:i:s")
          ));

         }


     }
  

  return Redirect::route('mccm.account.entry');
  
}

public function check_hq_entry_duplicate()
{
  $data=Input::all();

 
  $date_already_chosen = 0;

 
  $fundtype = $data["fundtype"];
  $date_record = $data["date_record"];

  $officer_data_id = $data["entity_id"];

  if(!$officer_data_id){
    $check_date = DB::table('HQGiving')
    ->select('DataID')
  ->where('FundTypeID', $fundtype)
  ->where('RecordedDate', $date_record)
  ->pluck('DataID');

     if($check_date){
      $date_already_chosen = 1;
    }

    return Response::json($date_already_chosen);

  }
  else
  {

    $check_date = DB::table('HQGiving')
    ->select('DataID')
    ->where('FundTypeID', $fundtype)
    ->where('RecordedDate', $date_record)
    ->pluck('DataID');

     if($check_date)
     {

      if($check_date != $officer_data_id)
      {
       $date_already_chosen = 1;
      }

     }

     

     return Response::json($date_already_chosen);

  }
  


}


}
