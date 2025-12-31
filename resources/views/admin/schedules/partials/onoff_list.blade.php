@foreach($items as $item)
    <div class="p-2 mb-2 rounded bg-light border d-flex justify-content-between">
        <div>
            <strong>{{ $item->from_date }} → {{ $item->to_date }}</strong><br>
            <small>
                @foreach(json_decode($item->weekdays) as $d)
                    <span class="badge bg-primary">{{ $d }}</span>
                @endforeach
            </small>
        </div>

        <span class="badge bg-info">{{ $item->start_time }}</span>
    </div>
@endforeach