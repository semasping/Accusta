{!! Form::open(array('url' => url('_form_submit'), 'class' => 'form-inline', 'method' => 'get')) !!}
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Выбрать дату</h4>
            </div>
            <div class="modal-body">
                {!! Form::date('d_from', $date) !!}
                <br>Выберите нужную вам дату в после сверху.
                <br>Чтобы отменить выбор даты нажмите на крестик.
                <br>Затем "закрыть выбор" и "посмотреть транзакции"
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть выбор</button>
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
{!! Form::submit('Посмотреть транзакции',  array('class'=>'btn btn-primary', 'data-after-submit-value'=>"Собираю транзакции. Это может занять некоторое время. Ждите&hellip;")) !!}
{{--            {!! Form::label('Рассчеты для Vox-Populi') !!}
            {!! Form::checkbox('vp', 'on') !!}--}}
{!! Form::close() !!}