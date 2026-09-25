@php
    $unreadCount = \App\Models\Message::query()
        ->where('recipient_id', auth()->id())
        ->whereNull('read_at')
        ->count();

    $linkClasses = fn (bool $active) => 'flex items-center px-3 py-2 rounded-md text-sm font-medium '
        .($active
            ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300'
            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700');
@endphp

<nav class="space-y-1 bg-white dark:bg-gray-800 shadow sm:rounded-lg p-3">
    <a href="{{ route('dashboard') }}" class="{{ $linkClasses(request()->routeIs('dashboard')) }}">
        {{ __('New Pictures') }}
    </a>

    <a href="{{ route('profile.edit') }}" class="{{ $linkClasses(request()->routeIs('profile.edit')) }}">
        {{ __('My Profile') }}
    </a>

    <a href="{{ route('messages.index') }}" class="{{ $linkClasses(request()->routeIs('messages.*')) }} justify-between">
        <span>{{ __('Messages') }}</span>
        @if ($unreadCount > 0)
            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-indigo-600 rounded-full">
                {{ $unreadCount }}
            </span>
        @endif
    </a>
</nav>
