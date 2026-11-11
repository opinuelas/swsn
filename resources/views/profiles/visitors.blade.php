<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Who Visited Me') }}
        </h2>
    </x-slot>

    <div class="space-y-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ trans_choice('Your public profile has been viewed :count time.|Your public profile has been viewed :count times.', $user->profile_views_count) }}
        </p>

        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-x-auto">
            @if ($visits->isEmpty())
                <p class="p-6 text-sm text-gray-600 dark:text-gray-400">{{ __('Nobody has visited your public profile yet.') }}</p>
            @else
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">{{ __('Date') }}</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">{{ __('Time') }}</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">{{ __('User ID') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($visits as $visit)
                            <tr>
                                <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $visit->created_at->format('Y-m-d') }}</td>
                                <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $visit->created_at->format('H:i:s') }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('profiles.show', $visit->visitor) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $visit->visitor->userid }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div>
            {{ $visits->links() }}
        </div>
    </div>
</x-app-layout>
