@extends('layouts.master')

@section('title', 'Coupon Management')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        {{-- Subheader --}}
        <div class="subheader subheader-solid" id="kt_subheader" style="background: #fdf2f2;">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                    <h4 class="text-danger font-weight-bold my-2 mr-5">
                        <i class="flaticon2-percentage text-danger mr-2"></i> Coupon Code Management
                    </h4>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-5">
            <div class="card card-custom shadow-sm">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">Coupon List</span>
                        <span class="text-muted mt-3 font-weight-bold font-size-sm">Manage your promotional codes and discounts</span>
                    </h3>
                    <div class="card-toolbar">
                        <a class="btn btn-danger font-weight-bolder" href="{{ route('admin.coupons.create') }}">
                            <span class="fa fa-plus-circle mr-2"></span> Create New Coupon
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-custom alert-light-success fade show mb-5" role="alert">
                            <div class="alert-icon"><i class="flaticon2-check-mark"></i></div>
                            <div class="alert-text">{{ session('success') }}</div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="data_table" class="table table-head-custom table-vertical-center border-0">
                            <thead>
                                <tr class="text-left text-uppercase">
                                    <th style="min-width: 150px">Coupon Details</th>
                                    <th>Coupon Code</th>
                                    <th>Fare Condition</th>
                                    <th>Discount</th>
                                    <th>Validity Period</th>
                                    <th>Status</th>
                                    <th class="text-right" style="min-width: 100px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($coupons as $item)
                                    <tr>
                                        {{-- Coupon Name --}}
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40 symbol-light-danger mr-3">
                                                    <span class="symbol-label">
                                                        <i class="flaticon-gift text-danger"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $item->offer_name }}</a>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Coupon Code with Copy Style --}}
                                        <td>
                                            <span class="label label-lg label-light-danger label-inline font-weight-bold py-4" 
                                                  style="border: 1px dashed #f64e60; font-family: monospace; letter-spacing: 1px;">
                                                {{ $item->coupon_code }}
                                            </span>
                                        </td>

                                        {{-- Minimum Fare Condition --}}
                                        <td>
                                            <span class="text-dark-75 font-weight-bolder d-block font-size-sm">Min. Fare</span>
                                            <span class="text-muted font-weight-bold">৳ {{ number_format($item->min_fare) }}</span>
                                        </td>

                                        {{-- Discount Amount --}}
                                        <td>
                                            <span class="text-danger font-weight-bolder font-size-h4">
                                                ৳ {{ number_format($item->discount_amount) }}
                                            </span>
                                        </td>

                                        {{-- Validity Dates --}}
                                        <td>
                                            <div class="text-dark-75 font-weight-bolder font-size-sm">
                                                {{ $item->start_date->format('d M, Y') }}
                                            </div>
                                            <div class="text-muted font-weight-bold">to {{ $item->end_date->format('d M, Y') }}</div>
                                        </td>

                                        {{-- Active/Inactive Toggle Style --}}
                                        <td>
                                            @if($item->is_active)
                                                <span class="label label-lg label-light-success label-inline font-weight-bold">Active</span>
                                            @else
                                                <span class="label label-lg label-light-dark label-inline font-weight-bold text-muted">Expired</span>
                                            @endif
                                        </td>

                                        {{-- Actions --}}
                                        <td class="text-right">
                                            <a href="{{ route('admin.coupons.edit', $item->id) }}" 
                                               class="btn btn-icon btn-light btn-hover-primary btn-sm mx-1" title="Edit Coupon">
                                                <span class="svg-icon svg-icon-md svg-icon-primary">
                                                    <i class="far fa-edit"></i>
                                                </span>
                                            </a>
                                            
                                            <form action="{{ route('admin.coupons.destroy', $item->id) }}" method="POST"
                                                  style="display:inline-block;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-light btn-hover-danger btn-sm" 
                                                        title="Delete Coupon"
                                                        onclick="return confirm('Delete this coupon code permanently?')">
                                                    <span class="svg-icon svg-icon-md svg-icon-danger">
                                                        <i class="far fa-trash-alt"></i>
                                                    </span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-10 text-muted">
                                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="opacity-20 mb-3"><br>
                                            No Coupons Available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection