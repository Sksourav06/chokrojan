@extends('layouts.master')

@section('title', 'Edit Counter')

@section('content')
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Editing Counter: {{ $counter->name }}</h3>
                </div>

                {{-- Form action points to the UPDATE route with PUT method --}}
                <form method="POST" action="{{ route('admin.counters.update', $counter->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row">

                            {{-- === LEFT COLUMN: Counter Details === --}}
                            <div class="col-md-5">
                                
                                {{-- Counter Name --}}
                                <div class="form-group @error('name') has-error @enderror selectpicker">
                                    <label for="name" class="required">Counter Name</label>
                                    <input type="text" id="name" class="form-control @error('name') is-invalid @enderror "
                                        placeholder="Enter name" name="name" 
                                        value="{{ old('name', $counter->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Station Selection --}}
                               <div class="form-group @error('station_id') has-error @enderror">
    <label for="station_id" class="required">Station</label>
    
    @php 
        // 1. Get the current selected station ID from old input or the existing $counter object
        $currentStationId = old('station_id', $counter->station_id ?? null); 
    @endphp
    
    <select id="station_id"
        class="form-control selectpicker @error('station_id') is-invalid @enderror"
        name="station_id" 
        required 
        data-live-search="true" 
        data-size="10">
        
        <option value="">Select Station...</option>
        
        {{-- Loop through the list of stations --}}
        @foreach($stations as $id => $name)
            <option value="{{ $id }}" {{ $currentStationId == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    
    {{-- Display validation error message --}}
    @error('station_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- 
Note: For Bootstrap-Select (selectpicker) to work:
1. The necessary CSS and JS libraries must be loaded in the master layout.
2. The selectpicker() function must be initialized via JavaScript if not done automatically (e.g., $(document).ready(function() { $('.selectpicker').selectpicker(); });)
--}}
                                {{-- Status --}}
                                <div class="form-group @error('status') has-error @enderror">
                                    <label for="status" class="required">Status</label>
                                    @php $currentStatus = old('status', $counter->status); @endphp
                                    <select id="status"
                                        class="form-control selectpicker  @error('status') is-invalid @enderror"
                                        name="status" required>
                                        @foreach($availableStatuses as $statusOption)
                                            <option value="{{ $statusOption }}" {{ $currentStatus == $statusOption ? 'selected' : '' }}>
                                                {{ ucfirst($statusOption) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Credit Limit --}}
                                <div class="form-group @error('credit_limit') has-error @enderror">
                                    <label for="credit_limit" class="required">Credit Limit (৳)</label>
                                    <input type="number" step="0.01" id="credit_limit"
                                        class="form-control @error('credit_limit') is-invalid @enderror"
                                        placeholder="Enter limit amount" name="credit_limit"
                                        value="{{ old('credit_limit', $counter->credit_limit) }}" required>
                                    @error('credit_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Counter Type --}}
                                <div class="form-group @error('counter_type') has-error @enderror">
                                    <label for="counter_type" class="required">Counter Type</label>
                                    @php $currentType = old('counter_type', $counter->counter_type); @endphp
                                    <select id="counter_type"
                                        class="form-control  @error('counter_type') is-invalid @enderror"
                                        name="counter_type" required onchange="toggleCommissionVisibility(this.value)">
                                        <option value="Own" {{ $currentType == 'Own' ? 'selected' : '' }}>Own</option>
                                        <option value="Commission" {{ $currentType == 'Commission' ? 'selected' : '' }}>Commission</option>
                                    </select>
                                    @error('counter_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Permitted Credit --}}
                                <div class="form-group @error('permitted_credit') has-error @enderror">
                                    <label for="permitted_credit">Counter Permitted Credit (৳)</label>
                                    <input type="number" step="0.01" id="permitted_credit"
                                        class="form-control @error('permitted_credit') is-invalid @enderror"
                                        placeholder="Enter amount" name="permitted_credit"
                                        value="{{ old('permitted_credit', $counter->permitted_credit) }}">
                                    @error('permitted_credit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- === RIGHT COLUMN: Route Commissions (Dynamic Content Area) === --}}
                            <div class="col-md-7" id="commission_settings_column" 
                                 style="display: {{ old('counter_type', $counter->counter_type) == 'Commission' ? 'block' : 'none' }};">
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
                                                {{-- Placeholder - AJAX will populate this --}}
                                                <tr><td colspan="4" class="text-center text-muted">Routes will load here.</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Counter</button>
                        <a href="{{ route('admin.counters.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
    // Store the ID of the counter we are editing
    const currentCounterId = {{ $counter->id }};

    document.addEventListener('DOMContentLoaded', initializeCounterForm);

    function initializeCounterForm() {
        if ($('.selectpicker').length) {
             $('.selectpicker').selectpicker('refresh');
        }

        const counterType = document.getElementById('counter_type');
        counterType.addEventListener('change', function () {
            toggleCommissionVisibility(this.value);
        });

        // Trigger initial load based on the counter's current type
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
            data: { 
                _token: $('meta[name="csrf-token"]').attr('content'),
                counter_id: currentCounterId // ⭐ Pass the counter ID to get existing values
            }, 
            
            success: function (response) {
                if (response.html) {
                    commissionBody.html(response.html);
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
            
            // Check if the table body is still the placeholder
            const isPlaceholder = $('#commission_table_body').find('td').text().includes('Routes will load here.');
            
            // Load commissions if it's the initial page load OR if data hasn't been loaded yet
            if (isInitialLoad || isPlaceholder) {
                loadRouteCommissions();
            } else {
                toggleCommissionFields(true); // Re-enable fields if already loaded
            }
        } else {
            commissionColumn.style.display = 'none';
            toggleCommissionFields(false); // Disable all inputs when switching to 'Own'
        }

        if ($('.selectpicker').length) {
            $('.selectpicker').selectpicker('refresh');
        }
    }
</script>
@endsection