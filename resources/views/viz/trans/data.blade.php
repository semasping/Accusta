<div class="container-fluid">
    <div class="row">
        <div class="page-header">
            <h1>
                Статистика аккаунта: <a target="_blank"
                                        href="https://golos.io/{{ '@'.$account }}">{{ '@'.$account }}</a>
            </h1>
            <h3>
                @if (!$date)
                    С момента регистрации (
                    <button type="button" class="btn btn-link btn-xs" data-toggle="modal" data-target="#myModal">
                        Указать дату
                    </button>)
                @else
                    С даты {{ \Jenssegers\Date\Date::parse($date)->format('d F Y') }} (
                    <button type="button" class="btn btn-link btn-xs" data-toggle="modal" data-target="#myModal">
                        Выбрать другую дату
                    </button>)
                @endif
            </h3>
        </div>
    </div>
    <div class="row">
        {!! link_to_route('trans_by_month',
'Export Author`s rewards to Excel (CSV)', ['account'=> $account, 'csv'=>1, 'authors'=>1, Request::getQueryString()],
['class' => 'btn btn-info pull-right ml10'])
!!}
        {!! link_to_route('trans_by_month',
        'Export Curator`s rewards to Excel (CSV)', ['account'=> $account, 'csv'=>1, 'curators'=>1, Request::getQueryString()],
        ['class' => 'btn btn-info pull-right ','role'=>"button"])
        !!}
        <div>
            Всего аккаунтом заработано на постах и кураторстве:
            <br>Силы Голоса: {{  \App\semas\GolosApi::convertToSg($all_gests) }}* (без учета переводов в СилуГолоса)
            <br>GBG: {{ $all_author_rew['GBG'] }}
            <br>GOLOS: {{ $all_author_rew['GOLOS'] }}
        </div>
    </div>
    <div class="row">

        @foreach($month as $key=>$month1)
            <?php if (isset ($curator_by_month[$key])) {
                $cur = $curator_by_month[$key];
            } else {
                $cur = collect([]);
            }?>
            <?php if (isset ($author_by_month[$key])) {
                $author = $author_by_month[$key];
            } else {
                $author = collect([]);
            }?>
            <?php if (isset ($posts_by_month[$key])) {
                $posts = $posts_by_month[$key];
            } else {
                $posts = collect([]);
            }?>
            <?php if (isset ($transfer_out_by_month[$key])) {
                $transfer_out = $transfer_out_by_month[$key];
            } else {
                $transfer_out = collect([]);
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
                            <br><br>Всего
                            СГ:{{ \App\semas\GolosApi::convertToSg($author->sum('GESTS')+$cur->sum('GESTS'))}}
                            @if ($vp_calc)
                                <br>Перечислить на доп акк для
                                ГолосФонда: {{ round((\App\semas\GolosApi::convertToSg($author->sum('GESTS')+$cur->sum('GESTS')))*20/100,3) }}
                            @endif
                        </h4>
                    </div>
                    <div id="{{ $key }}" class="panel-collapse collapse">
                        <div class="panel-body">

                            <h3>За данный период {{ $posts->count() }} поста. От постов поступило:</h3>
                            GBG: {{$author->sum('GBG')}}
                            <br>GOLOS: {{$author->sum('GOLOS')}}
                            <br>Силы Голоса: {{ \App\semas\GolosApi::convertToSg($author->sum('GESTS')) }}
                            <br><br>

                            @include('golos.trans.data.posts')
                            @include('golos.trans.data.author_rewards')

                            <h3>За данный период от кураторства поступило:</h3>
                            Силы Голоса: {{ \App\semas\GolosApi::convertToSg($cur->sum('GESTS')) }}

                            @include('golos.trans.data.curator_rewards')


                            <br><h5>Дополнительная разная информация:</h5>
                            @include('golos.trans.data.transactions_out')

                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
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

