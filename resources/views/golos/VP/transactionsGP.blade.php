@extends ('layouts.tra')

@section('title')Accusta for Vox-populi: статистика кураторских @endsection

@section('style')
    <link rel="shortcut icon" href="/favicon.ico">
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
    <div class="container-fluid">
        <div class="row">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>


        </div>
    </div>





    @foreach($accounts_vp as $key0=>$acc_vp)
        @foreach($acc_vp as $key=>$gp)
            @if(!empty($gp))
                <div class="panel-group">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" href="#collapse_{{ $loop->index.$key }}">{{ $gp }}</a>
                            </h4>
                        </div>
                        <div id="collapse_{{ $loop->index.$key }}" class="panel-collapse collapse">
                            <div class="panel-body">@asyncWidget('VPTransactions', ['account'=>$gp])</div>

                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endforeach









@endsection