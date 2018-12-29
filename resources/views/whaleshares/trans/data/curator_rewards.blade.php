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
                    <a data-toggle="collapse" href="#сrw_{{$key}}"> Curation rewards:</a>
                </h4>
            </div>
            <div id="сrw_{{$key}}" class="panel-collapse collapse">
                <div class="panel-body">

                    <TABLE class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="timestamp">Date</th>
                            <th class="author">Author</th>
                            <th class="permlink">Permlink</th>
                            <th class="GESTS">VESTS</th>
                            <th class="SG">SP</th>
                        </tr>
                        </thead>
                        @foreach($cur as $currwds)
                            <?php
                            $summ_gests = $summ_gests+$currwds['VESTS'];
                            ?>
                            <tr>
                                <td class="timestamp">{{  Date::parse($currwds['timestamp'])->format("H:i d.m.Y ") }}</td>
                                <td class="author">{{ $currwds['author'] }}</td>
                                <td class="permlink">{{ $currwds['permlink'] }}</td>
                                <td class="GESTS">{{ $currwds['VESTS'] }}</td>
                                <td class="SG">{{ \App\semas\SteemitApi::convertToSg($currwds['VESTS']) }}</td>

                            </tr>

                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{  $summ_gests}}</td>
                            <td>{{  \App\semas\SteemitApi::convertToSg($summ_gests) }}</td>
                        </tr>
                    </TABLE>

                </div>
            </div>
        </div>
    </div>
@endif