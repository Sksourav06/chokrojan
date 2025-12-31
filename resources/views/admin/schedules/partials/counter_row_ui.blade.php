<div class="row mb-3 align-items-center counter-item-row">
    {{-- টাইম --}}
    <div class="col-md-2">
        <input type="time" name="counter_data[{{ $rowIndex }}][time]" class="form-control"
            value="{{ data_get($record, 'time') }}">
    </div>

    {{-- কাউন্টার ড্রপডাউন --}}
    <div class="col-md-3">
        <select name="counter_data[{{ $rowIndex }}][counter_id]" class="form-control " data-live-search="true">
            <option value="">Select Counter</option>
            @php $available = data_get($station, 'available_counters', []); @endphp
            @foreach($available as $counter)
                <option value="{{ $counter->id }}" {{ data_get($record, 'counter_id') == $counter->id ? 'selected' : '' }}>
                    {{ $counter->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- তারিখ রেঞ্জ --}}
    <div class="col-md-2"><input type="date" name="counter_data[{{ $rowIndex }}][from_date]" class="form-control"
            value="{{ data_get($record, 'from_date') }}"></div>
    <div class="col-md-2"><input type="date" name="counter_data[{{ $rowIndex }}][to_date]" class="form-control"
            value="{{ data_get($record, 'to_date') }}"></div>

    <div class="col-md-1">
        <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fas fa-times"></i></button>
    </div>

    <input type="hidden" name="counter_data[{{ $rowIndex }}][station_id]" value="{{ data_get($station, 'id') }}">
</div>
<!-- <script>
    $(document).ready(function () {

        // init selectpicker on page load
        $('.selectpicker').selectpicker();

        $(document).on('click', '.add-new-row', function (e) {
            e.preventDefault();

            const stationId = $(this).data('station-id');
            const container = $('#station_rows_' + stationId);
            const firstRow = container.find('.counter-item-row').first();

            if (!firstRow.length) {
                console.error('Template row not found');
                return;
            }

            // unique index
            const newRowIndex = stationId + '_' + Date.now();

            // Destroy selectpicker BEFORE clone
            firstRow.find('.selectpicker').selectpicker('destroy');

            // Clone row
            const newRow = firstRow.clone();

            // Re-init selectpicker on original row
            firstRow.find('.selectpicker').selectpicker();

            // Clear values in cloned row
            newRow.find('input').val('');
            newRow.find('select').val('');

            // Update name attributes
            newRow.find('[name*="counter_data"]').each(function () {
                const name = $(this).attr('name');
                $(this).attr(
                    'name',
                    name.replace(/counter_data\[[^\]]+\]/, 'counter_data[' + newRowIndex + ']')
                );
            });

            // Append cloned row
            container.append(newRow);

            // Init selectpicker on cloned row
            newRow.find('.selectpicker').selectpicker();
        });

    });
    $(document).on('click', '.remove-row', function () {
        const row = $(this).closest('.counter-item-row');
        const container = row.parent();

        if (container.find('.counter-item-row').length > 1) {
            row.remove();
        } else {
            row.find('input').val('');
            row.find('select').val('').selectpicker('refresh');
        }
    });

</script> -->