<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkForge - Shorten Your Links</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-4xl bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Header -->
        <div class="bg-indigo-600 py-6 px-6 text-white">
            <h1 class="text-3xl font-bold">LinkForge</h1>
            <p class="mt-2 opacity-90">Shorten your links with ease</p>
        </div>

        <!-- Main Content -->
        <div class="p-6">
            <!-- Success Alert -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    <p class="font-medium">Link shortened successfully!</p>
                    <div class="mt-2 flex items-center justify-between">
                        <div>
                            <p class="text-sm"><span class="font-medium">Original:</span> {{ session('success.original_url') }}</p>
                            <p class="text-sm mt-1"><span class="font-medium">Shortened:</span> 
                                <a href="{{ route('links.redirect', session('success.short_code')) }}" 
                                   class="text-indigo-600 hover:underline"
                                   target="_blank">
                                    {{ route('links.redirect', session('success.short_code')) }}
                                </a>
                            </p>
                        </div>
                        <button onclick="copyToClipboard('{{ route('links.redirect', session('success.short_code')) }}')" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Copy
                        </button>
                    </div>
                </div>
            @endif

            <!-- Shorten Form -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Shorten a New Link</h2>
                
                <form action="{{ route('links.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- URL Input -->
                    <div>
                        <label for="url" class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                        <input 
                            type="url" 
                            id="url" 
                            name="url" 
                            value="{{ old('url') }}" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="https://example.com">
                        @error('url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Custom Code Input -->
                    <div>
                        <label for="custom_code" class="block text-sm font-medium text-gray-700 mb-1">Custom Code (Optional)</label>
                        <input 
                            type="text" 
                            id="custom_code" 
                            name="custom_code" 
                            value="{{ old('custom_code') }}" 
                            maxlength="16"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="e.g. mylink">
                        @error('custom_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Expiration Date Input -->
                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">Expiration Date (Optional)</label>
                        <input 
                            type="datetime-local" 
                            id="expires_at" 
                            name="expires_at" 
                            value="{{ old('expires_at') }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('expires_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                            Shorten Link
                        </button>
                    </div>
                </form>
            </div>

            <!-- Recent Links Section -->
            @if($recentLinks->isNotEmpty())
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Links</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Original URL</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Short URL</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clicks</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentLinks as $link)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 max-w-xs truncate">
                                            {{ $link->url }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600">
                                            <a href="{{ route('links.redirect', $link->short_code) }}" 
                                               target="_blank" 
                                               class="hover:underline">
                                                {{ route('links.redirect', $link->short_code) }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($link->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Expired
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $link->clicks_count }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 text-center text-sm text-gray-500">
            <p>© {{ date('Y') }} LinkForge - Your link shortening solution</p>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Show a temporary success message or change button text
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>Copied!';
                setTimeout(() => {
                    button.innerHTML = originalText;
                }, 2000);
            });
        }
    </script>
</body>
</html>
