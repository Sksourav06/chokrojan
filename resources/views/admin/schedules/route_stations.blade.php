<table class="table table-bordered align-middle text-center">
    <thead class="table-light">
        <tr>
            <th>Sequence</th>
            <th>Station Name</th>
            <th>Required Time</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($routeStations as $station)
            <tr>
                <td>{{ $station->pivot->sequence_order }}</td>
                <td>{{ $station->name }}</td>
                <td>{{ $station->pivot->required_time ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>