<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="mb-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4">
        <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('New Message') }}</h3>

        <form method="post" action="{{ route('messages.send') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="to" :value="__('To (User ID)')" />
                <x-text-input id="to" name="to" type="text" maxlength="15" class="mt-1 block w-full" :value="old('to', request('to'))" placeholder="{{ __('User ID') }}" required />
                <x-input-error class="mt-2" :messages="$errors->get('to')" />
            </div>

            <div>
                <x-input-label for="new-message-body" :value="__('Message')" />
                <textarea id="new-message-body" name="body" rows="3" required
                          class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('body') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('body')" />
            </div>

            <x-primary-button>{{ __('Send') }}</x-primary-button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg divide-y divide-gray-200 dark:divide-gray-700">
        @forelse ($conversations as $conversation)
            <a href="{{ route('messages.show', $conversation->partner) }}" class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                <div class="min-w-0">
                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $conversation->partner->userid }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                        {{ $conversation->last_message?->body }}
                    </p>
                </div>

                @if ($conversation->unread_count > 0)
                    <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-indigo-600 rounded-full">
                        {{ $conversation->unread_count }}
                    </span>
                @endif
            </a>
        @empty
            <p class="p-6 text-sm text-gray-600 dark:text-gray-400">
                {{ __('No conversations yet. Use the form above to message someone by their User ID.') }}
            </p>
        @endforelse
    </div>
</x-app-layout>
