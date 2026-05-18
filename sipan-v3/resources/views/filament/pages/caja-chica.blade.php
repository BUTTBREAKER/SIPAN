<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Estado de la Caja -->
        <div class="col-span-1">
            @if(!$cajaActiva)
                <x-filament::section title="Apertura de Caja" icon="heroicon-o-lock-open">
                    <p class="text-sm text-gray-500 mb-4">Ingrese los montos iniciales en caja.</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Efectivo USD ($)</label>
                            <x-filament::input.wrapper>
                                <x-filament::input type="number" wire:model="monto_apertura_usd" step="0.01" />
                            </x-filament::input.wrapper>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Efectivo VES (Bs)</label>
                            <x-filament::input.wrapper>
                                <x-filament::input type="number" wire:model="monto_apertura_bs" step="0.01" />
                            </x-filament::input.wrapper>
                            <p class="text-xs text-gray-400 mt-1">Tasa actual: {{ number_format($tasa_bcv, 2) }} Bs/$</p>
                        </div>

                        <x-filament::button wire:click="abrirCaja" color="success" class="w-full">
                            Abrir Caja
                        </x-filament::button>
                    </div>
                </x-filament::section>
            @else
                <div class="space-y-6">
                    <x-filament::section title="Caja Abierta" icon="heroicon-o-check-circle" class="border-green-500">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm text-gray-500">Apertura:</span>
                            <span class="font-medium">{{ $cajaActiva->fecha_apertura->format('d/m/Y h:i A') }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Monto Inicial (USD):</span>
                            <span class="font-bold text-green-600">${{ number_format($cajaActiva->monto_apertura_usd, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm text-gray-500">Monto Inicial (VES):</span>
                            <span class="font-bold text-blue-600">Bs {{ number_format($cajaActiva->monto_apertura_bs, 2) }}</span>
                        </div>
                    </x-filament::section>

                    <x-filament::section title="Cierre de Caja" icon="heroicon-o-lock-closed">
                        <div class="space-y-4">
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 mb-4">
                                <h4 class="text-sm font-semibold mb-2">Montos Esperados (Aprox)</h4>
                                <div class="flex justify-between">
                                    <span class="text-xs">USD: <strong class="text-green-500">${{ number_format($monto_cierre_usd, 2) }}</strong></span>
                                    <span class="text-xs">VES: <strong class="text-blue-500">Bs {{ number_format($monto_cierre_bs, 2) }}</strong></span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Arqueo Efectivo USD ($)</label>
                                <x-filament::input.wrapper>
                                    <x-filament::input type="number" wire:model="monto_cierre_usd" step="0.01" />
                                </x-filament::input.wrapper>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Arqueo Efectivo VES (Bs)</label>
                                <x-filament::input.wrapper>
                                    <x-filament::input type="number" wire:model="monto_cierre_bs" step="0.01" />
                                </x-filament::input.wrapper>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Observaciones</label>
                                <x-filament::input.wrapper>
                                    <textarea wire:model="observaciones" class="w-full border-none bg-transparent focus:ring-0 text-sm" rows="3"></textarea>
                                </x-filament::input.wrapper>
                            </div>

                            <x-filament::button wire:click="cerrarCaja" color="danger" class="w-full"
                                onclick="confirm('¿Está seguro de cerrar la caja con estos montos?') || event.stopImmediatePropagation()">
                                Confirmar Cierre
                            </x-filament::button>
                        </div>
                    </x-filament::section>
                </div>
            @endif
        </div>

        <!-- Movimientos -->
        <div class="col-span-1 md:col-span-2">
            @if($cajaActiva)
                <x-filament::section title="Registrar Movimiento Extra" icon="heroicon-o-arrows-right-left" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium">Tipo</label>
                            <select wire:model="tipo_movimiento" class="w-full rounded-lg border-gray-300 shadow-sm text-sm dark:bg-gray-900 dark:border-gray-700">
                                <option value="ingreso">Ingreso (+)</option>
                                <option value="egreso">Egreso (-)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Método</label>
                            <select wire:model="metodo_pago_movimiento" class="w-full rounded-lg border-gray-300 shadow-sm text-sm dark:bg-gray-900 dark:border-gray-700">
                                <option value="efectivo_usd">Efectivo USD</option>
                                <option value="efectivo_ves">Efectivo VES</option>
                                <option value="punto_venta">Punto de Venta</option>
                                <option value="pago_movil">Pago Móvil</option>
                                <option value="zelle">Zelle</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Monto</label>
                            <x-filament::input.wrapper>
                                <x-filament::input type="number" wire:model="monto_movimiento" step="0.01" />
                            </x-filament::input.wrapper>
                        </div>
                        <div>
                            <x-filament::button wire:click="registrarMovimiento" color="primary" class="w-full">
                                Registrar
                            </x-filament::button>
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-sm font-medium">Descripción</label>
                            <x-filament::input.wrapper>
                                <x-filament::input type="text" wire:model="descripcion_movimiento" placeholder="Ej: Pago a proveedor, Retiro del jefe..." />
                            </x-filament::input.wrapper>
                        </div>
                    </div>
                </x-filament::section>

                <x-filament::section title="Historial de Movimientos de hoy">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">Hora</th>
                                    <th class="px-4 py-3">Tipo</th>
                                    <th class="px-4 py-3">Descripción</th>
                                    <th class="px-4 py-3">Método</th>
                                    <th class="px-4 py-3 text-right">Monto (Equiv USD)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movimientos as $mov)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3">{{ $mov->fecha->format('h:i A') }}</td>
                                        <td class="px-4 py-3">
                                            @if($mov->tipo === 'ingreso')
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Ingreso</span>
                                            @else
                                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Egreso</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ $mov->descripcion ?: ($mov->id_venta ? 'Venta POS #'.$mov->venta->numero_venta : 'N/A') }}
                                        </td>
                                        <td class="px-4 py-3">{{ str_replace('_', ' ', strtoupper($mov->metodo_pago)) }}</td>
                                        <td class="px-4 py-3 text-right font-medium {{ $mov->tipo === 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $mov->tipo === 'ingreso' ? '+' : '-' }} ${{ number_format($mov->monto, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                                            No hay movimientos registrados en esta caja.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>
            @else
                <div class="flex items-center justify-center h-full">
                    <div class="text-center text-gray-500">
                        <x-heroicon-o-lock-closed class="w-16 h-16 mx-auto mb-4 text-gray-400" />
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Caja Cerrada</h3>
                        <p class="mt-1">Abra la caja para registrar ventas y movimientos.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
