@if (!empty($summs))
    <div class="container">
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
                                <th>SteemPower</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Last 7 days</td>
                                <td>{{ $summs['days7'] }}</td>
                                <td>{{ \App\semas\SteemitApi::convertToSg($summs['days7']) }}</td>
                            </tr>
                            <tr>
                                <td>Last 30 days</td>
                                <td>{{ $summs['days30'] }}</td>
                                <td>{{ \App\semas\SteemitApi::convertToSg($summs['days30']) }}</td>
                            </tr>
                            <tr>
                                <td>Whole witness time</td>
                                <td>{{ $summs['all'] }}</td>
                                <td>{{ \App\semas\SteemitApi::convertToSg($summs['all']) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endif