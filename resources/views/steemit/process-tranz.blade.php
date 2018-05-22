@extends ('layouts.tra')

@section('title')Collect account transactions  @endsection

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
    <div class="container-fluid">
        <div class="row">
            <br>
            <br>
            Processing account history. Wait. <br>
            Processed {{ $current }} from {{ $total }}. <br>
            The page will be updated in the processing.
        </div>
    </div>

@endsection