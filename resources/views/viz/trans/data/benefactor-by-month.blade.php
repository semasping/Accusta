<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="heading{{ $type.$k }}">
        <h4 class="panel-title">
            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
               href="#collapse{{ $type.$k }}" aria-expanded="false" aria-controls="collapse{{ $type.$k }}">
                <div class="">{!! $m !!}: {!! $data['total'][$k] !!} shares</div>
            </a>
        </h4>
    </div>
    <div id="collapse{{ $type.$k }}" class="panel-collapse collapse" role="tabpanel"
         aria-labelledby="heading{{ $type.$k }}" data-month="{{ $data['date'][$k] }}" data-type="{{ $type }}"
         data-href="/{{ '@'.$acc }}/benefactor/by_month/{{$type}}/{{$data['date'][$k]}}/">
        <div class="panel-body" id="body{{ $type.$data['date'][$k] }}">

            <table id="data{{ $type.$data['date'][$k] }}">
                <thead>
                <tr>
                    @if($type=='In')
                        <td>Автор</td>
                    @endif
                    @if($type=='Out')
                        <td>Кому</td>
                    @endif
                    <td>Permlink</td>
                    <td class="sum">SHARES</td>
                    <td>Timestamp</td>
                </tr>
                </thead>
                <tbody>

                </tbody>
                <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('js2')
    <script>
        $('#data{{ $type.$data["date"][$k] }}').DataTable({
            processing: true,
            responsive: true,
            serverSide: false,
            paging: true,

            columns: [
                {data: 'author'},
                {data: 'permlink'},
                {data: 'VESTS'},
                {data: 'timestamp'}
            ],
            "order": [[3, "desc"]],
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api();

                api.columns('.sum', {
                    "filter": "applied"
                }).every(function () {
                    var sum = this
                        .data()
                        .reduce(function (a, b) {
                            var x = parseFloat(a) || 0;
                            var y = parseFloat(b) || 0;
                            return (x + y).toFixed(3);
                        }, 0);
                    //console.log(sum); //alert(sum);
                    $(this.footer()).html(sum);
                });
            }
        });
    </script>
@endpush
