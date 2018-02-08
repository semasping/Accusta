@if ($transfer_out->count()>0)
<div class="panel-group">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" href="#transfer_out_{{$key}}">SBD Transfers  out from account:</a>
            </h4>
        </div>
        <div id="transfer_out_{{$key}}" class="panel-collapse collapse">
            <div class="panel-body">

                <TABLE class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="timestamp">Date</th>
                        <th class="to">To</th>
                        <th class="amount">Amount</th>
                        <th class="memo">Мемо</th>
                    </tr>
                    </thead>
                    @foreach($transfer_out as $trsfr_out)
                        <tr>
                            <td class="timestamp">{{  Date::parse($trsfr_out['timestamp'])->format("H:i d.m.Y ") }}</td>
                            <td class="to">{{ $trsfr_out['to'] }}</td>
                            <td class="amount">{{ $trsfr_out['amount'] }}</td>
                            <td class="memo">{{ $trsfr_out['memo'] }}</td>
                        </tr>

                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </TABLE>

            </div>
        </div>
    </div>
</div>
@endif