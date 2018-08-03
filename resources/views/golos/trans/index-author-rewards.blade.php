@extends ('layouts.tra')

@section('title')Accusta  -  {{ '@'.$account }}: Авторские вознаграждения @endsection


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
    <div class="container-fluid">
        <div class="row">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            @include(getenv('BCH_API').'.form')
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Авторские вознаграждения аккаунта: {{'@'.$acc}}</div>
                    {{--<div class="panel-body">{!! $chartRewardsIn->render() !!}</div>--}}
                    <div class="panel-footer slabel">
                        {!! link_to_route('trans_by_month',
                                       'Export rewards to Excel (CSV)', ['account'=> $account, 'csv'=>1, 'type'=>'in'],
                                       ['class' => 'btn btn-info pull-right','role'=>"button"])
                                       !!}

                        <span class="slabel all">Сумма всех вознаграждений</span>
                        <br>
                        <span class="slabel gbg">| {!! $dataIn['allSBD'] !!} GBG</span>
                        <span class="slabel golos">| {!! $dataIn['allSTEEM'] !!} GOLOS </span>
                        <span class="slabel sg">| {!! $dataIn['allSP'] !!} СГ</span>

                    </div>
                </div>
                <div class="panel-group" id="aIn" role="tablist" aria-multiselectable="true">
                    <?php krsort($dataIn['month']) ?>
                    @foreach($dataIn['month'] as $k=>$m)
                        @include('golos.trans.data.author-by-month', [$k, 'data'=>$dataIn, $m, 'type'=>'In'])
                    @endforeach
                </div>
            </div>


        </div>
    </div>




@endsection

@prepend('js2')
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

    <script>
        $('#aIn').on('show.bs.collapse', function (e) {
            var id  = $(e.target).attr('id');
            var month  = $(e.target).attr('data-month');
            var type = $(e.target).attr('data-type');
            //alert(month + type);
            //$(this).find("#body"+type+month).load($(e.target).attr("data-href"));
            var table = $('#data'+type+month).DataTable();
            table.ajax.url( $(e.target).attr("data-href") ).load();
        })
        $('#aOut').on('show.bs.collapse', function (e) {
            var id  = $(e.target).attr('id');
            var month  = $(e.target).attr('data-month');
            var type = $(e.target).attr('data-type');
            //$(this).find("#body"+type+month).load($(e.target).attr("data-href"));
            var table = $('#data'+type+month).DataTable();
            table.ajax.url( $(e.target).attr("data-href") ).load();
        })
    </script>
@endprepend
