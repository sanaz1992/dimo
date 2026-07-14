<div class="table-responsive">
    <table class="table {{ $class ?? '' }}">
        <thead>
            <tr>
                @foreach ($columns as $col)
                    <th>{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    @foreach ($fields as $field)
                        <td>{{ data_get($row, $field) }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
