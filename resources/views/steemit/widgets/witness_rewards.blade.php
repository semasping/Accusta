@if (($summs['all']!=0))
    <div class="container-fluid">
        <div class="row">

                <div class="panel panel-default">
                    <div class="panel-heading">Witness Rewards statistics for {{'@'.$acc}}</div>
                    <div class="panel-body">
                        {!! $chartRewardsSP->render() !!}
                    </div>
                </div>


        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">Witness rewards</div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Time</th>
                                <th>Vests</th>
                                <th>SteemPower (Calculated for current <b>steem_per_mvests</b> )</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($summs_by_monthes as $month)
                                <tr>
                                    <td>{{ $month['date'] }}</td>
                                    <td>{{ $month['value'] }}</td>
                                    <td>~ {{ \App\semas\SteemitApi::convertToSg($month['value']) }}</td>
                                </tr>
                            @endforeach
                            <tr>


                                <td>Whole witness time</td>
                                <td>{{ $summs['all'] }}</td>
                                <td>~ {{ \App\semas\SteemitApi::convertToSg($summs['all']) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endif
