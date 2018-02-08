@if ($cur->count()>0)
    <?php
    $summ_gbg=0;
    $summ_golos=0;
    $summ_gests=0;
    ?>
    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" href="#сrw_{{$key}}"> Кураторские:</a>
                </h4>
            </div>
            <div id="сrw_{{$key}}" class="panel-collapse collapse">
                <div class="panel-body">

                    <TABLE class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="timestamp">Дата</th>
                            <th class="author">author</th>
                            <th class="permlink">permlink</th>
                            <th class="GESTS">GESTS</th>
                            <th class="SG">СГ</th>
                        </tr>
                        </thead>
                        @foreach($cur as $currwds)
                            <?php
                            $summ_gests = $summ_gests+$currwds['GESTS'];
                            ?>
                            <tr>
                                <td class="timestamp">{{  Date::parse($currwds['timestamp'])->format("H:i d.m.Y ") }}</td>
                                <td class="author">{{ $currwds['author'] }}</td>
                                <td class="permlink">{{ $currwds['permlink'] }}</td>
                                <td class="GESTS">{{ $currwds['GESTS'] }}</td>
                                <td class="SG">{{ \App\semas\GolosApi::convertToSg($currwds['GESTS']) }}</td>

                            </tr>

                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{  $summ_gests}}</td>
                            <td>{{  \App\semas\GolosApi::convertToSg($summ_gests) }}</td>
                        </tr>
                    </TABLE>

                </div>
            </div>
        </div>
    </div>
@endif