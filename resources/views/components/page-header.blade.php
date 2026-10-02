@props([
    'title' => '',
    'description' => '',
    'breadcrumb' => '',
])

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        @if ($breadcrumb)
            <p class="text-sm text-textSecondary">{{ $breadcrumb }}</p>
        @endif
        <h1 class="mt-1 text-2xl font-semibold tracking-tight text-textPrimary">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1 text-sm text-textSecondary">{{ $description }}</p>
        @endif
    </div>

    @if (isset($actions) && $actions->isNotEmpty())
        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
