<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Izquierda: Búsqueda y Productos -->
        <div class="lg:col-span-2 space-y-6">
            <x-filament::section>
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
                            <x-filament::input
                                type="text"
                                placeholder="Buscar producto por nombre o código..."
                                wire:model.live.debounce.300ms="search"
                            />
                        </x-filament::input.wrapper>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @forelse($this->productos as $producto)
                        <div class="border rounded-xl p-4 flex flex-col justify-between hover:border-primary-500 transition-colors bg-white dark:bg-gray-900 shadow-sm">
                            <div>
                                <h3 class="font-bold text-lg leading-tight">{{ $producto->nombre }}</h3>
                                <p class="text-sm text-gray-500">{{ $producto->codigo }}</p>
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="text-primary-600 font-bold text-xl">${{ number_format($producto->precio_venta, 2) }}</span>
                                    <span class="text-xs text-gray-400">Stock: {{ $producto->stock_actual }}</span>
                                </div>
                            </div>
                            <x-filament::button
                                wire:click="addToCart({{ $producto->id }})"
                                class="mt-4 w-full"
                                size="sm"
                            >
                                Agregar
                            </x-filament::button>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-gray-500 italic">
                            {{ $search ? 'No se encontraron productos.' : 'Escribe algo para buscar productos...' }}
                        </div>
                    @endforelse
                </div>
            </x-filament::section>
        </div>

        <!-- Derecha: Carrito y Resumen -->
        <div class="space-y-6">
            <x-filament::section heading="🛒 Carrito de Compras">
                <div class="divide-y max-h-[400px] overflow-y-auto">
                    @forelse($cart as $id => $item)
                        <div class="py-3 flex flex-col gap-2">
                            <div class="flex justify-between items-start">
                                <span class="font-medium text-sm leading-tight">{{ $item['nombre'] }}</span>
                                <x-filament::icon-button
                                    icon="heroicon-m-x-mark"
                                    color="danger"
                                    size="xs"
                                    wire:click="removeFromCart({{ $id }})"
                                />
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <x-filament::input
                                        type="number"
                                        wire:change="updateQuantity({{ $id }}, $event.target.value)"
                                        value="{{ $item['cantidad'] }}"
                                        class="w-16 h-8 text-xs text-center"
                                        min="1"
                                    />
                                    <span class="text-xs text-gray-400">x ${{ number_format($item['precio_venta'], 2) }}</span>
                                </div>
                                <span class="font-bold text-sm">${{ number_format($item['precio_venta'] * $item['cantidad'], 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-400 text-sm">
                            Tu carrito está vacío.
                        </div>
                    @endforelse
                </div>

                <div class="mt-6 pt-6 border-t space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal (USD)</span>
                        <span class="font-medium">${{ number_format($this->total, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-gray-500 text-sm">Total (VES)</span>
                        <span class="text-xs text-gray-400 mr-auto ml-1">(Tasa: {{ $tasa_bcv }})</span>
                        <span class="font-bold text-xl text-primary-600">Bs. {{ number_format($this->total_ves, 2) }}</span>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section heading="💳 Finalizar Venta">
                <form wire:submit="processSale" class="space-y-4">
                    <div>
                        <label class="text-sm font-medium">Cliente (Opcional)</label>
                        <select wire:model="id_cliente" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm dark:bg-gray-800 dark:border-gray-700">
                            <option value="">Cliente Genérico</option>
                            @foreach($this->clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->apellido }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium">Método de Pago</label>
                        <select wire:model="metodo_pago" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm dark:bg-gray-800 dark:border-gray-700">
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta (Punto)</option>
                            <option value="transferencia">Transferencia / Pago Móvil</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium">Notas</label>
                        <textarea wire:model="notas" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm dark:bg-gray-800 dark:border-gray-700"></textarea>
                    </div>

                    <x-filament::button
                        type="submit"
                        class="w-full"
                        size="lg"
                        icon="heroicon-m-check-badge"
                        wire:loading.attr="disabled"
                    >
                        Procesar Venta
                    </x-filament::button>
                </form>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
