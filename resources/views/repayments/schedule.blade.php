@extends('layouts.app')
@section('page-title')
    {{ __('Repayment schedule') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">
                {{ __('Dashboard') }}
            </a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">{{ __('Repayment schedule') }}</a>
        </li>
    </ul>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card table-card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>
                                {{ __('Repayment schedule') }}
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover advance-datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Loan No.') }}</th>
                                    <th>{{ __('Payment Date') }}</th>
                                    <th>{{ __('Principal amount') }}</th>
                                    <th>{{ __('Interest') }}</th>
                                    <th>{{ __('Panality') }}</th>
                                    <th>{{ __('Total Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    @if (Gate::check('delete repayment schedule'))
                                        <th>{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($schedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->Loans ? loanPrefix() . $schedule->Loans->loan_id : 0 }} </td>
                                        <td>{{ dateFormat($schedule->due_date) }} </td>
                                        <td>{{ priceFormat($schedule->installment_amount) }} </td>
                                        <td>{{ priceFormat($schedule->interest) }} </td>
                                        <td>{{ $schedule->Panality ? priceFormat($schedule->Panality) : 0.0 }} </td>
                                        <td>{{ priceFormat($schedule->total_amount) }} </td>
                                        <td>
                                            @if ($schedule->status == 'Pending')
                                                <span class="badge badge-warning">{{ $schedule->status }}</span>
                                            @else
                                                <span class="badge badge-success">{{ $schedule->status }}</span>
                                            @endif
                                        </td>
                                        @if (Gate::check('delete repayment schedule'))
                                            <td>
                                                <div class="cart-action">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['repayment.schedules.destroy', $schedule->id]]) !!}
                                                    @if (Gate::check('delete repayment schedule'))
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
