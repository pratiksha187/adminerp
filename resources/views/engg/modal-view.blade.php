<table class="table table-bordered">

    <tr>
        <th>Date</th>
        <td>{{ $entry->date }}</td>
    </tr>

    <tr>
        <th>Chapter</th>
        <td>{{ $entry->chapter_name }}</td>
    </tr>

    <tr>
        <th>Description</th>
        <td>{{ $entry->description }}</td>
    </tr>

    <tr>
        <th>Unit</th>
        <td>{{ $entry->unit }}</td>
    </tr>

    <tr>
        <th>Length</th>
        <td>{{ $entry->length }}</td>
    </tr>

    <tr>
        <th>Breadth</th>
        <td>{{ $entry->breadth }}</td>
    </tr>

    <tr>
        <th>Height</th>
        <td>{{ $entry->height }}</td>
    </tr>

    <tr>
        <th>Days</th>
        <td>{{ $entry->days }}</td>
    </tr>

    <tr>
        <th>In Time</th>
        <td>{{ $entry->in_time ?? '-' }}</td>
    </tr>

    <tr>
        <th>Out Time</th>
        <td>{{ $entry->out_time ?? '-' }}</td>
    </tr>

    <tr>
        <th>Tonnage</th>
        <td>{{ $entry->tonnage ?? '-' }}</td>
    </tr>

    <tr>
        <th>Total Quantity</th>
        <td>{{ $entry->total_quantity }}</td>
    </tr>

    <tr>
        <th>Supervisor</th>
        <td>{{ $entry->supervisor }}</td>
    </tr>

    <tr>
        <th>Description of Work Done</th>
        <td>{{ $entry->description_of_work_done }}</td>
    </tr>

    <tr>
        <th>Labour Breakdown</th>
        <td>
            @if(is_array($entry->labour) && count($entry->labour) > 0)
                @foreach($entry->labour as $key => $value)
                    <strong>{{ ucwords(str_replace('_',' ',$key)) }}:</strong>
                    {{ $value }} <br>
                @endforeach
                <hr>
                <strong>Total Labour:</strong> {{ array_sum($entry->labour) }}
            @else
                <p class="text-muted">No labour recorded</p>
            @endif
        </td>
    </tr>

</table>
