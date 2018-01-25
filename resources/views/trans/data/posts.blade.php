@if ($posts->count()>0)


    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" href="#posts_{{$key}}"> Посты:</a>
                </h4>
            </div>
            <div id="posts_{{$key}}" class="panel-collapse collapse">
                <div class="panel-body">

                    <TABLE class="table table-bordered">
                        <thead><tr>
                            <th class="timestamp">Дата</th>
                            <th class="permlink">permlink</th>
                        </tr></thead>
                        @foreach($posts as $pst)
                            <tr>
                                <td class="timestamp">{{  Date::parse($pst['timestamp'])->format("H:i d.m.Y ") }}</td>
                                <td class="permlink">{{ $pst['permlink'] }}</td>
                            </tr>
                            {{--<tr>
                                <td></td>
                                <td>@include('trans.data.author_rewards')</td>
                            </tr>--}}

                        @endforeach
                    </TABLE>

                </div>
            </div>
        </div>
    </div>
@endif