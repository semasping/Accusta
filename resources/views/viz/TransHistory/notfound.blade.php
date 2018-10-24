@extends ('layouts.tra')

@section('title')Транзакции аккаунта  -  @endsection

@section('style')
    <link rel="shortcut icon" href="/golos_icon.png">
    <style>
        form.form-inline {
            text-align: center;
        }
    </style>
@endsection

@section('js')
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css"
          rel="stylesheet"/>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.ru.min.js"></script>

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
    </script>
@endsection

@section ('content')
    <div class="container-fluid">
        < class="row">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            {!! Form::open(array('action' => [$form_action, '@'.$account], 'class' => 'form-inline', 'method' => 'get')) !!}
            <div class="form-group">
                {!! Form::label('Account Name: @') !!}
                {!! Form::text('acc', null,
                    array('required','class'=>'form-control','placeholder'=>'Account name')) !!}
            </div>
            {!! Form::submit('Посмотреть транзакции',  array('class'=>'btn btn-primary', 'data-after-submit-value'=>"Собираю транзакции. Это может занять некоторое время. Ждите&hellip;")) !!}
            {!! Form::close() !!}
            @if($account=='')
                @include('trans.description')


            @else
                <div class="page-header">
                    <h1>
                        Аккаунт не найден. Или возникла ошибка обработки данных. Введите другой аккаунт или попробуйте
                        этот же через некоторое время..
                    </h1>
                </div>

            @endif

        </div>
    </div>

@endsection