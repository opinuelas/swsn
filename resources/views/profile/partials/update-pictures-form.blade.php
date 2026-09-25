<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Pictures') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Upload pictures and choose one as your profile picture.') }}
        </p>
    </header>

    <div class="mt-6 flex items-center gap-4">
        <img src="{{ $user->profilePicture ? $user->profilePicture->url() : asset('images/default-avatar.svg') }}"
             alt="{{ __('Profile picture') }}"
             class="h-20 w-20 rounded-full object-cover bg-gray-200 dark:bg-gray-700">
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Current profile picture') }}</p>
    </div>

    @if (session('status') === 'picture-uploaded')
        <p class="mt-4 text-sm font-medium text-green-600 dark:text-green-400">{{ __('Picture uploaded.') }}</p>
    @endif

    @if (session('status') === 'profile-picture-updated')
        <p class="mt-4 text-sm font-medium text-green-600 dark:text-green-400">{{ __('Profile picture updated.') }}</p>
    @endif

    <form method="post" action="{{ route('pictures.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4 max-w-xl">
        @csrf

        <div>
            <x-input-label for="photo" :value="__('New Picture')" />
            <input id="photo" name="photo" type="file" accept="image/*" required
                   class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300">
            <x-input-error class="mt-2" :messages="$errors->get('photo')" />
        </div>

        <div>
            <x-input-label for="caption" :value="__('Caption (optional)')" />
            <x-text-input id="caption" name="caption" type="text" class="mt-1 block w-full" :value="old('caption')" />
            <x-input-error class="mt-2" :messages="$errors->get('caption')" />
        </div>

        <div class="flex items-center gap-2">
            <input id="set_as_profile_picture" name="set_as_profile_picture" type="checkbox" value="1"
                   class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"
                   @checked($pictures->isEmpty())>
            <x-input-label for="set_as_profile_picture" :value="__('Use as profile picture')" class="!mb-0" />
        </div>

        <x-primary-button>{{ __('Upload') }}</x-primary-button>
    </form>

    @if ($pictures->isNotEmpty())
        <div class="mt-8">
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('My Pictures') }}</h3>

            <div class="mt-3 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                @foreach ($pictures as $picture)
                    <div class="relative group">
                        <a href="{{ route('pictures.show', $picture) }}">
                            <img src="{{ $picture->url() }}" alt="{{ $picture->caption }}"
                                 class="aspect-square w-full object-cover rounded-md">
                        </a>

                        @if ($user->profile_picture_id === $picture->id)
                            <span class="absolute top-1 right-1 px-1.5 py-0.5 text-xs font-semibold text-white bg-indigo-600 rounded">
                                {{ __('Profile') }}
                            </span>
                        @else
                            <form method="post" action="{{ route('profile.picture.update', $picture) }}" class="absolute inset-x-0 bottom-0">
                                @csrf
                                @method('patch')
                                <button type="submit" class="w-full text-xs bg-black/60 text-white py-1 rounded-b-md opacity-0 group-hover:opacity-100 transition">
                                    {{ __('Set as profile') }}
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
