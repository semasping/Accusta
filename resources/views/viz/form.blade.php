{!! Form::open(array('url' => url('_form_submit'), 'class' => 'form-inline', 'method' => 'get')) !!}

<div class="form-group">
    {!! Form::label('Account Name: @') !!}
    {!! Form::text('acc', $account,
        array('required','class'=>'form-control','placeholder'=>'Account name')) !!}
    {!! Form::hidden('controller', $form_action) !!}

</div>
{!! Form::submit('Посмотреть транзакции',  array('class'=>'btn btn-primary', 'data-after-submit-value'=>"Собираю транзакции. Это может занять некоторое время. Ждите&hellip;")) !!}
{{--            {!! Form::label('Рассчеты для Vox-Populi') !!}
            {!! Form::checkbox('vp', 'on') !!}--}}
{!! Form::close() !!}