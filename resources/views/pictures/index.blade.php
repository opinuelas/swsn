<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('New Pictures') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
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
                            <span class="truncate">{{ $picture->user->userid }}</span>
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
