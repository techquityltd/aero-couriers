<div class="card w-full mt-4">
    <div class="flex flex-wrap -mb-4 -mx-2">
        @forelse($configuration['settings']->chunk(ceil($configuration['settings']->count() / 2)) as $chunk)
            @foreach ($chunk as $setting)
                <div class="w-1/2 px-2 mb-4">
                    @include('admin::settings.inputs.type.' . Str::kebab(class_basename($setting)), [
                        'key' => $configuration['key'],
                        'setting' => $setting,
                    ])
                </div>
            @endforeach
        @empty
            <p class="my-4">No settings to edit</p>
        @endforelse
    </div>
</div>
