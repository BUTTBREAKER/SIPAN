<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        
        <!-- Reporte de Ventas -->
        <x-filament::section title="Reporte de Ventas" icon="heroicon-o-currency-dollar" class="border-t-4 border-green-500">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Genera un reporte detallado de las ventas completadas en un rango de fechas.
            </p>
            
            <form wire:submit.prevent="generarReporteVentas" class="space-y-4">
                {{ $this->ventasForm }}
                
                <x-filament::button type="submit" color="success" icon="heroicon-o-document-arrow-down" class="w-full">
                    Descargar PDF
                </x-filament::button>
            </form>
        </x-filament::section>

        <!-- Reporte de Inventario -->
        <x-filament::section title="Estado de Inventario" icon="heroicon-o-archive-box" class="border-t-4 border-blue-500">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Genera un listado del inventario actual con existencias y valoración de costo.
            </p>
            
            <form wire:submit.prevent="generarReporteInventario" class="space-y-4">
                {{ $this->inventarioForm }}
                
                <x-filament::button type="submit" color="primary" icon="heroicon-o-document-arrow-down" class="w-full">
                    Descargar PDF
                </x-filament::button>
            </form>
        </x-filament::section>

        <!-- Reporte de Cajas Chicas -->
        <x-filament::section title="Movimientos de Caja" icon="heroicon-o-banknotes" class="border-t-4 border-purple-500">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Genera un historial de aperturas, cierres y descuadres de la caja chica.
            </p>
            
            <form wire:submit.prevent="generarReporteCajas" class="space-y-4">
                {{ $this->cajasForm }}
                
                <x-filament::button type="submit" color="gray" icon="heroicon-o-document-arrow-down" class="w-full">
                    Descargar PDF
                </x-filament::button>
            </form>
        </x-filament::section>

    </div>
</x-filament-panels::page>
