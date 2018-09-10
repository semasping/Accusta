<table class="table-bordered table-hover table-condensed">
    <thead>
    <tr>
        <th>От</th>
        <th>Сумма</th>
        <th>Дата</th>

    </tr>
    </thead>
    <tbody>
@foreach($data as $item)
    <tr>
        <td>{{ $item['from'] }}</td>
        <td>{{ $item['amount'] }}</td>
        <td>{{ $item['date'] }}</td>

    </tr>
@endforeach
    </tbody>
</table>
Итого:{{ $sum }}
