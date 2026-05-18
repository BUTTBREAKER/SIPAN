<x-filament-panels::page>
    <!-- Filtros -->
    <x-filament::section>
        <form wire:submit.prevent="calcularPrediccion" class="space-y-4">
            {{ $this->filtroForm }}
            
            <div class="flex justify-end mt-4">
                <x-filament::button type="submit" color="primary" icon="heroicon-o-calculator">
                    Analizar y Proyectar
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    <!-- Resultados -->
    @if(!empty($chartData))
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        
        <!-- Tarjeta de Resumen -->
        <div class="lg:col-span-1 space-y-6">
            <x-filament::section title="Resumen de Tendencia">
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Producto Analizado</p>
                        <p class="text-lg font-bold">{{ $chartData['producto'] }}</p>
                    </div>
                    
                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800 border dark:border-gray-700">
                        <p class="text-sm text-gray-500">Comportamiento Esperado</p>
                        @if($resultados['tendencia'] === 'creciente')
                            <p class="text-xl font-bold text-green-600 flex items-center">
                                <x-heroicon-o-arrow-trending-up class="w-6 h-6 mr-2"/>
                                Tendencia Creciente
                            </p>
                        @elseif($resultados['tendencia'] === 'decreciente')
                            <p class="text-xl font-bold text-red-600 flex items-center">
                                <x-heroicon-o-arrow-trending-down class="w-6 h-6 mr-2"/>
                                Tendencia Decreciente
                            </p>
                        @else
                            <p class="text-xl font-bold text-yellow-600 flex items-center">
                                <x-heroicon-o-minus class="w-6 h-6 mr-2"/>
                                Demanda Estable
                            </p>
                        @endif
                    </div>

                    <div class="mt-4">
                        <h4 class="text-md font-bold mb-2">Proyección Diaria (Próximos días)</h4>
                        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Unid. Esperadas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                                    @foreach(array_slice($resultados['proyecciones'], 0, 7) as $fecha => $valor)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">{{ date('d/m/Y', strtotime($fecha)) }}</td>
                                        <td class="px-4 py-2 text-sm text-right font-bold text-primary-600">{{ ceil($valor) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if(count($resultados['proyecciones']) > 7)
                            <p class="text-xs text-gray-500 text-center mt-2">Mostrando los primeros 7 días de la proyección.</p>
                        @endif
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Gráfico -->
        <div class="lg:col-span-2">
            <x-filament::section title="Gráfico de Demanda vs Proyección">
                <div class="relative w-full h-96">
                    <canvas id="predictionChart"></canvas>
                </div>
            </x-filament::section>
        </div>
        
    </div>

    <!-- Script de Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            let chartInstance = null;

            Livewire.on('update-prediction-chart', (event) => {
                const data = event[0].data || event[0]; // Extraer datos según estructura del evento

                const ctx = document.getElementById('predictionChart');
                
                if(!ctx) return;

                if (chartInstance) {
                    chartInstance.destroy();
                }

                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: data.datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += Math.round(context.parsed.y) + ' unid.';
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Cantidad Vendida / Proyectada'
                                }
                            }
                        }
                    }
                });
            });

            // Si hay datos cargados inicialmente (ej. se vuelve de otra página con el estado guardado)
            @if(!empty($chartData))
                const initialData = @json($chartData);
                Livewire.dispatch('update-prediction-chart', [initialData]);
            @endif
        });
    </script>
    @endif

</x-filament-panels::page>
