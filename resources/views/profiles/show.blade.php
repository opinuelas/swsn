<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Public Profile') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <div class="flex flex-col sm:flex-row gap-6">
                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->userid }}"
                     class="h-32 w-32 rounded-full object-cover bg-gray-200 dark:bg-gray-700 shrink-0">

                <div class="flex-1 min-w-0 space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $user->userid }}</h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ trans_choice(':count profile view|:count profile views', $user->profile_views_count) }}
                        </span>

                        @unless ($user->is(auth()->user()))
                            <a href="{{ route('messages.show', $user) }}" class="ms-auto text-sm text-indigo-600 dark:text-indigo-400 underline">
                                {{ __('Send message') }}
                            </a>
                        @endunless
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Name') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Gender') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $user->gender?->label() }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Location') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $user->location() ?: '—' }}</dd>
                        </div>
                    </dl>

                    <div class="text-sm">
                        <h4 class="text-gray-500 dark:text-gray-400">{{ __('Physical Description') }}</h4>
                        <p class="text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $user->physical_description ?: '—' }}</p>
                    </div>

                    <div class="text-sm">
                        <h4 class="text-gray-500 dark:text-gray-400">{{ __('Finding Preference') }}</h4>
                        <p class="text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $user->finding_preference ?: '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Pictures') }}</h3>

            @if ($pictures->isEmpty())
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('No pictures yet.') }}</p>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    @foreach ($pictures as $picture)
                        <a href="{{ route('pictures.show', $picture) }}" class="group block aspect-square overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-700">
                            <img src="{{ $picture->url() }}" alt="{{ $picture->caption }}" class="h-full w-full object-cover group-hover:opacity-90 transition">
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
