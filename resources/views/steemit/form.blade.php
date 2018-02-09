{!! Form::open(array('url' => url('_form_submit'), 'class' => 'form-inline', 'method' => 'get')) !!}
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Choose date</h4>
            </div>
            <div class="modal-body">
                {!! Form::date('d_from', $date) !!}
                <br> Select the date you want in the field above.
                <br> To cancel the date selection, click on the cross in the field above.
                <br> Then "Close" and "View statistics"
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    {!! Form::label('Account Name: @') !!}
    {!! Form::text('acc', $account,
        array('required','class'=>'form-control','placeholder'=>'Account name')) !!}
    {!! Form::hidden('controller', $form_action) !!}

</div>
{!! Form::submit('View statistics',  array('class'=>'btn btn-primary', 'data-after-submit-value'=>"Loading transactions. It`s takes for a while&hellip;")) !!}

{!! Form::close() !!}