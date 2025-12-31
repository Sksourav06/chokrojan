@extends('layouts.master')

@section('title', 'Create New Counter')

@section('content')
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Create New Counter</h3>
                </div>

                <form method="POST" action="{{ route('admin.counters.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">

                            {{-- LEFT COLUMN --}}
                            <div class="col-md-5">

                                {{-- Counter Name --}}
                                <div class="form-group @error('name') has-error @enderror">
                                    <label for="name" class="required">
                                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Counter Name
                                    </label>
                                    <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Enter name" name="name" value="{{ old('name') }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Station --}}
                                <div class="form-group @error('station_id') has-error @enderror">
                                    <label for="station_id" class="required">
                                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Station
                                    </label>
                                    <select id="station_id" class="form-control  @error('station_id') is-invalid @enderror"
                                        name="station_id" required data-live-search="true" data-size="10">
                                        <option value="">Select Station...</option>
                                        @foreach($stations as $id => $name)
                                            <option value="{{ $id }}" {{ old('station_id') == $id ? 'selected' : '' }}>{{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('station_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Status --}}
                                <div class="form-group @error('status') has-error @enderror">
                                    <label for="status" class="required">
                                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Status
                                    </label>
                                    <select id="status" class="form-control  @error('status') is-invalid @enderror"
                                        name="status" required>
                                        @foreach($availableStatuses as $statusOption)
                                            <option value="{{ $statusOption }}" {{ old('status', 'active') == $statusOption ? 'selected' : '' }}>
                                                {{ ucfirst($statusOption) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Credit Limit --}}
                                <div class="form-group @error('credit_limit') has-error @enderror">
                                    <label for="credit_limit" class="required">
                                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Credit Limit (৳)
                                    </label>
                                    <input type="number" step="0.01" id="credit_limit"
                                        class="form-control @error('credit_limit') is-invalid @enderror"
                                        placeholder="Enter limit amount" name="credit_limit"
                                        value="{{ old('credit_limit', 0) }}" required>
                                    @error('credit_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Counter Type --}}
                                <div class="form-group @error('counter_type') has-error @enderror">
                                    <label for="counter_type" class="required">
                                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Counter Type
                                    </label>
                                    <select id="counter_type"
                                        class="form-control  @error('counter_type') is-invalid @enderror"
                                        name="counter_type" required>
                                        <option value="Own" {{ old('counter_type') == 'Own' ? 'selected' : '' }}>Own</option>
                                        <option value="Commission" {{ old('counter_type') == 'Commission' ? 'selected' : '' }}>Commission</option>
                                    </select>
                                    @error('counter_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Permitted Credit --}}
                                <div class="form-group @error('permitted_credit') has-error @enderror">
                                    <label for="permitted_credit">Counter Permitted Credit (৳)</label>
                                    <input type="number" step="0.01" id="permitted_credit"
                                        class="form-control @error('permitted_credit') is-invalid @enderror"
                                        placeholder="Enter amount" name="permitted_credit"
                                        value="{{ old('permitted_credit', 0) }}">
                                    @error('permitted_credit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- RIGHT COLUMN: Commission --}}
                            <div class="col-md-7" id="commission_settings_column"
                                style="display: {{ old('counter_type') == 'Commission' ? 'block' : 'none' }};">
                                <h4>Route Wise Commissions</h4>
                                <div id="route_com_div">
                                    <div class="table-responsive-lg">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: left;">Route Name</th>
                                                    <th style="text-align: center;">NonAC Comm.</th>
                                                    <th style="text-align: center;">AC Comm.</th>
                                                    <th style="text-align: center;">Comm. Type</th>
                                                </tr>
                                            </thead>
                                            <tbody id="commission_table_body">
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Routes will load here.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="reset" class="btn btn-warning">Reset</button>
                        <a href="{{ route('admin.counters.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JS --}}
    <script>
        // Runs on DOMContentLoaded
        document.addEventListener('DOMContentLoaded', initializeCounterForm);

        function initializeCounterForm() {
            // Initialize selectpicker styles
            if ($('.selectpicker').length) {
                $('.selectpicker').selectpicker('refresh');
            }

            const counterType = document.getElementById('counter_type');

            // Bind change event
            counterType.addEventListener('change', function () {
                toggleCommissionVisibility(this.value);
            });

            // Trigger initial load based on current state (important for validation error reload)
            toggleCommissionVisibility(counterType.value, true);
        }

        /**
         * Executes the AJAX call to fetch and load the Route Commission table content.
         */
        function loadRouteCommissions() {
            const commissionBody = $('#commission_table_body');
            commissionBody.html('<tr><td colspan="4" class="text-center text-info"><i class="fas fa-spinner fa-spin"></i> Loading routes...</td></tr>');

            $.ajax({
                url: "{{ route('admin.counters.route-commission') }}",
                type: 'GET',
                dataType: 'json',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },

                success: function (response) {
                    if (response.html) {
                        // Insert the returned HTML into the <tbody>
                        commissionBody.html(response.html);
                        // ⭐ FIX: Enable fields after successful insertion ⭐
                        toggleCommissionFields(true);
                    } else {
                        commissionBody.html('<tr><td colspan="4" class="text-danger text-center">Failed to load commission data.</td></tr>');
                    }
                },
                error: function () {
                    commissionBody.html('<tr><td colspan="4" class="text-danger text-center">Error loading routes. Check server logs.</td></tr>');
                }
            });
        }

        /**
         * Enables/Disables input fields within the commission table.
         */
        function toggleCommissionFields(isEnabled) {
            // Selects all inputs within the commission column
            const selector = '#commission_settings_column input, #commission_settings_column select';
            $(selector).prop('disabled', !isEnabled);
        }

        /**
         * Toggles visibility and triggers AJAX load if 'Commission' is selected.
         */
        function toggleCommissionVisibility(selectedType, isInitialLoad = false) {
            const commissionColumn = document.getElementById('commission_settings_column');
            const isCommission = selectedType === 'Commission';

            if (isCommission) {
                commissionColumn.style.display = 'block';

                // Check if the table is empty or has the placeholder text
                const isPlaceholder = $('#commission_table_body').find('td').text().includes('Routes will load here.');

                if (isInitialLoad && isPlaceholder) {
                    loadRouteCommissions();
                } else if (!isInitialLoad) {
                    // User actively switched to Commission, force reload/enable
                    loadRouteCommissions();
                }

            } else {
                commissionColumn.style.display = 'none';
            }

            // Apply disable/enable logic
            toggleCommissionFields(isCommission);

            // Refresh selectpicker styles
            if ($('.selectpicker').length) {
                $('.selectpicker').selectpicker('refresh');
            }
        }
    </script>
@endsection