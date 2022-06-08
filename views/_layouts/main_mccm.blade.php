<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title or 'Mount Calvary Cross Ministry'  }} </title>



    <!-- Font awesome -->
    <link href="{{ asset('plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="{{ asset('plugins/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">

    <link href="{{ asset('plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">

    <link href="{{ asset('plugins/multiple-select-master/multiple-select.css') }}" rel="stylesheet">

    <link href="{{ asset('plugins/iCheck/skins/flat/green.css') }}" rel="stylesheet">

    <link href="{{ asset('plugins/sweetalert/sweetalert2.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/switchery/dist/switchery.min.css') }}" rel="stylesheet">


    <link href="{{ asset('plugins/jquery-ui/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">



            <!-- Datatables -->
    <link href="{{ asset('plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('plugins/datatables.net-responsive-bs/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/datatables.net-scroller-bs/css/scroller.bootstrap.min.css') }}" rel="stylesheet">

        <!-- <script type="text/javascript" src="{{asset('js/modernizr.js')}}"></script> -->

        <!-- Custom Theme Style -->
        <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
        <link href="{{ asset('css/overlay.css') }}" rel="stylesheet">


        
        @yield('styles')
    </head>

    <body class="nav-md">

        <div class="overlay">
            <div class="overlay-content">
                <a class="lead">Please wait...</a>
                <div class="windows8">
                        <div class="wBall" id="wBall_1">
                            <div class="wInnerBall"></div>
                        </div>
                            <div class="wBall" id="wBall_2">
                                <div class="wInnerBall"></div>
                            </div>
                                <div class="wBall" id="wBall_3">
                                    <div class="wInnerBall"></div>
                                </div>
                            <div class="wBall" id="wBall_4">
                                <div class="wInnerBall"></div>
                            </div>
                            <div class="wBall" id="wBall_5">
                                <div class="wInnerBall"></div>
                            </div>
               </div>
          </div>
      </div>

        <div class="container body">
            <div class="main_container">
                <div class="col-md-3 left_col">
                    <div class="left_col scroll-view">
                        <div class="navbar nav_title" style="border: 0;">
                            <a href="{{ URL::route('mccm.dashboard') }}" class="site_title"><i class="fa fa-paw"></i> <span>MCCM-HQ</span></a>
                        </div>

                        <div class="clearfix"></div>

                        <!-- menu profile quick info -->
                        <div class="profile">
                            <div class="profile_pic">
                                <img src="{{ asset('images/placeholder.jpg') }}" alt="..." class="img-circle profile_img">
                            </div>
                            <div class="profile_info">
                                <span>Welcome,</span>
                                <h2>  @if(!empty(Auth::user()->username))
                                                {{Auth::user()->username}}
                                            @else
                                            MCCM- House of Refuge
                                            @endif
                                </h2>
                            </div>
                        </div>
                      

                        <br />

                        <!-- sidebar menu -->
                        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                            <div class="menu_section">
                                <div class="clearfix"></div>
                                <ul class="nav side-menu">
                                    <li><a><i class="fa fa-home"></i> Home <span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <li><a href="{{ URL::route('mccm.dashboard') }}">Dashboard</a></li>
                                        </ul>
                                    </li>
                                    
                                    @if (Auth::user()->hasRole("SuperUser"))
                                    <li><a href="{{ URL::route('nmc.setup.institutions') }}"><i class="fa fa-building fa-lg"></i> Branches</a></li> 
                                    @endif
                                  

                                    @if (Auth::user()->hasRole("SuperUser"))
                                    <li><a href="{{ URL::route('mccm.members.list') }}"><i class="fa fa-users"></i> Members</a></li> 
                                    @endif
                                    
                                    @if (Auth::user()->hasRole("SwAdmin"))
                                    <li><a href="{{ URL::route('mccm.branch.converts.list') }}"><i class="fa fa-user"></i> Convert</a></li> 
                                    @endif

                                    @if (Auth::user()->hasRole("SuperUser") || Auth::user()->hasRole("CashAdmin")|| Auth::user()->hasRole("AccountAdmin") )
                                    <li><a><i class="fa fa-money fa-lg"></i> Accounts <span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                    
                                        @if (Auth::user()->hasRole("SuperUser") || Auth::user()->hasRole("CashAdmin") )
                                            <li><a href="{{ URL::route('mccm.account.entry')  }}"> Account Entry</a></li>
                                            @endif
                                            @if (Auth::user()->hasRole("SuperUser") || Auth::user()->hasRole("AccountAdmin") )
                                            <li><a href="{{ URL::route('mccm.account')  }}"> Receipts</a></li>
                                            @endif
                                        </ul>
                                    </li>
                                    @endif
                                    
                                    
                                    @if (Auth::user()->hasRole("SuperUser"))
                                    <li><a><i class="fa fa-gear"></i> Setup <span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <li><a href="{{ URL::route('mccm.setup.groups')  }}"> Group User Accounts</a></li>

                                    
                                        <li><a href="{{ URL::route('mccm.setup.users') }}"> Users</a></li>
                                    
                                    
                                        </ul>
                                    </li>
                                    @endif
                                   

                                
                                    
                                    
                                </ul>
                            </div>
                           
                        </div>
                        <!-- /sidebar menu -->

                        
                    </div>
                </div>

                <!-- top navigation -->
                <div class="top_nav">
                    <div class="nav_menu">
                        <nav class="" role="navigation">
                            <div class="nav toggle">
                                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                            </div>

                            <ul class="nav navbar-nav navbar-right">
                                <li class="">
                                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <img src="{{ asset('images/placeholder.jpg') }}" alt="">
                                            @if(!empty(Auth::user()->username))
                                                {{Auth::user()->username}}
                                            @else
                                            MCCM - House of Refuge
                                            @endif
                                        <span class=" fa fa-angle-down"></span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-usermenu pull-right">
                                        <li><a href="{{ URL::route('branch.changepassword') }}"><i class="fa fa-key pull-right"></i> Change Password</a></li>
                                        <li><a href="{{ URL::route('logout') }}"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                                    </ul>
                                </li>

                                
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- /top navigation -->

                <!-- page content -->
                <div class="right_col" role="main">
                    @yield('content')
                    
                </div>
                <!-- /page content -->

                <!-- footer content -->
                <footer>
                    <div class="pull-right">
                        Mount Calvary Cross Ministry
                    </div>
                    <div class="clearfix"></div>
                </footer>
                <!-- /footer content -->
            </div>
        </div>

   
        <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('plugins/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('plugins/fastclick/lib/fastclick.js') }}"></script>
    <!-- NProgress -->
    <script src="{{ asset('plugins/nprogress/nprogress.js') }}"></script>

    <script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>

    <script src="{{ asset('plugins/sweetalert/sweetalert2.min.js') }}"></script>

    <script src="{{ asset('plugins/select2/dist/js/select2.full.min.js') }}"></script>


    <script src="{{ asset('plugins/jquery-ui/jquery-ui.js') }}"></script>

    <script src="{{ asset('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

    <!-- Switchery -->
    <script src="{{ asset('plugins/switchery/dist/switchery.min.js') }}"></script>
        



     <!-- Datatables -->
     <script src="{{ asset('plugins/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-buttons-bs/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-responsive-bs/js/responsive.bootstrap.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-scroller/js/datatables.scroller.min.js') }}"></script>

    <script src="{{ asset('plugins/jszip/dist/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/build/vfs_fonts.js') }}"></script>

    <!--multiple select-->
    <script src="{{ asset('plugins/multiple-select-master/multiple-select.js') }}"></script>

        
        <!-- Custom Theme Scripts -->
        <script src="{{ asset('js/custom.js') }}"></script>

        <script>
            $(document).ready(function () {
                $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });


                $('.select2_single').each(function (index, value) {
                    curr = $(this);
                    $(curr).select2({
                        placeholder: $(curr).attr('placeholder'),
                        allowClear: true
                    });
        //            console.log('div' + index + ':' + $(this).attr('placeholder'));
                });
        
            });
        </script>
        @yield('scripts')
    </body>
</html>