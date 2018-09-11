<table class="table-bordered table-hover table-condensed">
    <thead>
    <tr>
        <th>От</th>
        <th>Сумма</th>
        <th>Дата</th>
        <th>Дней работы</th>
        <th>Процент</th>
        <th>Итого</th>

    </tr>
    </thead>
    <tbody>
@foreach($data as $item)
    <tr>
        <td>{{ $item['from'] }}</td>
        <td>{{ $item['amount'] }}</td>
        <td>{{ $item['date'] }}</td>
        <td>{{ $item['date_diff'] }}</td>
        <td>{{ $item['perc'] }}</td>
        <td>{{ $item['itog'] }}</td>

    </tr>
@endforeach
    </tbody>
</table>
Итого:{{ $sum }}<br>
На аккануте:
{{ $sp }} Сила Голоса<br>
{{ $golos }}<br>
{{ $gbg }}

