<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('New Pictures') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="p-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <form method="get" action="{{ route('dashboard') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                <div class="sm:col-span-2">
                    <x-input-label for="search" :value="__('Search Profile')" />
                    <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="request('search')" placeholder="{{ __('Search by name...') }}" />
                </div>

                <div>
                    <x-input-label for="gender" :value="__('Gender')" />
                    <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="">{{ __('Any') }}</option>
                        @foreach ($genders as $gender)
                            <option value="{{ $gender->value }}" @selected(request('gender') === $gender->value)>{{ $gender->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="country" :value="__('Country')" />
                    <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" :value="request('country')" />
                </div>

                <div>
                    <x-input-label for="state" :value="__('State')" />
                    <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" :value="request('state')" />
                </div>

                <div class="sm:col-span-4">
                    <x-input-label for="city" :value="__('City')" />
                    <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="request('city')" />
                </div>

                <div class="flex items-end gap-2">
                    <x-primary-button>{{ __('Filter') }}</x-primary-button>
                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 underline">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>

        @if ($pictures->isEmpty())
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg text-gray-600 dark:text-gray-400">
                {{ __('No pictures found.') }}
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                @foreach ($pictures as $picture)
                    <a href="{{ route('pictures.show', $picture) }}" class="group block">
                        <div class="aspect-square overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-700">
                            <img src="{{ $picture->url() }}" alt="{{ $picture->caption }}"
                                 class="h-full w-full object-cover group-hover:opacity-90 transition">
                        </div>
                        <div class="mt-1 flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
                            <span class="truncate">{{ $picture->user->name }}</span>
                            <span class="flex items-center gap-2 shrink-0 ms-2">
                                <span>&hearts; {{ $picture->likes->count() }}</span>
                                <span>&#128172; {{ $picture->comments->count() }}</span>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div>
                {{ $pictures->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
