<div class="flex truncate">
    @if(isset($count) && $count)
        <span class="text-grey-dark font-mono">[{{ $count }}]</span>
    @endif
    @foreach($links as $link)
        @include('admin::resource-lists.link', [
            'route' => $link['route'],
            'text' => $link['text'] . (!$loop->last ? ',' : ''),
            'blank' => $link['blank'] ?? null,
            'class' => $link['class'] ?? null . ' ml-2',
            'noreferrer' => $link['noreferrer'] ?? null,
        ])
    @endforeach
  </div>
</div>
