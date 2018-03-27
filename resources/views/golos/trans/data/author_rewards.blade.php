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
                    <a data-toggle="collapse" href="#arw_{{$key}}"> Выплаты:</a>
                </h4>
            </div>
            <div id="arw_{{$key}}" class="panel-collapse ">
                <div class="panel-body">

                    <TABLE class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="timestamp">Дата</th>
                            <th class="permlink">permlink</th>
                            <th class="GBG">GBG</th>
                            <th class="GOLOS">GOLOS</th>
                            <th class="GESTS">GESTS</th>
                            <th class="SG">СГ</th>
                        </tr>
                        </thead>
                        @foreach($author as $athr)
                            <?php
                            $summ_gbg = $summ_gbg+$athr['GBG'];
                            $summ_golos = $summ_golos+$athr['GOLOS'];
                            $summ_gests = $summ_gests+$athr['GESTS'];
                            ?>
                            <tr>
                                <td class="timestamp">{{  Date::parse($athr['timestamp'])->format("H:i d.m.Y ") }}</td>
                                <td class="permlink">{{ $athr['permlink'] }}</td>
                                <td class="GBG">{{ $athr['GBG'] }}</td>
                                <td class="GOLOS">{{ $athr['GOLOS'] }}</td>
                                <td class="GESTS">{{ $athr['GESTS'] }}</td>
                                <td class="SG">{{ $athr['SP'] }}
                                </td>

                            </tr>

                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{  $summ_gbg}}</td>
                            <td>{{  $summ_golos}}</td>
                            <td>{{  $summ_gests}}</td>
                            <td>{{  \App\semas\GolosApi::convertToSg($summ_gests) }}</td>
                        </tr>
                    </TABLE>

                </div>
            </div>
        </div>
    </div>
@endif