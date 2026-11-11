@php
    $unreadCount = \App\Models\Message::query()
        ->where('recipient_id', auth()->id())
        ->whereNull('read_at')
        ->count();

    $linkClasses = fn (bool $active) => 'flex items-center px-3 py-2 rounded-md text-sm font-medium '
        .($active
            ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300'
            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700');

    $isOwnPublicProfile = request()->routeIs('profiles.show') && request()->route('user')?->is(auth()->user());
@endphp

<nav class="space-y-1 bg-white dark:bg-gray-800 shadow sm:rounded-lg p-3">
    <a href="{{ route('dashboard') }}" class="{{ $linkClasses(request()->routeIs('dashboard')) }}">
        {{ __('New Pictures') }}
    </a>

    <a href="{{ route('profiles.index') }}" class="{{ $linkClasses(request()->routeIs('profiles.index')) }}">
        {{ __('Search Profiles') }}
    </a>

    <a href="{{ route('profiles.show', auth()->user()) }}" class="{{ $linkClasses($isOwnPublicProfile) }}">
        {{ __('Public Profile') }}
    </a>

    <a href="{{ route('profile.edit') }}" class="{{ $linkClasses(request()->routeIs('profile.edit')) }}">
        {{ __('My Profile') }}
    </a>

    <a href="{{ route('profile.pictures') }}" class="{{ $linkClasses(request()->routeIs('profile.pictures')) }}">
        {{ __('My Pictures') }}
    </a>

    <a href="{{ route('profile.password') }}" class="{{ $linkClasses(request()->routeIs('profile.password')) }}">
        {{ __('Password Change') }}
    </a>

    <a href="{{ route('profiles.visitors') }}" class="{{ $linkClasses(request()->routeIs('profiles.visitors')) }}">
        {{ __('Who Visited Me') }}
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
