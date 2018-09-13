<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    {{--    {{ Html::style(('assets/app/css/app.css')) }}
        {{ Html::style(('assets/admin/css/admin.css')) }}
        {{ Html::style(('assets/admin/css/dashboard.css')) }}--}}
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="/css/slickModal.min.css">

    @yield('style')
    <link rel="shortcut icon" href="/favicon.ico">

    <style>
        /* Sticky footer styles
        -------------------------------------------------- */
        html {
            position: relative;
            min-height: 100%;
        }

        body {
            /* Margin bottom by footer height */
            margin-bottom: 60px;
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            /* Set the fixed height of the footer here */

            background-color: #f5f5f5;
            padding: 5px;
        }

        /* Custom page CSS
        -------------------------------------------------- */
        /* Not required for template or sticky footer method. */

        .container {

            padding: 0 15px;
        }

        .container .text-muted {
            margin: 10px 0;
            text-align: center;
        }

        img.logo {
            margin-top: -5px;
        }
        .ml10 {
            margin-left: 10px;
        }

        .navbar-fixed-left {
            width: 220px;
            position: fixed;
            border-radius: 0;
            height: 100%;
        }
        .navbar-brand {
            float: none;
        }

        .navbar-fixed-left .navbar-nav > li {
            float: none;  /* Cancel default li float: left */
            width: auto;
        }

        .navbar-fixed-left + .container-fluid {
            padding-left: 240px;
        }

        /* On using dropdown menu (To right shift popuped) */
        .navbar-fixed-left .navbar-nav > li > .dropdown-menu {
            margin-top: -50px;
            margin-left: 220px;
        }

        .navbar>.container-fluid .navbar-brand {
            margin-left: 0px;
        }

        .slabel {
            font-size: 12px;
        }
        .slabel .month {
            display: inline-block;
            width: 62px;
        }
        .slabel .gbg {
            display: inline-block;
            width: 110px;
        }
        .slabel .golos {
            display: inline-block;
            width: 130px;
        }
        .slabel .sg {
            display: inline-block;

        }
    </style>


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div id="app" class=" ">
    <div class="">
        <nav class="navbar navbar-default navbar-fixed-left">
            <div class="container-fluid">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>


                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    @include(getenv('BCH_API').'.menu')

                    <!-- Right Side Of Navbar -->
                    {{--<ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @guest
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>--}}
                </div>
            </div>
            @include(getenv('BCH_API').'.footer')
        </nav>
        <div class="container-fluid">
            @yield('content')
        </div>
        @include(getenv('BCH_API').'.new-articles-modal')

    </div>
</div>


<br>

<!-- Scripts -->
<!-- jQuery -->
<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
<!-- DataTables -->

<!-- Bootstrap JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!-- Slick Modals jQuery plugin -->
<script type="text/javascript" src="/js/slickModal.min.js"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js" integrity="sha256-XF29CBwU1MWLaGEnsELogU6Y6rcc5nCkhhx89nFMIDQ=" crossorigin="anonymous"></script>


@yield('js')
@yield('js2')
@stack('js2')
</body>
</html>
