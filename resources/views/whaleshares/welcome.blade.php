<body>
<div class="flex-center position-ref full-height">
    @if (Route::has('login1'))
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
            <a href="#">Account statistics service for Whaleshares Blockchain </a>
        </div>

        <div class="text">
            <h3>In this service you can see:</h3>
            <p>Weekly and monthly data grouping: </p>
            <ul>
                <li> * Posts</li>
                <li> * Author Rewards</li>
                <li> * Curations Rewards</li>
            </ul>

            <p>Whaleshares Power down statistics </p>
            <p>Full history of Account wallet with filters</p>
            <p>Witness rewards</p>


        </div>
        {!! Form::open(array('url' => url('_form_submit'), 'class' => 'form-inline', 'method' => 'get')) !!}
        <div class="form-group">
            {!! Form::label('Account Name: @') !!}
            {!! Form::text('acc', null,
                array('required','class'=>'form-control','placeholder'=>'Account name')) !!}
            {!! Form::hidden('controller', 'AuthorRewardsController@showAll') !!}

        </div>
        {!! Form::submit('View statistics',  array('class'=>'btn btn-primary', 'data-after-submit-value'=>"Loading transactions. It`s takes for a while&hellip;")) !!}
        {!! Form::close() !!}
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <div class="row links ">
                <center>
                    <p class="text-muted links">Developed by:<a href="https://whaleshares.io/@semasping">@semasping</a>. </p>

                </center>
            </div>
        </div>
    </footer>
</div>

</body>