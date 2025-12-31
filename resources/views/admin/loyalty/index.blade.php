@extends('layouts.master')

@section('title', 'Loyalty Discount List')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        {{-- Subheader --}}
        <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                    <h4 class="text-success font-weight-bold my-2 mr-5">
                        Loyalty Discounts
                    </h4>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Loyalty Discount List</h3>
                    <div class="card-toolbar">
                        <a class="btn btn-outline-primary" href="{{ route('admin.loyalty.create') }}">
                            <span class="fa fa-plus"></span> Add New Discount
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive-lg">
                        <table id="data_table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Days Threshold</th>
                                    <th>Discount (৳)</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($discounts as $item)
                                    <tr class="{{ $item->is_active ? '' : 'bg-light text-muted' }}">
                                        <td>{{ $item->days_threshold }} days</td>
                                        <td>{{ $item->discount_amount }}</td>
                                        <td>{{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d-m-Y') : '-' }}
                                        </td>
                                        <td>{{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('d-m-Y') : '-' }}
                                        </td>
                                        <td>
                                            @if($item->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a title="Edit" class="btn btn-icon btn-sm btn-outline-primary"
                                                href="{{ route('admin.loyalty.edit', $item->id) }}">
                                                <i class="far fa-edit"></i>
                                            </a>

                                            <form method="POST" action="{{ route('admin.loyalty.delete', $item->id) }}"
                                                style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button title="Delete" class="btn btn-icon btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </form>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-danger">No Discounts Found</td>
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