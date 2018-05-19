<ul class="nav navbar-nav">
    <a class="navbar-brand" href="{{ route('welcome') }}">
        <img class="logo" src="/images/accusta_logo_line_170.png">
    </a>
    @if(Route::has('trans_by_month'))
        <a class="navbar-brand" href="{{ route('trans_by_month',[''.$account]) }}">
            Статистика аккаунта по месяцам
        </a>
    @endif
    @if(Route::has('trans_by_week'))
        <a class="navbar-brand" href="{{ route('trans_by_week',[''.$account]) }}">
            Статистика аккаунта по неделям
        </a>
    @endif
    @if(Route::has('trans_history'))
        <a class="navbar-brand" href="{{ route('trans_history',[''.$account]) }}">
            История транзакций
        </a>
    @endif
    @if(Route::has('trans_sg'))
        <a class="navbar-brand" href="{{ route('trans_sg',[''.$account]) }}">
            Статистика понижения СГ
        </a>
    @endif
    @if(Route::has('witness_votes'))
        <a class="navbar-brand" href="{{ route('witness_votes',[''.$account]) }}">
            Голосование за делегатов
        </a>
    @endif
    @if(Route::has('trans_benefactor'))
        <a class="navbar-brand" href="{{ route('trans_benefactor',[''.$account]) }}">
            Статистика Бенефециарских вознаграждений
        </a>
    @endif
</ul>