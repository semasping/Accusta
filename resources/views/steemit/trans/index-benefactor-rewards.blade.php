@extends ('layouts.tra')

@section('title')Accusta  -  {{ '@'.$account }}: Benefactor rewards statistics @endsection


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
            @include(getenv('BCH_API').'.form')
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                {!! $chartRewardsIn->render() !!}
                Sums All rewards: {!! $dataIn['allSP'] !!}
                @foreach($dataIn['month'] as $k=>$m)
                    <div class="">{!! $m !!}: {!! $dataIn['total'][$k] !!} Steem Power</div>
                @endforeach
            </div>
            <div class="col-md-6">
                {!! $chartRewardsOut->render() !!}
                Sums All rewards: {!! $dataOut['allSP'] !!}
                @foreach($dataOut['month'] as $k=>$m)
                    <div class="">{!! $m !!}: {!! $dataOut['total'][$k] !!} Steem Power</div>
                @endforeach
            </div>

        </div>
    </div>




@endsection