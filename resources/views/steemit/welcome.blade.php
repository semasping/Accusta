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
            <a href="#">Account statistics service for Steemit Blockchain </a>
        </div>

        <div class="text">
            <h3>In this service your can see:</h3>
            <p>Steem Power down statistics</p>
            <p>Full history of Account wallet with filters(In Process)</p>
            <p>Weekly and monthly data grouping: (In Process)</p>
            <ul>
                <li> * Posts</li>
                <li> * Author Rewards</li>
                <li> * Curations Rewards</li>
            </ul>


        </div>
        {!! Form::open(array('url' => url('_form_submit'), 'class' => 'form-inline', 'method' => 'get')) !!}
        <div class="form-group">
            {!! Form::label('Account Name: @') !!}
            {!! Form::text('acc', null,
                array('required','class'=>'form-control','placeholder'=>'Account name')) !!}
            {!! Form::hidden('controller', 'TransAccController@index') !!}

        </div>
        {!! Form::submit('Начать просмотр',  array('class'=>'btn btn-primary', 'data-after-submit-value'=>"Собираю транзакции. Это может занять некоторое время. Ждите&hellip;")) !!}
        {!! Form::close() !!}
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row links ">
                <center>
                    <p class="text-muted links">Developed by:<a href="https://steemit.com/@semasping">@semasping</a>. </p>

                </center>
            </div>
        </div>
    </footer>
</div>

</body>