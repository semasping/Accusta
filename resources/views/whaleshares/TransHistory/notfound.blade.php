@extends ('layouts.tra')

@section('title')Account statistics  -  @endsection

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
            @include(getenv('BCH_API').'.form')
            @if($account=='')
                @include('trans.description')


            @else
                <div class="page-header">
                    <h2>
                        Account not found. Or there was a data processing error. Please enter another account or try the same after a while ..
                    </h2>
                </div>

            @endif

        </div>
    </div>

@endsection