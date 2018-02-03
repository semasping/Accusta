@extends ('layouts.tra')

@section('title')Транзакции аккаунта  -  @endsection

@section('style')
    <link rel="shortcut icon" href="/golos_icon.png">
    <meta http-equiv="refresh" content="5" />
    <style>
        form.form-inline {
            text-align: center;
        }
    </style>
@endsection

@section ('content')
    <div class="container">
        <div class="row">
            <br>
            <br>
            Обрабатываю историю аккаунта. Ждите.<br>
            Обработано {{  $current }} из {{ $total }}.<br>
            Страница будет обновляться в процесс обработки.
        </div>
    </div>

@endsection