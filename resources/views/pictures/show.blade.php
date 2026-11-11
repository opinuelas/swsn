<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Picture') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
            <img src="{{ $picture->url() }}" alt="{{ $picture->caption }}" class="w-full max-h-[32rem] object-contain bg-black">

            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <a href="{{ route('profiles.show', $picture->user) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                            {{ $picture->user->userid }}
                        </a>
                        @unless ($picture->user->id === auth()->id())
                            <a href="{{ route('messages.show', $picture->user) }}" class="ms-2 text-xs text-gray-600 dark:text-gray-400 underline">
                                {{ __('Send message') }}
                            </a>
                        @endunless
                        @if ($picture->caption)
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $picture->caption }}</p>
                        @endif
                    </div>

                    <button type="button" id="like-button" data-picture-id="{{ $picture->id }}"
                            data-liked="{{ $picture->isLikedBy(auth()->user()) ? '1' : '0' }}"
                            class="flex items-center gap-1 px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 text-sm font-medium {{ $picture->isLikedBy(auth()->user()) ? 'text-red-600 border-red-300' : 'text-gray-700 dark:text-gray-300' }}">
                        <span id="like-icon">{{ $picture->isLikedBy(auth()->user()) ? '♥' : '♡' }}</span>
                        <span id="like-count">{{ $picture->likes->count() }}</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4">
            <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Comments') }}</h3>

            <div class="space-y-3 mb-4">
                @forelse ($picture->comments as $comment)
                    <div class="text-sm">
                        <a href="{{ route('profiles.show', $comment->user) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:underline">{{ $comment->user->userid }}</a>
                        <span class="text-gray-700 dark:text-gray-300">{{ $comment->body }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('No comments yet.') }}</p>
                @endforelse
            </div>

            <form method="post" action="{{ route('pictures.comments.store', $picture) }}" class="flex gap-2">
                @csrf
                <x-text-input name="body" type="text" class="flex-1" placeholder="{{ __('Add a comment...') }}" required />
                <x-primary-button>{{ __('Post') }}</x-primary-button>
            </form>
            <x-input-error class="mt-2" :messages="$errors->get('body')" />
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('like-button')?.addEventListener('click', async function () {
            const button = this;
            const pictureId = button.dataset.pictureId;

            const response = await fetch(`/pictures/${pictureId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();

            document.getElementById('like-count').textContent = data.count;
            document.getElementById('like-icon').textContent = data.liked ? '♥' : '♡';
            button.classList.toggle('text-red-600', data.liked);
            button.classList.toggle('border-red-300', data.liked);
        });
    </script>
    @endpush
</x-app-layout>
