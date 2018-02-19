@if (($data->isNotEmpty()))
    <div class="container">
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">Witness rewards for whole time</div>
                <div class="panel-body">
                    Vests:{{ $data->sum('VESTS') }} that is<br>
                    Steem Power: {{ \App\semas\SteemitApi::convertToSg($data->sum('VESTS')) }}
                </div>
            </div>
        </div>
    </div>


@endif