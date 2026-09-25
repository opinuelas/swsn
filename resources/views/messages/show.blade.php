<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Conversation with :name', ['name' => $partner->name]) }}
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 space-y-4">
        <div class="space-y-3 max-h-[28rem] overflow-y-auto">
            @forelse ($messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs px-3 py-2 rounded-lg text-sm {{ $message->sender_id === auth()->id() ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' }}">
                        {{ $message->body }}
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Say hello!') }}</p>
            @endforelse
        </div>

        <form method="post" action="{{ route('messages.store', $partner) }}" class="flex gap-2">
            @csrf
            <x-text-input name="body" type="text" class="flex-1" placeholder="{{ __('Type a message...') }}" required autofocus />
            <x-primary-button>{{ __('Send') }}</x-primary-button>
        </form>
        <x-input-error class="mt-2" :messages="$errors->get('body')" />
    </div>
</x-app-layout>
