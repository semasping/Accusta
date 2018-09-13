<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/><link href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.12/css/dataTables.bootstrap.min.css" rel="stylesheet"/>
<div class="container-fluid">
    <div class="row">
        @if (!empty($voteFor))
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#v1">Account voted for witnesses</a></li>
                <li><a data-toggle="tab" href="#v1h">History</a></li>
            </ul>
            <div class="tab-content">
                <div id="v1" class="tab-pane fade in active">

                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive table-bordered">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>For witness</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($voteFor as $vote)
                                        <tr>
                                            <td>{{ $vote['date'] }}</td>
                                            <td>{{ $vote['witness'] }}
                                                <a target="_blank" href="https://golos.io/{{ '@'.$vote['witness'] }}/transfers">
                                                    <span class="glyphicon glyphicon-log-out"><img class="logo" src="/golos_icon_15.png"></span>
                                                </a>
                                            </td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="v1h" class="tab-pane fade">

                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive table-bordered">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>For witness</th>
                                        <th>Approve/Disapprove</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($voteForHistory as $vote)
                                        <tr>
                                            <td>{{ $vote['date'] }}</td>
                                            <td>{{ $vote['witness'] }}
                                                <a target="_blank" href="https://golos.io/{{ '@'.$vote['witness'] }}/transfers">
                                                    <span class="glyphicon glyphicon-log-out"><img class="logo" src="/golos_icon_15.png"></span>
                                                </a>
                                            </td>
                                            <td>{{ (string)$vote['status'] }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="row">
        @if (!empty($forWitness))
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#v2">Votes for witness {{ $account }}</a></li>
                <li><a data-toggle="tab" href="#v2h">History</a></li>
            </ul>
            <div class="tab-content">
                <div id="v2" class="tab-pane fade in active">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table" id="table_v2">
                                    <thead>
                                    <tr>
                                        <th>Date<i class="fa fa-sort float-right" aria-hidden="true"></i></th>
                                        <th>Voted account<i class="fa fa-sort float-right" aria-hidden="true"></i></th>
                                        <th>VS<i class="fa fa-sort float-right" aria-hidden="true"></i></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($forWitness as $vote)
                                        <tr>
                                            <td>{{ $vote['date'] }}</td>
                                            <td>{{ $vote['account'] }}
                                                <a target="_blank" href="https://golos.io/{{ '@'.$vote['account'] }}/transfers">
                                                    <span class="glyphicon glyphicon-log-out"><img class="logo" src="/golos_icon_15.png"></span>
                                                </a>
                                            </td>
                                            <td>{{ $vote['power'] }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="v2h" class="tab-pane fade">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Voted account</th>
                                        <th>Approve/Disapprove</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($forWitnessHistory as $vote)
                                        <tr>
                                            <td>{{ $vote['date'] }}</td>
                                            <td>{{ $vote['account'] }}
                                                <a target="_blank" href="https://golos.io/{{ '@'.$vote['account'] }}/transfers">
                                                    <span class="glyphicon glyphicon-log-out"><img class="logo" src="/golos_icon_15.png"></span>
                                                </a>
                                            </td>
                                            <td>{{ (string)$vote['status'] }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        @endif

    </div>
</div>

<script>

    $(document).ready(function() {
        $('#table_v2').DataTable();
    });
</script>

