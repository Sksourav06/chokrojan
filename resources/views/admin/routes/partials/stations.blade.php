@if($route->stations && $route->stations->count())
    @foreach($route->stations as $station)
        ...
    @endforeach
@else
    <div class="text-muted">No stations found for this route.</div>
@endif