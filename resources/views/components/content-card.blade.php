@props([
    'title' => '',
    'description' => '',
    'padding' => true,
])

<section {{ $attributes->class(['rounded-card border border-border bg-surface shadow-card' => true, 'p-5' => $padding]) }}>
    @if ($title)
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-textPrimary">{{ $title }}</h2>
                @if ($description)
                    <p class="mt-0.5 text-sm text-textSecondary">{{ $description }}</p>
                @endif
            </div>
            @if (isset($actions) && $actions->isNotEmpty())
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    {{ $slot }}
</section>
