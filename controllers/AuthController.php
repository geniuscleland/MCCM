<?php

use Carbon\Carbon;

class AuthController extends BaseController {
    protected $redirectTo = 'admin/dashboard';
    protected $userstatus = "user";
    protected $adminrole = "administrator";
 
  
    public function login() { 
        
        return View::make('auth.login');
    }
    
   

    public function loginpost() {
        $rules = array(
            'username' => 'required|string|exists:users',
            // 'password' => 'required|min:6|max:250|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/'
            'password' => 'required|string'
        );

        $validator = Validator::make($data = Input::all(), $rules);
       
        if ($validator->fails()) {
            ActivityLog::create(array(
                'content_id'   => 1,
                'description' => 'Login Attempt failed',
                'details' => 'username : '.$data['username'],
                'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
                'action'      => 'Login',
                'created_at'  => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ));
           
            if (Request::ajax()) {
                return $validator->messages()->first();
            }

            return Redirect::back()->withErrors($validator)->withInput()->with('message', $validator->messages()->first());
        }

        $rememberme = (Input::has('remember_me')) ? TRUE : FALSE;


      
        if(Auth::attempt(['username' =>Input::get('username'),'password' => Input::get('password')],$rememberme))
            {

           

            Auth::user()->LastLogin=date("Y-m-d H:i:s");
            Auth::user()->save();

            $business_entity_id = Auth::user()->BusinessEntityID;

            ActivityLog::create(array(
                'BusinessEntityID' => $business_entity_id,
                'content_id'   => 1,
                'description' => 'Login Successful',
                'details' => 'username : '.$data['username'],
                'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
                'action'      => 'Login',
                'created_at'  => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ));

            $academic_year = DB::table('AcademicYears')
            ->where('iActive', 1)->first();

            if($academic_year){
                Session::put('academic_year', $academic_year);
            }

           
                   
            if (trim(Auth::user()->user_type) == 'nmc') {
            
                return Redirect::to('nmc/dashboard');

           
            }
            if (trim(Auth::user()->user_type) == 'college') {
               


                return Redirect::intended('/institution/dashboard');
            }

            if (trim(Auth::user()->user_type) == 'branch') {
                $branch = Branches::where('BusinessEntityID', $business_entity_id)->first();

               

               if ($branch){              
                Session::put('branch', $branch);
               }
                
      
                return Redirect::intended('/branch/dashboard');
            }

            if (trim(Auth::user()->user_type) == 'church') {
           
                
      
                return Redirect::intended('/mccm/dashboard');
            }
            if (trim(Auth::user()->user_type) == 'stud') {

                $voucher = Voucher::where('StudentEntityID',Auth::user()->BusinessEntityID)->first();
             


                $getIndexDate  =  DB::table('IndexingDaysMonitor')->first();

                $daysleft = DB::select( DB::raw("SELECT * FROM dbo.fnIndexingDaysMonitor('$getIndexDate->StartDate',$getIndexDate->DurationInMonths,$getIndexDate->ExtentionInDays)"));

                $currentdate = date('Y-m-d');

                 $currentTime = date('Y-m-d H:i:s');


                if($voucher){
                    Session::put('voucher_state', $voucher->State);
                }
                


                if($currentTime >= '2021-12-14 17:00:00')
                 {
                     Session::flush();
                     Auth::logout();
                     return Redirect::route('end_indexing');   
                 }  

                
                return Redirect::route('student.application');
            }
            
        }
        else{
            ActivityLog::create(array(
                'content_id'   => 1,
                'content_type' => 'Login Attempt',
                'description' => 'Login Attempt failed',
                'details' => 'username : '.$data['username'],
                'ip_address' => Illuminate\Support\Facades\Request::getClientIp(),
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent',
                'action'      => 'Login',
                'created_at'  => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ));
            DB::table('users')->where('username',$data['username'])->increment('failedpasswordattemptcount', 1, array('lastfailedpasswordattempt' => date("Y-m-d H:i:s")));
            return View::make('auth.login')->with('flag',1);
        }
        return Redirect::to('nmc/dashboard');
      
    }

    public function logout() {
        Session::flush();
        Auth::logout();
        return Redirect::route('auth.login');
    }

    public function forgot_password() {
        // dd(Session::all());
        return View::make('auth.forgot_password');
    }

    
   

    }
