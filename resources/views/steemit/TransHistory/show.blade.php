@extends ('layouts.tra')

@section('title')Accusta  -  {{ '@'.$account }}: History transaction with filter @endsection


@section('style')
    <link rel="shortcut icon" href="/golos_icon.png">
    <style>
        .container-fluid {
            width: 90%;
        }
        .radio-inline {
            vertical-align: baseline;
        }
    </style>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"/>
    {{--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css"/>--}}

@endsection

@section('js')

    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css"
          rel="stylesheet"/>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>


    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>


    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css"/>

    {{--<script src="//code.jquery.com/jquery-1.12.4.js"></script>--}}
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    {{--<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>--}}


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

            $('#datetimepicker1').daterangepicker({
                "showDropdowns": true,
                "autoApply": true,
                locale: {
                    format: 'DD.MM.YYYY'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'С создания аккаунта': ["{!! $date_acc_create !!}", moment()]
                },
                "alwaysShowCalendars": true,
                "startDate": "{!! $date[0] !!}",
                "endDate": "{!! $date[1] !!}",
                "opens": "center"
            }, function (start, end, label) {
                //console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
            });

            $('#tranz').DataTable({
                processing: true,
                responsive: true,
                serverSide: false,
                paging: true,
                ajax: {
                    "url": "{!! action('TransHistoryController@dt_show', [
                        'acc'=>$acc,

                        'd_from'=>$request->get('d_from'),
                        'currency'=>$request->get('currency'),
                        'exclude_sum_less'=>$request->get('exclude_sum_less'),
                        'exclude_sum_more'=>$request->get('exclude_sum_more'),
                        'tr_type'=>$request->get('tr_type'),
                    ])
                    !!}",
                    "type": "GET"
                },
                columns: [
                    {data: 'date', name: 'date'},
                    {data: 'from', name: 'from'},
                    {data: 'to', name: 'to'},
                    {data: 'amount', name: 'amount'},
                    {data: 'sum', name: 'sum'},
                    {data: 'currency', name: 'currency'},
                    {data: 'memo', name: 'memo'},
                    {data: 'block', name: 'block'},
                    {data: 'trx_id', name: 'trx_id'},
                ],
                "order": [[ 0, "desc" ]],
                /*'initComplete': function (settings, json) {
                    this.api().columns('.sum').every(function () {
                        var column = this;

                        var intVal = function (i) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '') * 1 :
                                typeof i === 'number' ?
                                    i : 0;
                        };

                        var sum = column
                            .data()
                            .reduce(function (a, b) {
                                return (intVal(a).toFixed(3) + intVal(b)).toFixed(3);
                            });


                        $(column.footer()).html('Итого: ' + sum);
                    });
                }*/
                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api();

                    api.columns('.sum', {
                    "filter": "applied"
                    }).every(function() {
                        var sum = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return (x+y).toFixed(3);
                            }, 0);
                        console.log(sum); //alert(sum);
                        $(this.footer()).html(sum);
                    });
                }
            });
        });
    </script>
@endsection

@section ('content')


    <div class="container-fluid">
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
                {!! Form::open(array('url' => url('_form_submit'), 'class' => 'form-horizontal', 'method' => 'get')) !!}
                <div class="form-group">
                    {!! Form::label('account','Account Name: @', ['class'=>'control-label']) !!}
                    {!! Form::text('account', $acc,
                        array('required','class'=>'form-control','placeholder'=>'Account name')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('tr_type','Transaction types', ['class'=>'control-label']) !!}

                    <label class="radio-inline"><input type="radio" name="tr_type" value="all" {!! $tr['all'] !!}>All</label>
                    <label class="radio-inline"><input type="radio" name="tr_type" value="in"  {!! $tr['tr_in'] !!}>In</label>
                    <label class="radio-inline"><input type="radio" name="tr_type" value="out"  {!! $tr['tr_out'] !!}>Out</label>
                </div>
                <div class="form-group">
                    {!! Form::label('currency','Currency', ['class'=>'control-label']) !!}
                    {!! Form::select('currency', ['sbd'=>'SBD','steem'=>'STEEM'],null,
                        array('class'=>'form-control','placeholder'=>'Choose currency')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('exclude_sum','Exclude sums less', ['class'=>'control-label']) !!}
                    {!! Form::text('exclude_sum_less', null,
                    array('class'=>'form-control','placeholder'=>'<', '')) !!}
                    {!! Form::label('exclude_sum','Exclude sums more', ['class'=>'control-label']) !!}
                    {!! Form::text('exclude_sum_more', null,
                        array('class'=>'form-control','placeholder'=>'>', '')) !!}

                </div>
                {{--<div class="form-group">
                    {!! Form::label('exclude_acc_from','Исключить аккаунты от', ['class'=>'control-label']) !!}
                    {!! Form::text('exclude_acc_from', null,
                        array('class'=>'form-control','placeholder'=>'', 'disabled')) !!}
                </div>--}}

                <div class="form-group">
                    {!! Form::label('d_from','SHow from period:', ['class'=>'control-label'] ) !!}
                    <div class='input-group' id='datetimepickerraw'>
                        <input type='text' class="form-control" name="d_from" id="datetimepicker1"/>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::hidden('controller', $form_action) !!}
                    {!! Form::submit('Show transaction',  array('class'=>'btn btn-primary', 'data-after-submit-value'=>"Load transaction. It takes a while&hellip;")) !!}
                    {!! link_to_route('trans_history_dt_show',
'Export to Excel (CSV)', ['account'=> $acc, 'csv'=>1, Request::getQueryString()],
['class' => 'btn btn-info pull-right'])
!!}
                    {{--{!! Form::label('','*Возможность выбора параметров будет в течении дня', ['class'=>'control-label']) !!}--}}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="row">
            <table id="tranz">
                <thead>
                <tr>
                    <td>Date (GMT)</td>
                    <td>Form</td>
                    <td>To</td>
                    <td>Amount</td>
                    <td class="sum">Sum</td>
                    <td>Currency</td>
                    <td>Memo</td>
                    <td>ID block</td>
                    <td>ID transaction</td>
                </tr>
                </thead>
                <tbody>

                </tbody>
                <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <br>
    <br>


@endsection