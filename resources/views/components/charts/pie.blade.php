{{-- @props(['chartId', 'labels', 'values', 'label'])

<div style="position: relative; height: 300px;">
    <canvas id="{{ $chartId }}"></canvas>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
    <script>
        (function() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded',
                    initPieChart_{{ str_replace(['-', '.', ' '], '_', $chartId) }});
            } else {
                initPieChart_{{ str_replace(['-', '.', ' '], '_', $chartId) }}();
            }

            function initPieChart_{{ str_replace(['-', '.', ' '], '_', $chartId) }}() {
                try {
                    const ctx = document.getElementById('{{ $chartId }}');
                    if (!ctx) {
                        console.warn('Canvas element {{ $chartId }} not found');
                        return;
                    }

                    const labels = {!! json_encode($labels) !!};
                    const values = {!! json_encode($values) !!};

                    const validLabels = [];
                    const validData = [];

                    for (let i = 0; i < labels.length; i++) {
                        if (values[i] !== null && values[i] !== undefined && values[i] > 0) {
                            validLabels.push(labels[i]);
                            validData.push(values[i]);
                        }
                    }

                    if (validData.length === 0) {
                        validLabels.push('Tidak ada data');
                        validData.push(1);
                    }

                    const colors = [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                        '#9966FF', '#FF9F40', '#C9CBCF', '#4BC0C0',
                        '#FF6384', '#36A2EB', '#FFCE56'
                    ];
                    const backgroundColors = validLabels.map((_, i) => colors[i % colors.length]);

                    new Chart(ctx.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: validLabels,
                            datasets: [{
                                data: validData,
                                backgroundColor: backgroundColors,
                                borderColor: backgroundColors,
                                borderWidth: 2,
                                hoverBorderWidth: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 10,
                                        usePointStyle: true,
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(
                                                1) : 0;
                                            return label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                },
                                datalabels: {
                                    color: '#fff',
                                    font: {
                                        weight: 'bold',
                                        size: 10
                                    },
                                    formatter: function(value, context) {
                                        const data = context.chart.data.datasets[0].data;
                                        const total = data.reduce((a, b) => a + b, 0);
                                        const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return percent + '%';
                                    },
                                    display: function(context) {
                                        // Tampilkan label hanya jika value > 0
                                        return context.dataset.data[context.dataIndex] > 0;
                                    }
                                }
                            }
                        },
                        plugins: [ChartDataLabels]
                    });

                    console.log('Pie Chart {{ $chartId }} rendered successfully');
                } catch (error) {
                    console.error('Error rendering pie chart {{ $chartId }}:', error);
                }
            }
        })();
    </script>
@endpush --}}


@props(['chartId', 'labels', 'values', 'label'])

<div style="position: relative; height: 300px;">
    <canvas id="{{ $chartId }}"></canvas>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
    <script>
        (function() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded',
                    initPieChart_{{ str_replace(['-', '.', ' '], '_', $chartId) }});
            } else {
                initPieChart_{{ str_replace(['-', '.', ' '], '_', $chartId) }}();
            }

            function initPieChart_{{ str_replace(['-', '.', ' '], '_', $chartId) }}() {
                try {
                    const ctx = document.getElementById('{{ $chartId }}');
                    if (!ctx) return console.warn('Canvas {{ $chartId }} not found');

                    const labels = {!! json_encode($labels) !!};
                    const values = {!! json_encode($values) !!};

                    const validLabels = [];
                    const validData = [];

                    for (let i = 0; i < labels.length; i++) {
                        if (values[i] !== null && values[i] !== undefined && values[i] > 0) {
                            validLabels.push(labels[i]);
                            validData.push(values[i]);
                        }
                    }

                    if (validData.length === 0) {
                        validLabels.push('Tidak ada data');
                        validData.push(1);
                    }

                    const colors = [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                        '#9966FF', '#FF9F40', '#C9CBCF', '#4BC0C0',
                        '#FF6384', '#36A2EB', '#FFCE56'
                    ];
                    const backgroundColors = validLabels.map((_, i) => colors[i % colors.length]);

                    const total = validData.reduce((a, b) => a + b, 0);

                    new Chart(ctx.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: validLabels,
                            datasets: [{
                                data: validData,
                                backgroundColor: backgroundColors,
                                borderColor: backgroundColors,
                                borderWidth: 2,
                                hoverBorderWidth: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 10,
                                        usePointStyle: true,
                                        font: {
                                            size: 11
                                        },
                                        generateLabels: function(chart) {
                                            const data = chart.data;
                                            if (!data.labels.length) return [];

                                            return data.labels.map((label, i) => {
                                                const value = data.datasets[0].data[i];
                                                const total = data.datasets[0].data.reduce((a, b) =>
                                                    a + b, 0);
                                                const percentage = total > 0 ? ((value / total) *
                                                    100).toFixed(1) : 0;

                                                return {
                                                    text: `${label} (${percentage}%)`, // 💡 Tampilkan persen di legend
                                                    fillStyle: data.datasets[0].backgroundColor[i],
                                                    strokeStyle: data.datasets[0].backgroundColor[
                                                        i],
                                                    lineWidth: 2,
                                                    hidden: isNaN(value) || value <= 0,
                                                    index: i
                                                };
                                            });
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(
                                                1) : 0;
                                            return label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                },
                                datalabels: {
                                    color: '#fff',
                                    font: {
                                        weight: 'bold',
                                        size: 10
                                    },
                                    formatter: function(value, context) {
                                        const data = context.chart.data.datasets[0].data;
                                        const total = data.reduce((a, b) => a + b, 0);
                                        const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return percent + '%';
                                    },
                                    display: function(context) {
                                        return context.dataset.data[context.dataIndex] > 0;
                                    }
                                }
                            }
                        },
                        plugins: [ChartDataLabels]
                    });

                } catch (error) {
                    console.error('Error rendering pie chart {{ $chartId }}:', error);
                }
            }
        })();
    </script>
@endpush
