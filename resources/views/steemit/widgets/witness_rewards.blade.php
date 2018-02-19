@if (($data->isNotEmpty()))
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
                                <td>{{ $data->where('timestamp','>=',\Jenssegers\Date\Date::now()->subDays(7))->sum('VESTS') }}</td>
                                <td>{{ \App\semas\SteemitApi::convertToSg($data->where('timestamp','>=',\Jenssegers\Date\Date::now()->subDays(7))->sum('VESTS')) }}</td>
                            </tr>
                            <tr>
                                <td>Last 30 days</td>
                                <td>{{ $data->where('timestamp','>=',\Jenssegers\Date\Date::now()->subDays(30))->sum('VESTS') }}</td>
                                <td>{{ \App\semas\SteemitApi::convertToSg($data->where('timestamp','>=',\Jenssegers\Date\Date::now()->subDays(30))->sum('VESTS')) }}</td>
                            </tr>
                            <tr>
                                <td>Whole witness time</td>
                                <td>{{ $data->sum('VESTS') }}</td>
                                <td>{{ \App\semas\SteemitApi::convertToSg($data->sum('VESTS')) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endif