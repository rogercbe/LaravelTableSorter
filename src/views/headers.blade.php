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
                            <i class="fa fa-sort-asc"></i>
                        </a>
                    @else
                        <a href="{{ $header->path('asc') }}">
                            {{ $header->title }}
                            <i class="fa fa-sort-desc"></i>
                        </a>
                    @endif
                @else
                    <a href="{{ $header->path }}">
                        {{ $header->title }}
                        <i class="fa fa-sort"></i>
                    </a>
                @endif
            @endif
        </th>
    @endforeach
</tr>