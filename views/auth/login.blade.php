@extends('_layouts.main_login')

@section('content')

<div >
    
    <div class="login_wrapper">
        
        <div   class="animate form login_form shadow">
            <div class="row">
                <div class="col-sm-5"  style="max-width: 200px;">
                    <img src="{{ asset('images/MCCM_LOGO.jfif') }}" alt="mccm_logo" class="img-circle profile_img img-responsive">

                    
                </div>
                            
                <div class="col-sm-7">
                <center>   <h1 class="app_name" style="margin-top: 65px;" >MCCM
                    </h1></center>
                    <center><h4>Version 1.0.0.e</h4></center>
                </div>
            </div>

            <section style="margin-top: 40px;" class="login_content">
                
                {{Form::open(array('route'=>'auth.login'))  }}
                <h1>Login</h1>

                @include('message')
                @if(Session::has('message'))
                    <div class="alert alert-info alert-dismissible fade in"  role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                        </button>
                    <span> {{ Session::get('message') }} </span>
                    </div>
                @endif

                <div>
                    {{ 
                            Form::text('username',null,array(
                                                            'class'=>'form-control', 'placeholder'=>'Username',
                                                            'title'=>'Enter a username', 'required'=>'required'
                                                         ))
                    }}
                </div>
                <div>
                    {{ 
                            Form::password('password',array(
                                                            'class'=>'form-control','placeholder'=>'Password',
                                                            'title'=>'Enter a password', 'required'=>'required'
                                                         ))
                    }}
                </div>
                <div>
                    <button class="btn btn-default submit">Log in</button>
                    <a class="reset_pass" href="{{url("/resend/username")}}">Forgotten your username?</a>
                    <a class="reset_pass" href="{{url("/reset/password")}}">Lost your password?</a>
                </div>

                <div class="clearfix"></div>

                <div class="separator">
                    <p class="change_link fa-lg" style="margin-top: 10px; ">New Application?
                        <a href="{{ URL::route('register.student.voucher') }}" style="margin-top: -2px;" class="to_register btn btn-default btn-lg"><i class="fa fa-user-plus fa-lg"></i> Create Account </a>
                    </p>

                    <div class="clearfix"></div>
                    <br />

                    
                        <!-- <p>Licensed to</p>
                        <h1> Nursing and Midwifery Council</h1> -->
                        <p>©2021 All Rights Reserved.</p>

                    </div>


                </div>
                {{Form::close() }}
            </section>
        </div>



    </div>

        
    
</div>



@stop

@section('scripts')


@stop