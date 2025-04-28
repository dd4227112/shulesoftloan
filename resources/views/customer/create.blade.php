@extends('layouts.app')
@section('page-title')
    {{ __('Customer Create') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('customer.index') }}">{{ __('Customer') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">{{ __('Create') }}</a>
        </li>
    </ul>
@endsection
@section('content')
    {{ Form::open(['url' => 'customer', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
    <div class="row">
        <div class="col-xl-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>{{ customerPrefix() . $customerNumber }}</h4>
                    <p class="alert alert-info">
            
                        customer code exists in ShuleSoft system. By entering these key details, we will send verification details
                        if this customer exists in respective shulesoft system, and if exists, we will load all details from customer system, once customer approve the request
                    </p>
                  </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6 col-lg-6">
                            {{ Form::label('branch_id', __('Branch'), ['class' => 'form-label']) }}
                            {!! Form::select('branch_id', $branch, old('branch_id'), [
                                'class' => 'form-control  basic-select hidesearch',
                                'required' => 'required',
                            ]) !!}
                        </div>
                        <div class="form-group col-md-6 col-lg-6">
                            {{ Form::label('name', __('C_Name'), ['class' => 'form-label']) }}
                            {{ Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => __('Enter Unique Customer Code'), 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6 col-lg-6">
                            {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
                            {{ Form::text('email', old('email'), ['class' => 'form-control', 'placeholder' => __('Enter email'), 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6 col-lg-6">
                            {{ Form::label('password', __('Password'), ['class' => 'form-label']) }}
                            {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter password'), 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6 col-lg-6">
                            {{ Form::label('dob', __('Date of Birth'), ['class' => 'form-label']) }}
                            {{ Form::date('dob', old('dob'), ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6 col-lg-6">
                            {{ Form::label('phone_number', __('Phone Number'), ['class' => 'form-label']) }}
                            {{ Form::text('phone_number', old('phone_number'), ['class' => 'form-control', 'placeholder' => __('Enter phone number'), 'required' => 'required']) }}
                        </div>

                        <div class="form-group col-md-6 col-lg-6">
                            {{ Form::label('gender', __('Gender'), ['class' => 'form-label']) }}
                            {!! Form::select('gender', $gender, old('gender'), [
                                'class' => 'form-control  basic-select hidesearch',
                                'required' => 'required',
                            ]) !!}
                        </div> 

                        <div class="form-group col-md-6 col-lg-6">
                            {{ Form::label('marital_status', __('Marital Status'), ['class' => 'form-label']) }}
                            {!! Form::select('marital_status', $maritalStatus, old('marital_status'), [
                                'class' => 'form-control  basic-select hidesearch',
                                'required' => 'required',
                            ]) !!}
                        </div> 
                        <div class="form-group col-md-6 col-lg-6">
                            {{ Form::label('country', __('County'), ['class' => 'form-label']) }}
                            {{ Form::text('country', old('country'), ['class' => 'form-control', 'placeholder' => __('Enter your country'), 'required' => 'required']) }}
                        </div>

                        <div class="form-group col-md-6 col-lg-6">
                            {{ Form::label('zip_code', __('Zip Code'), ['class' => 'form-label']) }}
                            {{ Form::text('zip_code', old('zip_code'), ['class' => 'form-control', 'placeholder' => __('Zip Code'), 'required' => 'required']) }}
                        </div>

                        <div class="form-group col-md-6 col-lg-12">
                            {{ Form::label('address', __('Address'), ['class' => 'form-label']) }}
                            {{ Form::text('address', old('address'), ['class' => 'form-control', 'placeholder' => __('Enter address'), 'required' => 'required']) }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Guarantor Detail') }}</h4>
                    <p class="alert alert-info">
                        Guarantor details must be a senior organization that oversee this customer, but also must first accept concert to guarantee customer loans
                    </p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6 col-lg-6">
                            {{ Form::label('profession', __('Name'), ['class' => 'form-label']) }}
                            {{ Form::text('company', old('company'), ['class' => 'form-control', 'placeholder' => __('Enter Guarantor Name'), 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6 col-lg-6">
                            {{ Form::label('company', __('Profession'), ['class' => 'form-label']) }}
                            {{ Form::text('profession', old('profession'), ['class' => 'form-control', 'placeholder' => __('Enter GuarantorOrganization Professional'), 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6 col-lg-6">
                            {{ Form::label('city', __('City'), ['class' => 'form-label']) }}
                            {{ Form::text('city', old('city'), ['class' => 'form-control', 'placeholder' => __('Enter Guarantor city'), 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6 col-lg-6">
                            {{ Form::label('state', __('State'), ['class' => 'form-label']) }}
                            {{ Form::text('state', old('state'), ['class' => 'form-control', 'placeholder' => __('Enter guarantee state'), 'required' => 'required']) }}
                        </div>
                      

                        <div class="form-group col-md-6 col-lg-12">
                            {{ Form::label('notes', __('Note'), ['class' => 'form-label']) }}
                            {{ Form::textarea('notes', old('notes'), ['class' => 'form-control', 'placeholder' => __('Enter notes'), 'rows' => 1]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-md-12 document">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Document') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row document_list">
                        <div class="form-group col-md-3 col-lg-3">
                            {{ Form::label('document_type[]', __('Document Type'), ['class' => 'form-label']) }}
                            {!! Form::select('document_type[]', $documentTypes, old('document_type'), [
                                'class' => 'form-control  ',
                            ]) !!}
                        </div>
                        <div class="form-group col-md-3 col-lg-3">
                            {{ Form::label('document[]', __('document'), ['class' => 'form-label']) }}
                            {{ Form::file('document[]', ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-2 col-lg-2">
                            {{ Form::label('document_status[]', __('status'), ['class' => 'form-label']) }}
                            {!! Form::select('document_status[]', $document_status, old('document_status'), [
                                'class' => 'form-control  ',
                            ]) !!}
                        </div>
                        <div class="form-group col">
                            {{ Form::label('description[]', __('description'), ['class' => 'form-label']) }}
                            {{ Form::textarea('description[]', old('description'), ['class' => 'form-control', 'placeholder' => __('Enter description'), 'rows' => '1']) }}
                        </div>
                        <div class="col-auto m-auto">
                            <a href="javascript:void(0)" class="fs-2 text-danger document_list_remove btn-sm "> <i
                                    class="ti ti-trash"></i></a>
                        </div>
                    </div>
                    <div class="document_list_results"></div>
                    <div class="row ">
                        <div class="col-sm-12">
                            <a href="javascript:void(0)" class="btn btn-secondary btn-xs document_clone text-white"> <i class="ti ti-circle-plus align-text-bottom"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row ">
        <div class="form-group text-end">
            {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary ml-10']) }}
        </div>
    </div>
    {{ Form::close() }}
@endsection

@push('script-page')
    <script>
        $('.document').on('click', '.document_list_remove', function() {
            if ($('.document_list').length > 1) {
                $(this).parent().parent().remove();
            }
        });
        $('.document').on('click', '.document_clone', function() {
            var clonedCocument = $('.document_clone').closest('.document').find('.document_list').first().clone();
            clonedCocument.find('input[type="text"]').val('');
            $('.document_list_results').append(clonedCocument);
        });

        $('.document').on('click', '.document_list_remove', function() {
            var id = $(this).data('val');
        });
    </script>
@endpush
