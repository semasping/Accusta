<ul class="nav navbar-nav">
    <a class="navbar-brand" href="{{ route('welcome') }}">
        <img class="logo" src="/images/accusta_logo_line_170.png">
    </a>
    @if(Route::has('trans_by_month'))
        <a class="navbar-brand" href="{{ route('trans_by_month',[''.$account]) }}">
            Monthly Statistics
        </a>
    @endif
    @if(Route::has('trans_by_week'))
        <a class="navbar-brand" href="{{ route('trans_by_week',[''.$account]) }}">
            Weekly Statistics
        </a>
    @endif
    @if(Route::has('trans_history'))
        <a class="navbar-brand" href="{{ route('trans_history',[''.$account]) }}">
            Transaction History
        </a>
    @endif
    @if(Route::has('trans_sg'))
        <a class="navbar-brand" href="{{ route('trans_sg',[''.$account]) }}">
            Power Down Statistics
        </a>
    @endif
    @if(Route::has('witness_votes'))
        <a class="navbar-brand" href="{{ route('witness_votes',[''.$account]) }}">
            WItness votes Statistics
        </a>
    @endif
    @if(Route::has('trans_benefactor'))
        <a class="navbar-brand" href="{{ route('trans_benefactor',[''.$account]) }}">
            Benefactor Rewards Statistics
        </a>
    @endif
</ul>