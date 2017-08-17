<tr>
    @foreach($headers as $header)
        <th class="{{ $header->classes }}">
            @if(! $header->sortable)
                {{ $header->title }}
            @else
                @if(request('sort') == $header->key)
                    @if(request('direction') == 'asc')
                        <a href="{{ $header->path('desc') }}">
                            {{ $header->title }}
                            <span>&#x25BC</span>
                        </a>
                    @else
                        <a href="{{ $header->path('asc') }}">
                            {{ $header->title }}
                            <span>&#x25B2</span>
                        </a>
                    @endif
                @else
                    <a href="{{ $header->path }}">
                        {{ $header->title }}
                    </a>
                @endif
            @endif
        </th>
    @endforeach
</tr>