<body>
<div class="flex-center position-ref full-height">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                    @endauth
        </div>
    @endif



    <div class="content">
        <div class="title m-b-md">
            <img src="/images/accusta_logo.png" title="Accusta">
        </div>

        <div class="links">
            <a href="#">Сервис статистики аккаунтов в блокчейне Голос</a>
        </div>

        <div class="text">
            <h3>Возможности сервиса:</h3>
            <p>Статистика понижения Силы голоса</p>
            <p>Фильтр истории транзакций</p>
            <p>Группировка данных по месяцам или неделям:</p>
            <ul>
                <li> * Посты</li>
                <li> * Выплаты за посты</li>
                <li> * Кураторские</li>
            </ul>


        </div>
        {!! Form::open(array('url' => url('_form_submit'), 'class' => 'form-inline', 'method' => 'get')) !!}
        <div class="form-group">
            {!! Form::label('Account Name: @') !!}
            {!! Form::text('acc', null,
                array('required','class'=>'form-control','placeholder'=>'Account name')) !!}
            {!! Form::hidden('controller', 'AuthorRewardsController@showAll') !!}

        </div>
        {!! Form::submit('Начать просмотр',  array('class'=>'btn btn-primary', 'data-after-submit-value'=>"Собираю транзакции. Это может занять некоторое время. Ждите&hellip;")) !!}
        {!! Form::close() !!}
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row links ">
                <center>
                    <p class="text-muted links">Разработкой сервиса занят:<a href="https://golos.io/@semasping">@semasping</a>. </p>


                    <p class="links"> Проголосовать за <b>Делегата Semasping</b> вы можете <a href="https://golos.io/~witnesses">https://golos.io/~witnesses</a> или <a href="https://goldvoice.club/witnesses/">https://goldvoice.club/witnesses/</a></p>

                </center>
            </div>
        </div>
    </footer>
</div>

</body>