@extends('layouts.master')

@section('title', 'Offer Management')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        {{-- Subheader --}}
        <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                    <h4 class="text-success font-weight-bold my-2 mr-5">
                        Offer Management
                    </h4>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-5">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Offer List</h3>
                    <div class="card-toolbar">
                        <a class="btn btn-outline-primary" href="{{ route('admin.offers.create') }}">
                            <span class="fa fa-plus"></span> Add New Offer
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table id="data_table" class="table table-bordered table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Offer Name</th>
                                    <th>Target Selection</th>
                                    <th>Fare Range (৳)</th>
                                    <th>Discount (৳)</th>
                                    <th>Validity</th>
                                    <th>Status</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($offers as $item)
                                    <tr class="{{ $item->is_active ? '' : 'bg-light text-muted' }}">
                                        <td>
                                            <span class="font-weight-bolder text-dark">{{ $item->offer_name }}</span>
                                        </td>

                                        {{-- Target Selection Display --}}
                                        <td>
                                            @if($item->schedule)
                                                <span class="label label-inline label-light-primary font-weight-bold">Coach:
                                                    {{ $item->schedule->name }}</span><br>
                                            @endif
                                            @if($item->route)
                                                <span class="text-muted small">Route: {{ $item->route->name }}</span><br>
                                            @endif
                                            @if($item->bus_type)
                                                <span class="badge badge-info small">{{ $item->bus_type }}</span>
                                            @else
                                                <span class="badge badge-secondary small">All Types</span>
                                            @endif
                                        </td>

                                        <td>{{ number_format($item->min_fare) }} - {{ number_format($item->max_fare) }}</td>

                                        <td class="text-danger font-weight-bold">
                                            ৳ {{ number_format($item->discount_amount) }}
                                        </td>

                                        <td class="small text-muted">
                                            {{ $item->start_date->format('d M, Y') }} - <br>
                                            {{ $item->end_date->format('d M, Y') }}
                                        </td>

                                        <td>
                                            <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-danger' }}">
                                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            <a class="btn btn-icon btn-sm btn-outline-primary"
                                                href="{{ route('admin.offers.edit', $item->id) }}" title="Edit">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.offers.delete', $item->id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-icon btn-sm btn-outline-danger" title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this offer?')">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted font-italic">No Offers Found</td>
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