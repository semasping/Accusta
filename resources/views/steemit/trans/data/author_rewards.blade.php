@if ($author->count()>0)
    <?php
    $summ_gbg=0;
    $summ_golos=0;
    $summ_gests=0;
    ?>
    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" href="#arw_{{$key}}"> Author rewards:</a>
                </h4>
            </div>
            <div id="arw_{{$key}}" class="panel-collapse collapse">
                <div class="panel-body">

                    <TABLE class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="timestamp">Date</th>
                            <th class="permlink">Permlink</th>
                            <th class="GBG">SBD</th>
                            <th class="GOLOS">STEEM</th>
                            <th class="GESTS">VESTS</th>
                            <th class="SG">SP</th>
                        </tr>
                        </thead>
                        @foreach($author as $athr)
                            <?php
                            $summ_gbg = $summ_gbg+$athr['SBD'];
                            $summ_golos = $summ_golos+$athr['STEEM'];
                            $summ_gests = $summ_gests+$athr['VESTS'];
                            ?>
                            <tr>
                                <td class="timestamp">{{  Date::parse($athr['timestamp'])->format("H:i d.m.Y ") }}</td>
                                <td class="permlink">{{ $athr['permlink'] }}</td>
                                <td class="GBG">{{ $athr['SBD'] }}</td>
                                <td class="GOLOS">{{ $athr['STEEM'] }}</td>
                                <td class="GESTS">{{ $athr['VESTS'] }}</td>
                                <td class="SG">{{ \App\semas\SteemitApi::convertToSg($athr['VESTS']) }}
                                </td>

                            </tr>

                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{  $summ_gbg}}</td>
                            <td>{{  $summ_golos}}</td>
                            <td>{{  $summ_gests}}</td>
                            <td>{{  \App\semas\SteemitApi::convertToSg($summ_gests) }}</td>
                        </tr>
                    </TABLE>

                </div>
            </div>
        </div>
    </div>
@endif