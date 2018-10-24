
<div class="container-fluid">
    <div class="row">
        <div class="page-header">
            <h1>
                Статистика понижения СГ аккаунтом: <a target="_blank"
                                        href="https://golos.io/{{ '@'.$account }}">{{ '@'.$account }}</a>
            </h1>
            <h3>
                @if (!$date)
                    С момента регистрации
                @else
                    С даты {{ \Jenssegers\Date\Date::parse($date)->format('d F Y') }} (
                    <button type="button" class="btn btn-link btn-xs" data-toggle="modal" data-target="#myModal">
                        Выбрать другую дату
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
                                    Период c {{ Date::parse($key)->startOfWeek()->format('d F Y') }}
                                    по {{ Date::parse($key)->endOfWeek()->format('d F Y') }}... <span class="glyphicon glyphicon-menu-down"></span></a>

                            @else
                                Период: {{ Date::parse($key)->format("F Y") }} ...</a>
                            @endif
                            <br><br>Всего понижение на: {{ $wv['withdrawn'] }}<br> получено:
                            СГ:{{ $wv['deposited'] }}
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
                В указанный период аккаунт Не понижал Силу Голоса.
            </h2>
        </div>

        @endif
    <div class="row">
        {{--            <table class="table table-bordered" id="users-table">
                        <thead>
                        <tr>
                            <th>block</th>
                            <th>timestamp</th>
                            <th>permlink</th>
                            <th>GBG</th>
                            <th>GP</th>
                            <th>GESTS</th>
                        </tr>
                        </thead>
                    </table>--}}
    </div>
</div>

