{!! Form::open(array('url' => url('_form_submit'), 'class' => 'form-inline', 'method' => 'get')) !!}
<div class="form-group">
    {!! Form::label('Account Name: @') !!}
    {!! Form::text('acc', $account,
        array('required','class'=>'form-control','placeholder'=>'Account name')) !!}
    {!! Form::hidden('controller', $form_action) !!}

</div>
{!! Form::submit('View statistics',  array('class'=>'btn btn-primary', 'data-after-submit-value'=>"Loading transactions. It takes for a while&hellip;")) !!}

{!! Form::close() !!}