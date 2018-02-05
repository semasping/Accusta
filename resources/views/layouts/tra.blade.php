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
            height: 70px;
            background-color: #f5f5f5;
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
    </style>


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body >
<div id="app" class=" ">
    <div class="">
        <nav class="navbar navbar-default navbar-fixed-top">
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
                    <ul class="nav navbar-nav">
                        <a class="navbar-brand" href="{{ route('welcome') }}">
                            <img class="logo" src="/images/accusta_logo_line_170.png">
                        </a>
                        <a class="navbar-brand" href="{{ route('trans_by_month',[''.$account]) }}">
                            Статистика аккаунта по месяцам
                        </a>
                        <a class="navbar-brand" href="{{ route('trans_by_week',[''.$account]) }}">
                            Статистика аккаунта по неделям
                        </a>
                        <a class="navbar-brand" href="{{ route('trans_history',[''.$account]) }}">
                            История транзакций
                        </a>
                        <a class="navbar-brand" href="{{ route('trans_sg',[''.$account]) }}">
                            Статистика понижения СГ <span class="glyphicon glyphicon-star-empty " style="color: red"></span>
                        </a>
                    </ul>

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
        </nav>
<br>
<br>
        @yield('content')
    </div>
</div>


<br>
<footer class="footer">
    <div class="container">
        <div class="row links ">
            <center>
                <p class="text-muted links">Разработкой сервиса занят:<a href="https://golos.io/@semasping">@semasping</a>. </p>
                <p class="links"> Проголосовать за <b>Делегата Semasping</b> вы можете <a href="https://golos.io/~witnesses">https://golos.io/~witnesses</a> или <a href="https://goldvoice.club/witnesses/">https://goldvoice.club/witnesses/</a></p>

            </center>
        </div>
    </div>
</footer>
<!-- Scripts -->
<!-- jQuery -->
<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
<!-- DataTables -->

<!-- Bootstrap JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


@yield('js')
</body>
</html>
