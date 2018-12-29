@extends ('layouts.tra')

@section('title')Account not exists  @endsection

@section('style')
    <link rel="shortcut icon" href="/golos_icon.png">

    <style>
        form.form-inline {
            text-align: center;
        }
    </style>
@endsection

@section ('content')
    <div class="container-fluid">
        <div class="row">
            <br>
            <br>
            Такого аккаунта не существует. Попробуйте другой аккаунтю
        </div>
        <div class="row">
            @include(getenv('BCH_API').'.form')
        </div>
    </div>

@endsection