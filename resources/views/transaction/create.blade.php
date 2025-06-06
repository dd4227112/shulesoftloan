{{ Form::open(['url' => 'transaction', 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">
        <input type="hidden" name="account" id="account">
        <div class="form-group col-md-12">
            {{ Form::label('customer', __('Customer'), ['class' => 'form-label']) }}
            {!! Form::select('customer', $customers, null, [
                'class' => 'form-control hidesearch',
                'required' => 'required',
            ]) !!}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('account_number', __('Account Number'), ['class' => 'form-label']) }}
            {{ Form::text('account_number', null, ['class' => 'form-control', 'placeholder' => __('Enter account number'), 'readonly']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}
            {!! Form::select('type', $type, null, [
                'class' => 'form-control hidesearch',
                'required' => 'required',
            ]) !!}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
            {!! Form::select('status', $status, null, [
                'class' => 'form-control hidesearch',
                'required' => 'required',
            ]) !!}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('date_time', __('Date Time'), ['class' => 'form-label']) }}
            {{ Form::datetimeLocal('date_time', null, ['class' => 'form-control', 'placeholder' => __('Enter date time')]) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}
            {{ Form::number('amount', null, ['class' => 'form-control', 'step' => 0.1, 'placeholder' => __('Enter amount')]) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}
            {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => '1', 'placeholder' => __('Enter notes')]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}
