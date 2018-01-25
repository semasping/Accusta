@extends ('layouts.tra')

@section('title')Статистика аккаунта  -  @endsection

@section('style')
    <link rel="shortcut icon" href="/golos_icon.png">
    <style>
        form.form-inline {
            text-align: center;
        }
    </style>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
@endsection

@section('js')
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css"
          rel="stylesheet"/>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.ru.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>

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

        /*        jQuery(function ($) {
         $('.datepicker').datepicker();
         });*/
    </script>
@endsection

@section ('content')
    <div class="container">
        <div class="row">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            {!! Form::open(array('action' => [$form_action, '@'.$account], 'class' => 'form-inline', 'method' => 'get')) !!}
            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Выбрать дату</h4>
                        </div>
                        <div class="modal-body">
                            {!! Form::date('d_from', $date) !!}
                            <br>Выберите нужную вам дату в после сверху.
                            <br>Чтобы отменить выбор даты нажмите на крестик.
                            <br>Затем "закрыть выбор" и "посмотреть транзакции"
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть выбор</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('Account Name: @') !!}
                {!! Form::text('acc', null,
                    array('required','class'=>'form-control','placeholder'=>'Account name')) !!}

            </div>
            {!! Form::submit('Посмотреть транзакции',  array('class'=>'btn btn-primary', 'data-after-submit-value'=>"Собираю транзакции. Это может занять некоторое время. Ждите&hellip;")) !!}
{{--            {!! Form::label('Рассчеты для Vox-Populi') !!}
            {!! Form::checkbox('vp', 'on') !!}--}}
            {!! Form::close() !!}
        </div>
    </div>
    @include('trans.data')





@endsection