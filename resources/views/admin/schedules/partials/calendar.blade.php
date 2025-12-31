<div class="calendar-wrapper">

    <table class="table table-bordered text-center">
        <thead>
            <tr class="bg-light">
                <th>SUN</th>
                <th>MON</th>
                <th>TUE</th>
                <th>WED</th>
                <th>THU</th>
                <th>FRI</th>
                <th>SAT</th>
            </tr>
        </thead>

        <tbody>
            @php
                $month = now()->month;
                $year = now()->year;
                $daysInMonth = now()->daysInMonth;
                $startWeekday = \Carbon\Carbon::create($year, $month, 1)->dayOfWeek;

                $day = 1;
            @endphp

            @for ($row = 0; $row < 6; $row++)
                <tr>
                    @for ($col = 0; $col < 7; $col++)
                        @if ($row == 0 && $col < $startWeekday)
                            <td></td>
                        @elseif ($day > $daysInMonth)
                            <td></td>
                        @else
                            <td class="p-2">
                                <strong>{{ $day }}</strong>

                                @foreach ($items as $item)
                                    @if (
                                            $day >= \Carbon\Carbon::parse($item->from_date)->day
                                            && $day <= \Carbon\Carbon::parse($item->to_date)->day
                                        )
                                        <div class="badge bg-success mt-1">{{ $item->start_time }}</div>
                                    @endif
                                @endforeach

                                @php $day++; @endphp
                            </td>
                        @endif
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>

</div>