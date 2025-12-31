@extends('layouts.master')

@section('title', 'Station List')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        {{-- Subheader --}}
        <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                    <h4 class="text-success font-weight-bold my-2 mr-5">
                        Station
                        <small></small>
                    </h4>
                </div>
            </div>
        </div>
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="card card-custom">
                    <div class="card-header">
                        <h3 class="card-title">Station List</h3>
                        <div class="card-toolbar">
                            <a class="btn btn-outline-primary" href="{{ route('admin.stations.create') }}">
                                <span class="fa fa-plus"></span> Create New Station
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="table-responsive-lg">
                            <table id="data_table" class="table table-bordered table-striped dataTable">
                                <thead>
                                    <tr>
                                        <th style="width: 50%;">Name</th>
                                        <th style="width: 25%;">Status</th>
                                        <th class="text-center" style="width: 25%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($stations as $station)
                                        <tr role="row" class="{{ $loop->even ? 'even' : 'odd' }}">
                                            <td class="sorting_1">{{ $station->name }}</td>
                                            <td class="text-capitalize">
                                                @if($station->status == 'active')
                                                    <span
                                                        class="label label-inline label-light-success font-weight-bold">Active</span>
                                                @else
                                                    <span
                                                        class="label label-inline label-light-danger font-weight-bold">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{-- Edit Button --}}
                                                <a title="Edit" class="btn btn-icon btn-sm btn-outline-primary"
                                                    href="{{ route('admin.stations.edit', $station->id) }}">
                                                    <i class="far fa-edit"></i>
                                                </a>

                                                {{-- Delete Button (Secure DELETE Form) --}}
                                                <form method="POST" action="{{ route('admin.stations.destroy', $station->id) }}"
                                                    style="display: inline;"
                                                    onsubmit="return confirm('Are you sure you want to delete station {{ $station->name }}?');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" title="Delete Station"
                                                        class="btn btn-icon btn-sm btn-outline-danger">
                                                        <i class="far fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No stations found.</td>
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