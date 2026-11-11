@php
    $selectClasses = 'mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';
    $activeFilterCount = collect(['search', 'gender', 'country', 'state', 'city'])->filter(fn ($field) => request()->filled($field))->count();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Search Profiles') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <x-primary-button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'profile-filters')">
                {{ __('Filters') }}
                @if ($activeFilterCount > 0)
                    <span class="ms-2 inline-flex items-center justify-center px-1.5 text-xs font-bold text-indigo-700 bg-white rounded-full">{{ $activeFilterCount }}</span>
                @endif
            </x-primary-button>

            @if ($activeFilterCount > 0)
                <a href="{{ route('profiles.index') }}" class="text-sm text-gray-600 dark:text-gray-400 underline">{{ __('Clear filters') }}</a>
            @endif
        </div>

        <x-modal name="profile-filters" focusable>
            <form method="get" action="{{ route('profiles.index') }}" class="p-6 space-y-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Filter Profiles') }}</h2>

                <div>
                    <x-input-label for="search" :value="__('User ID or Name')" />
                    <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="request('search')" />
                </div>

                <div>
                    <x-input-label for="gender" :value="__('Gender')" />
                    <select id="gender" name="gender" class="{{ $selectClasses }}">
                        <option value="">{{ __('Any') }}</option>
                        @foreach ($genders as $gender)
                            <option value="{{ $gender->value }}" @selected(request('gender') === $gender->value)>{{ $gender->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="country" :value="__('Country')" />
                        <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" :value="request('country')" />
                    </div>

                    <div>
                        <x-input-label for="state" :value="__('State')" />
                        <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" :value="request('state')" />
                    </div>

                    <div>
                        <x-input-label for="city" :value="__('City')" />
                        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="request('city')" />
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                    <x-primary-button>{{ __('Apply') }}</x-primary-button>
                </div>
            </form>
        </x-modal>

        @if ($profiles->isEmpty())
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg text-gray-600 dark:text-gray-400">
                {{ __('No profiles found.') }}
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                @foreach ($profiles as $profile)
                    <a href="{{ route('profiles.show', $profile) }}" class="group block p-3 bg-white dark:bg-gray-800 shadow sm:rounded-lg hover:ring-2 hover:ring-indigo-500 transition">
                        <div class="aspect-square overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-700">
                            <img src="{{ $profile->avatarUrl() }}" alt="{{ $profile->userid }}" class="h-full w-full object-cover">
                        </div>
                        <p class="mt-2 font-medium text-sm text-gray-900 dark:text-gray-100 truncate">{{ $profile->userid }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $profile->location() ?: __('Location not set') }}</p>
                    </a>
                @endforeach
            </div>

            <div>
                {{ $profiles->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
