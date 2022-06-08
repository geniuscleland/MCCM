<?php 
    if(!empty($flag) && $flag ==14){
        header('Refresh: 8; URL=/login');
    }
?>
@extends('_layouts.main_login')

@section('content')

<div >
    
    <div class="login_wrapper">
        
        <div   class="animate form login_form shadow">
            <div class="row">
                <div class="col-sm-5"  style="max-width: 200px;">
                    <img src="{{ asset('images/MCCM_LOGO.jfif') }}"alt="nmc_logo" class="img-circle profile_img img-responsive">

                    
                </div>
                            
                <div class="col-sm-7">
                <center>   <h1 class="app_name" style="margin-top: 65px;" >MCCM
                    </h1></center>
                    <center><h4>Version 1.0.0.e</h4></center>
                </div>
            </div>
            <section style="margin-top: 40px;" class="login_content">
            
                
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/reset/email') }}">
                <h1>Reset Password</h1>

               
                <div class="col-sm-10 col-sm-offset-1">
                    @include('message')
                </div>
                @if(Session::has('controllermessage'))
                
                    <div class="alert alert-info alert-dismissible fade in"  role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                        </button>
                    <span> {{ Session::get('controllermessage') }} </span>
                    </div>
                @endif

                <div>
                    <input type="email" class="form-control" required name="email" id="email" placeholder="Enter Email">
                    <!-- {{Form::email('email',null,array('class'=>'form-control', 'placeholder'=>'Username','title'=>'Enter a username', 'required'=>'required'))}} -->
                </div>
                <div class="col-sm-10 col-sm-offset-2">
                    {{ Form::captcha() }}
                    <!-- <div class="g-recaptcha" data-sitekey="6LeeEjwUAAAAAOtXiXdmMuuhTVRNyVpPwhIuIRON"></div> -->
                </div>
                <div>
                    
                    <button class="btn btn-default submit">Send Password Reset Link</button>
                </div>
                <div  class="col-sm-5 col-sm-offset-3">
                    <a class="reset_pass" href="{{url("/login")}}">Go back to Login</a>
                </div>

                <div class="clearfix"></div>

                <div class="separator">
                    <div class="clearfix"></div>
                    <br />
                        <!-- <p>Licensed to</p>
                        <h1> Nursing and Midwifery Council</h1> -->
                        <p>©2021 All Rights Reserved.</p>

                    </div>


                </div>
                </form>
            </section>
        </div>



    </div>

        
    
</div>



@stop

@section('scripts')


@stop