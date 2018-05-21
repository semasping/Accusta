<ul class="nav navbar-nav">
    <li>
        <a class="navbar-brand" href="{{ route('welcome') }}">
            <img class="logo" src="/images/accusta_logo_line_170.png">
        </a>
    </li>
    @if(Route::has('trans_by_month'))
        <li {{ Route::is('trans_by_month')  ? ' class="active"' : null }}>
            <a href="{{ route('trans_by_month',[''.$account]) }}">
                Статистика аккаунта по месяцам
            </a>
        </li>
    @endif
    @if(Route::has('trans_by_week'))
        <li {{ Route::is('trans_by_week')  ? ' class=active' : null }}>
            <a href="{{ route('trans_by_week',[''.$account]) }}">
                Статистика аккаунта по неделям
            </a></li>
    @endif
    @if(Route::has('trans_history'))
        <li {{ Route::is('trans_history')  ? ' class=active' : null }}>

        <a href="{{ route('trans_history',[''.$account]) }}">
                История транзакций
            </a></li>
    @endif
    @if(Route::has('trans_sg'))
        <li {{ Route::is('trans_sg')  ? ' class=active' : null }}>

        <a href="{{ route('trans_sg',[''.$account]) }}">
                Статистика понижения СГ
            </a></li>
    @endif
    @if(Route::has('witness_votes'))
        <li {{ Route::is('witness_votes')  ? ' class=active' : null }}>

        <a href="{{ route('witness_votes',[''.$account]) }}">
                Голосование за делегатов
            </a></li>
    @endif
    @if(Route::has('trans_benefactor'))
        <li {{ Route::is('trans_benefactor')  ? ' class=active' : null }}>
            <a href="{{ route('trans_benefactor',[''.$account]) }}">
                Статистика Бенефециарских вознаграждений
            </a></li>
    @endif
    @if(Route::has('trans_curator'))
        <li {{Route::is('trans_curator')  ? ' class=active' : null }}>

        <a href="{{ route('trans_curator',[''.$account]) }}">
                Статистика Кураторских вознаграждений
            </a></li>
    @endif
</ul>