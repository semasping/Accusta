@extends ('layouts.tra')

@section('title')Экспериментальные Расчеты понижения СГ-  @endsection


@section ('content')
<div class="container">
    <div class="row" >
        <div class="col-lg-12">
            {!! Form::open(array('action' => 'TransHistoryController@show_withdraw_example', 'class' => 'form-horizontal', 'method' => 'get')) !!}
            <div class="form-group">
                {!! Form::label('account','Account Name: @', ['class'=>'control-label']) !!}
                {!! Form::text('account', $account,
                    array('required','class'=>'form-control','placeholder'=>'Account name',)) !!}
            </div>
            <div class="form-group">
                {!! Form::submit('Посмотреть транзакции',  array('class'=>'btn btn-primary', 'data-after-submit-value'=>"Собираю транзакции. Это может занять некоторое время. Ждите&hellip;")) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<div class="container">
    <div class="row" >
        <div class="col-lg-12">
            {!! $grid !!}
        </div>
    </div>
</div>
@endsection
