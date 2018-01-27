<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Accusta</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <link rel="shortcut icon" href="/golos_icon.png">

    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
            margin-top: -20%;
        }

        .text {
            text-align: left;
            margin-top: 20px;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        footer {
            position: absolute;
            bottom: 0px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                    @endauth
        </div>
    @endif

    <div class="content">
        <div class="title m-b-md">
            Accusta
        </div>

        <div class="links">
            <a href="#">Сервис статистики аккаунтов в блокчейне Голос</a>
        </div>

        <div class="text">
            <h3>Возможности сервиса:</h3>
            <p>Статистика понижения Силы голоса</p>
            <p>Фильтр истории транзаций</p>
            <p>Группировка данных по месяцам или неделям:</p>
            <ul>
                <li> * Посты</li>
                <li> * Выплаты за посты</li>
                <li> * Кураторские</li>
            </ul>


        </div>
        {!! Form::open(array('url' => url('_form_submit'), 'class' => 'form-inline', 'method' => 'get')) !!}
        <div class="form-group">
            {!! Form::label('Account Name: @') !!}
            {!! Form::text('acc', null,
                array('required','class'=>'form-control','placeholder'=>'Account name')) !!}
            {!! Form::hidden('controller', 'TransAccController@index') !!}

        </div>
        {!! Form::submit('Начать просмотр',  array('class'=>'btn btn-primary', 'data-after-submit-value'=>"Собираю транзакции. Это может занять некоторое время. Ждите&hellip;")) !!}
        {!! Form::close() !!}
    </div>

    <footer class="footer">
        <div class="container">
            <row>
                <center>
                    <p class="text-muted">Разработкой сервиса занят:<a href="https://golos.io/@semasping">@semasping</a>. </p>


                        Проголосовать за <b>Делегата Semasping</b> вы можете <a href="https://golos.io/~witnesses">https://golos.io/~witnesses</a> или <a href="https://goldvoice.club/witnesses/">https://goldvoice.club/witnesses/</a>

                </center>
            </row>
        </div>
    </footer>
</div>

</body>
</html>
