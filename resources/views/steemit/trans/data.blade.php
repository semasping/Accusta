<div class="container">
    <div class="row">
        <div class="page-header">
            <h1>
                Account statistics: <a target="_blank"
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

            Total account earned on posts and curation:
            <br>Steem Power: {{  \App\semas\SteemitApi::convertToSg($all_gests) }}
            <br>SBD: {{ $all_author_rew['SBD'] }}
            <br>STEEM: {{ $all_author_rew['STEEM'] }}
        </div>

    </div>
    <div class="row">

        @foreach($month as $key=>$month1)
            <?php if (isset ($curator_by_month[$key])) {
                $cur = $curator_by_month[$key];
            }
            else {
                $cur = collect([]);
            }?>
            <?php if (isset ($author_by_month[$key])) {
                $author = $author_by_month[$key];
            }
            else {
                $author = collect([]);
            }?>
            <?php if (isset ($posts_by_month[$key])) {
                $posts = $posts_by_month[$key];
            }
            else {
                $posts = collect([]);
            }?>
            <?php if (isset ($transfer_out_by_month[$key])) {
                $transfer_out = $transfer_out_by_month[$key];
            }
            else {
                $transfer_out = collect([]);
            }?>
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" href="#{{ $key }}">
                                @if(isset($week))
                                    From {{ Date::parse($key)->startOfWeek()->format('d F Y') }}
                                    to {{ Date::parse($key)->endOfWeek()->format('d F Y') }}... <span
                                            class="glyphicon glyphicon-menu-down"></span></a>

                            @else
                                {{ Date::parse($key)->format("F Y") }} ...</a>
                            @endif
                            <br><br>
                            SP:{{ \App\semas\SteemitApi::convertToSg($author->sum('VESTS')+$cur->sum('VESTS'))}};
                            SBD:{{$author->sum('SBD')}}; STEEM:{{$author->sum('STEEM')}};

                        </h4>
                    </div>
                    <div id="{{ $key }}" class="panel-collapse collapse">
                        <div class="panel-body">

                            <h3>Author rewards from {{ $posts->count() }} posts:</h3>
                            SBD: {{$author->sum('SBD')}}
                            <br>STEEM: {{$author->sum('STEEM')}}
                            <br>SP: {{ \App\semas\SteemitApi::convertToSg($author->sum('VESTS')) }}
                            <br><br>

                            @include('steemit.trans.data.posts')
                            @include('steemit.trans.data.author_rewards')

                            <h3>Curation rewards:</h3>
                            STEEM POWER: {{ \App\semas\SteemitApi::convertToSg($cur->sum('VESTS')) }}

                            @include('steemit.trans.data.curator_rewards')


                            <br><h5>Other information:</h5>
                            @include('steemit.trans.data.transactions_out')

                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>

