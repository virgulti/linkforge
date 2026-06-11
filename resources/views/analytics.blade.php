<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkForge - Analytics for {{ $link->short_code }}</title>
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .gradient-bg {
            background: radial-gradient(circle at 10% 20%, rgb(242, 235, 243) 0%, rgb(228, 237, 250) 90%);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen p-4 md:p-8 text-gray-800">
    <div class="max-w-6xl mx-auto space-y-6">
        
        <!-- Header -->
        <header class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-indigo-600 text-white p-2.5 rounded-xl shadow-md transition hover:scale-105 duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">LinkForge <span class="text-indigo-600">Analytics</span></h1>
                    <p class="text-xs text-gray-500">Real-time link performance metrics</p>
                </div>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Home
            </a>
        </header>

        <!-- Link info Card -->
        <div class="glass-card rounded-2xl shadow-xl p-6 md:p-8 relative overflow-hidden transition hover:shadow-2xl duration-300">
            <!-- Subtle background graphics -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-100 rounded-full blur-3xl opacity-60"></div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div class="space-y-4 max-w-3xl">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $link->is_active ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                            {{ $link->is_active ? 'Active' : 'Expired' }}
                        </span>
                        @if($link->expires_at)
                            <span class="ml-2 text-xs text-gray-500">
                                Expires: {{ $link->expires_at->format('M d, Y H:i') }}
                            </span>
                        @endif
                    </div>
                    
                    <div>
                        <h2 class="text-sm font-medium text-gray-400 uppercase tracking-wider">Original URL</h2>
                        <a href="{{ $link->original_url }}" target="_blank" class="text-lg font-semibold text-gray-900 break-all hover:text-indigo-600 transition">
                            {{ $link->original_url }}
                        </a>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <div>
                            <h2 class="text-sm font-medium text-gray-400 uppercase tracking-wider">Shortened URL</h2>
                            <a id="shortUrl" href="{{ route('links.redirect', $link->short_code) }}" target="_blank" class="text-xl font-bold text-indigo-600 hover:underline">
                                {{ route('links.redirect', $link->short_code) }}
                            </a>
                        </div>
                        <button onclick="copyToClipboard('{{ route('links.redirect', $link->short_code) }}')" class="mt-4 p-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg transition duration-200 self-end" title="Copy to clipboard">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="copyIcon">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Big Click Counter Stat -->
                <div class="flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-2xl p-6 md:p-8 min-w-[200px] shadow-lg shadow-indigo-200">
                    <div class="text-center">
                        <span class="block text-4xl font-extrabold tracking-tight">{{ number_format($totalClicks) }}</span>
                        <span class="text-xs uppercase tracking-widest text-indigo-100 font-semibold mt-1 block">Total Clicks</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chart Card -->
        <div class="glass-card rounded-2xl shadow-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Clicks Over Time (Last 30 Days)</h3>
            <div class="relative h-80 w-full">
                <canvas id="clicksChart"></canvas>
            </div>
        </div>

        <!-- Referrers and Devices Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Top Referrers Card -->
            <div class="glass-card rounded-2xl shadow-xl p-6 flex flex-col">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Referrers</h3>
                
                @if($topReferrers->isEmpty())
                    <div class="flex-grow flex items-center justify-center text-gray-400 py-12">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            <p class="text-sm">No referrer data available yet</p>
                        </div>
                    </div>
                @else
                    <div class="space-y-4 flex-grow">
                        @foreach($topReferrers as $referrer)
                            @php
                                $percent = $totalClicks > 0 ? ($referrer->count / $totalClicks) * 100 : 0;
                            @endphp
                            <div class="space-y-1">
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium text-gray-700 truncate max-w-xs" title="{{ $referrer->referrer }}">
                                        {{ $referrer->referrer }}
                                    </span>
                                    <span class="text-gray-500 font-semibold">{{ $referrer->count }} ({{ number_format($percent, 1) }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Device Breakdown Card -->
            <div class="glass-card rounded-2xl shadow-xl p-6 flex flex-col">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Device Breakdown</h3>
                
                @if($totalClicks === 0)
                    <div class="flex-grow flex items-center justify-center text-gray-400 py-12">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <p class="text-sm">No device data available yet</p>
                        </div>
                    </div>
                @else
                    <div class="flex-grow flex flex-col sm:flex-row items-center justify-around gap-6">
                        <div class="relative w-44 h-44">
                            <canvas id="devicesChart"></canvas>
                        </div>
                        <div class="space-y-2.5">
                            @foreach($devices as $device => $count)
                                @php
                                    $percent = $totalClicks > 0 ? ($count / $totalClicks) * 100 : 0;
                                    $colors = [
                                        'Desktop' => 'bg-indigo-500',
                                        'Mobile' => 'bg-emerald-500',
                                        'Tablet' => 'bg-amber-500',
                                        'Unknown' => 'bg-gray-400'
                                    ];
                                @endphp
                                <div class="flex items-center space-x-3 text-sm">
                                    <span class="w-3.5 h-3.5 rounded-full {{ $colors[$device] }}"></span>
                                    <span class="font-medium text-gray-700 w-20">{{ $device }}</span>
                                    <span class="text-gray-500 font-semibold">{{ $count }} ({{ number_format($percent, 1) }}%)</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>
        
    </div>

    <!-- Chart Configuration Script -->
    <script>
        // Clicks Chart (Line Chart with Gradient)
        const clicksData = @json($clicksPerDay);
        const labels = Object.keys(clicksData);
        const data = Object.values(clicksData);

        const ctxClicks = document.getElementById('clicksChart');
        if (ctxClicks) {
            const chartContext = ctxClicks.getContext('2d');
            const gradient = chartContext.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

            new Chart(ctxClicks, {
                type: 'line',
                data: {
                    labels: labels.map(date => {
                        const d = new Date(date);
                        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    }),
                    datasets: [{
                        label: 'Clicks',
                        data: data,
                        borderColor: '#4f46e5',
                        borderWidth: 3,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#4f46e5',
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#ffffff',
                        pointHoverBorderColor: '#4f46e5',
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            padding: 12,
                            backgroundColor: 'rgba(17, 24, 39, 0.95)',
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 13 },
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: '#9ca3af'
                            },
                            grid: {
                                borderDash: [5, 5],
                                color: 'rgba(156, 163, 175, 0.15)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#9ca3af',
                                maxRotation: 45,
                                autoSkip: true,
                                maxTicksLimit: 15
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Devices Chart (Doughnut Chart)
        const devicesData = @json($devices);
        const ctxDevices = document.getElementById('devicesChart');
        if (ctxDevices) {
            new Chart(ctxDevices, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(devicesData),
                    datasets: [{
                        data: Object.values(devicesData),
                        backgroundColor: [
                            '#6366f1', // Indigo (Desktop)
                            '#10b981', // Emerald (Mobile)
                            '#f59e0b', // Amber (Tablet)
                            '#9ca3af'  // Gray (Unknown)
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            padding: 10,
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            cornerRadius: 6
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // Copy to clipboard helper
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const button = event.target.closest('button');
                const svg = button.querySelector('svg');
                const originalSvg = svg.innerHTML;
                
                // Temporary green checkmark icon
                svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                button.classList.remove('bg-indigo-50', 'text-indigo-600');
                button.classList.add('bg-green-50', 'text-green-600');
                
                setTimeout(() => {
                    svg.innerHTML = originalSvg;
                    button.classList.remove('bg-green-50', 'text-green-600');
                    button.classList.add('bg-indigo-50', 'text-indigo-600');
                }, 2000);
            });
        }
    </script>
</body>
</html>
