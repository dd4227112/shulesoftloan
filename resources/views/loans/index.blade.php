@extends('layouts.app')
@section('page-title')
    {{ __('Loans') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">
                {{ __('Dashboard') }}
            </a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">{{ __('Loans') }}</a>
        </li>
    </ul>
@endsection
@section('card-action-btn')
    @if (Gate::check('create loan'))
        <a class="btn btn-secondary btn-sm ml-20" href="{{ route('loan.create') }}"> <i
                class="ti-plus mr-5"></i>{{ __('Create Loan') }}</a>
    @endif
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card table-card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>
                                {{ __('Loan') }}
                            </h5>
                        </div>
                        @if (Gate::check('create loan'))
                            <div class="col-auto">
                                <a class="btn btn-secondary btn-sm" href="{{ route('loan.create') }}">
                                    <i class="ti ti-circle-plus align-text-bottom"></i>
                                    {{ __('Create Loan') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover advance-datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Loan Type') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Start Date') }}</th>
                                    <th>{{ __('End Date') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    @if (Gate::check('edit loan') || Gate::check('show loan') || Gate::check('delete loan'))
                                        <th>{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loans as $loan)
                                    <tr>
                                        <td>{{ loanPrefix() . $loan->loan_id }} </td>
                                        <td>{{ !empty($loan->branch)?$loan->branch->name:'' }} </td>
                                        <td>{{ !empty($loan->loanType)?$loan->loanType->type:'' }} </td>
                                        <td>{{ !empty($loan->Customers)?$loan->Customers->name:'' }} </td>
                                        <td>{{ dateFormat($loan->loan_start_date) }} </td>
                                        <td>{{ dateFormat($loan->loan_due_date) }} </td>
                                        <td>{{ priceFormat($loan->amount) }} </td>
                                        <td>
                                            @if ($loan->status == 'draft')
                                                <span
                                                    class="d-inline badge text-bg-info">{{ \App\Models\Loan::$status[$loan->status] }}</span>
                                            @elseif($loan->status == 'submitted')
                                                <span
                                                    class="d-inline badge text-bg-primary">{{ \App\Models\Loan::$status[$loan->status] }}</span>
                                            @elseif($loan->status == 'under_review')
                                                <span
                                                    class="d-inline badge text-bg-warning">{{ \App\Models\Loan::$status[$loan->status] }}</span>
                                            @elseif($loan->status == 'approved')
                                                <span
                                                    class="d-inline badge text-bg-success">{{ \App\Models\Loan::$status[$loan->status] }}</span>
                                            @elseif($loan->status == 'rejected')
                                                <span
                                                    class="d-inline badge text-bg-danger">{{ \App\Models\Loan::$status[$loan->status] }}</span>
                                            @else
                                                <span
                                                    class="d-inline badge text-bg-info">{{ \App\Models\Loan::$status[$loan->status] }}</span>
                                            @endif
                                        </td>

                                        @if (Gate::check('edit loan') || Gate::check('delete loan'))
                                            <td>
                                                <div class="cart-action">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['loan.destroy', $loan->id]]) !!}
                                                    @if (Gate::check('edit loan'))
                                                        <a class="text-warning" data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Show') }}"
                                                            href="{{ route('loan.show', $loan->id) }}"> <i
                                                                data-feather="eye"></i></a>
                                                    @endif
                                                    @if (Gate::check('edit loan'))
                                                        <a class="text-success" data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Edit') }}"
                                                            href="{{ route('loan.edit', $loan->id) }}"> <i
                                                                data-feather="edit"></i></a>
                                                    @endif
                                                    @if (Gate::check('delete loan'))
                                                        <a class=" text-danger confirm_dialog" data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Detete') }}" href="#"> <i
                                                                data-feather="trash-2"></i></a>
                                                    @endif
                                                    {!! Form::close() !!}
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
