

@props(['chartId', 'labels', 'values', 'labelName'])

<div>
    <canvas id="{{ $chartId }}" width="400" height="200"></canvas>
</div>

@push('scripts')
    <script>
        // Menggunakan IIFE (Immediately Invoked Function Expression) untuk menghindari conflict
        (function() {
            // Pastikan DOM sudah ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded',
                    initBarChart_{{ str_replace(['-', '.', ' '], '_', $chartId) }});
            } else {
                initBarChart_{{ str_replace(['-', '.', ' '], '_', $chartId) }}();
            }

            function initBarChart_{{ str_replace(['-', '.', ' '], '_', $chartId) }}() {
                try {
                    const ctx = document.getElementById('{{ $chartId }}');
                    if (!ctx) {
                        console.warn('Canvas element {{ $chartId }} not found');
                        return;
                    }

                    const labels = {!! json_encode($labels) !!};
                    const values = {!! json_encode($values) !!};

                    // Validation
                    if (!labels || !values || labels.length === 0 || values.length === 0) {
                        console.warn('Bar Chart {{ $chartId }}: No valid data provided');
                        return;
                    }

                    // Filter data yang valid
                    const validData = [];
                    const validLabels = [];

                    for (let i = 0; i < labels.length; i++) {
                        if (values[i] !== null && values[i] !== undefined && values[i] >= 0) {
                            validLabels.push(labels[i]);
                            validData.push(values[i]);
                        }
                    }

                    // Jika tidak ada data valid, tampilkan placeholder
                    if (validData.length === 0) {
                        validLabels.push('Tidak ada data');
                        validData.push(0);
                    }

                    // Generate colors
                    const colors = [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                        'rgba(83, 102, 255, 0.8)'
                    ];

                    const borderColors = [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)',
                        'rgba(83, 102, 255, 1)'
                    ];

                    const backgroundColors = validLabels.map((_, index) => colors[index % colors.length]);
                    const borderColorsArray = validLabels.map((_, index) => borderColors[index % borderColors.length]);

                    const chart = new Chart(ctx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: validLabels,
                            datasets: [{
                                label: '{!! addslashes($labelName ?? 'Data') !!}',
                                data: validData,
                                backgroundColor: backgroundColors,
                                borderColor: borderColorsArray,
                                borderWidth: 2,
                                borderRadius: 4,
                                borderSkipped: false,
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
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed.y;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(
                                                1) : 0;
                                            return label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    min: 0,
                                    ticks: {
                                        precision: 0,
                                        callback: function(value) {
                                            return Number.isInteger(value) ? value : '';
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 0
                                    }
                                }
                            }
                        }
                    });

                    console.log('Bar Chart {{ $chartId }} rendered successfully');
                    console.log('Valid Labels:', validLabels);
                    console.log('Valid Data:', validData);

                } catch (error) {
                    console.error('Error rendering bar chart {{ $chartId }}:', error);

                    // Fallback
                    const ctx = document.getElementById('{{ $chartId }}');
                    if (ctx) {
                        const context = ctx.getContext('2d');
                        context.fillStyle = '#666';
                        context.font = '14px Arial';
                        context.textAlign = 'center';
                        context.fillText('Error loading chart', ctx.width / 2, ctx.height / 2);
                    }
                }
            }
        })();
    </script>
@endpush
