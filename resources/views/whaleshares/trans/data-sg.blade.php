
<div class="container-fluid">
    <div class="row">
        <div class="page-header">
            <h1>
                Steem Power down statistics for <a target="_blank"
                                        href="https://steemit.com/{{ '@'.$account }}">{{ '@'.$account }}</a>
            </h1>
            <h3>

                @if (!$date)
                    From registration (
                    <button type="button" class="btn btn-link btn-xs" data-toggle="modal" data-target="#myModal">
                        Choose date
                    </button>)
                @else
                    From {{ \Jenssegers\Date\Date::parse($date)->format('d F Y') }} (
                    <button type="button" class="btn btn-link btn-xs" data-toggle="modal" data-target="#myModal">
                        Choose date
                    </button>)
                @endif
            </h3>
        </div>
    </div>
    @if(!empty($wv_by_month))
        <div class="row">{!! link_to_route('trans_sg',
'Export to Excel (CSV)', ['account'=> $acc, 'csv'=>1, Request::getQueryString()],
['class' => 'btn btn-info pull-right'])
!!}</div>
        <div class="row">
        @foreach($month as $key=>$month1)
            <?php if (isset ($wv_by_month[$key])) {
                $wv = $wv_by_month[$key];
            } else {
                $wv = collect([]);
            }?>
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" href="#{{ $key }}">
                                @if(isset($week))
                                    From {{ Date::parse($key)->startOfWeek()->format('d F Y') }}
                                    to {{ Date::parse($key)->endOfWeek()->format('d F Y') }}... <span class="glyphicon glyphicon-menu-down"></span></a>

                            @else
                                From: {{ Date::parse($key)->format("F Y") }} ...</a>
                            @endif
                            <br><br>Vests get: {{ $wv['withdrawn'] }}<br>
                            Steem get:{{ $wv['deposited'] }}
                        </h4>
                    </div>
                    <div id="{{ $key }}" class="panel-collapse collapse">
                        <div class="panel-body">


                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
        @else
        <div class="row">
            <h2>
                There is no Power down In choosen period
            </h2>
        </div>

        @endif

</div>

