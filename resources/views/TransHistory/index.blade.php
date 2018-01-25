@extends ('layouts.tra')

@section('title')Статистика аккаунта -  @endsection

@section('style')
    <link rel="shortcut icon" href="/golos_icon.png">
    <style>

    </style>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"/>
@endsection

@section('js')

    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css"
          rel="stylesheet"/>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment-with-locales.min.js"
            integrity="sha256-/ioiJhI6NkoUDkSyBru7JZUGXGQhdml6amBC3ApTf5A=" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    {{--<script src="//code.jquery.com/jquery-1.12.4.js"></script>--}}
    {{--<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>--}}

    <script>
        jQuery(function ($) {
            // set data-after-submit-value on input:submit to disable button after submit
            $(document).on('submit', 'form', function () {
                var $form = $(this),
                    $button,
                    label;
                $form.find(':submit').each(function () {
                    $button = $(this);
                    label = $button.data('after-submit-value');
                    if (typeof label != 'undefined') {
                        $button.val(label).prop('disabled', true);
                    }
                });
            });
        });

        jQuery(function ($) {
            $(function () {
                $('#datetimepicker1').datetimepicker({
                    sideBySide: false,
                    format: 'YYYY MM DD HH:mm'
                });
            });


        });
    </script>
@endsection

@section ('content')

    {!! $grid !!}
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <div class="row">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <div class="col-md-12">
                {!! Form::open(array('action' => [$form_action, '@'.$account], 'class' => 'form-horizontal', 'method' => 'post')) !!}
                <div class="form-group">
                    {!! Form::label('account','Account Name: @', ['class'=>'control-label']) !!}
                    {!! Form::text('account', $account,
                        array('required','class'=>'form-control','placeholder'=>'Account name', 'disabled')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('currency','Валюта', ['class'=>'control-label']) !!}
                    {!! Form::select('currency', ['GBG','GOLOS'],null,
                        array('class'=>'form-control','placeholder'=>'Выберите валюту', 'disabled')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('exclude_sum','Исключить суммы меньше', ['class'=>'control-label']) !!}
                    {!! Form::text('exclude_sum', null,
                        array('class'=>'form-control','placeholder'=>'', 'disabled')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('exclude_acc_from','Исключить аккаунты от', ['class'=>'control-label']) !!}
                    {!! Form::text('exclude_acc_from', null,
                        array('class'=>'form-control','placeholder'=>'', 'disabled')) !!}
                </div>
{{--                <div class="form-group">
                    {!! Form::label('d_from','Считать с даты:', ['class'=>'control-label'] ) !!}
                    <div class='input-group date' id='datetimepicker1'>
                        <input type='text' class="form-control" name="d_from" id="d_from" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>--}}
                <div class="form-group">
                    {!! Form::submit('Посмотреть транзакции',  array('class'=>'btn btn-primary', 'data-after-submit-value'=>"Собираю транзакции. Это может занять некоторое время. Ждите&hellip;")) !!}
                    {!! Form::label('','*Возможность выбора параметров будет в течении дня', ['class'=>'control-label']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
<br>
<br>

@endsection