<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Configuración de Reglas Contables</h1>
            <p class="mt-2 text-sm text-gray-600">
                Configura las cuentas contables que se utilizarán automáticamente en cada tipo de transacción
            </p>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Nota:</strong> Estos son los valores por defecto que se utilizarán cuando se generen asientos contables automáticamente. Puedes cambiarlos en cualquier momento.
                </p>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if($showSuccess)
        <div class="rounded-lg bg-green-50 border border-green-200 p-4 animate-pulse">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-700">{{ $successMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Rules Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Rule: Income -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Ingresos</h3>
                        <p class="text-sm text-gray-600">Cuenta para registrar ingresos por servicios</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4">
                @if($rules['income'])
                    <div class="mb-4 p-3 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-sm text-gray-600">Cuenta configurada:</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $rules['income']['account_code'] }}</p>
                        <p class="text-sm text-gray-600">{{ $rules['income']['account_name'] }}</p>
                    </div>
                @else
                    <div class="mb-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <p class="text-sm text-yellow-700">Sin configuración</p>
                    </div>
                @endif

                <div @click="$wire.selectedRule = 'income'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cambiar a:</label>
                    <select
                        wire:model="selectedAccountId"
                        @change="if($wire.selectedRule === 'income') {}"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                    >
                        <option value="">-- Selecciona una cuenta --</option>
                        @foreach($accounts as $account)
                            @if(in_array($account['type'], ['income', 'revenue']))
                                <option value="{{ $account['id'] }}">
                                    {{ $account['code'] }} - {{ $account['name'] }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <button
                    wire:click="updateRule"
                    @click="$wire.selectedRule = 'income'; $wire.call('updateRule')"
                    class="mt-4 w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition"
                >
                    Guardar Cambios
                </button>
            </div>
        </div>

        <!-- Rule: Receivable -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Cuentas por Cobrar</h3>
                        <p class="text-sm text-gray-600">Cuenta para registrar dinero adeudado por pacientes</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4">
                @if($rules['receivable'])
                    <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-sm text-gray-600">Cuenta configurada:</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $rules['receivable']['account_code'] }}</p>
                        <p class="text-sm text-gray-600">{{ $rules['receivable']['account_name'] }}</p>
                    </div>
                @else
                    <div class="mb-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <p class="text-sm text-yellow-700">Sin configuración</p>
                    </div>
                @endif

                <div @click="$wire.selectedRule = 'receivable'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cambiar a:</label>
                    <select
                        wire:model="selectedAccountId"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    >
                        <option value="">-- Selecciona una cuenta --</option>
                        @foreach($accounts as $account)
                            @if($account['type'] === 'asset')
                                <option value="{{ $account['id'] }}">
                                    {{ $account['code'] }} - {{ $account['name'] }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <button
                    wire:click="selectedRule = 'receivable'; updateRule()"
                    class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition"
                >
                    Guardar Cambios
                </button>
            </div>
        </div>

        <!-- Rule: Expense -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4m0 0L3 9m8 8v-8m4-4l8 8" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Gastos</h3>
                        <p class="text-sm text-gray-600">Cuenta para registrar gastos operacionales</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4">
                @if($rules['expense'])
                    <div class="mb-4 p-3 bg-red-50 rounded-lg border border-red-200">
                        <p class="text-sm text-gray-600">Cuenta configurada:</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $rules['expense']['account_code'] }}</p>
                        <p class="text-sm text-gray-600">{{ $rules['expense']['account_name'] }}</p>
                    </div>
                @else
                    <div class="mb-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <p class="text-sm text-yellow-700">Sin configuración</p>
                    </div>
                @endif

                <div @click="$wire.selectedRule = 'expense'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cambiar a:</label>
                    <select
                        wire:model="selectedAccountId"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                    >
                        <option value="">-- Selecciona una cuenta --</option>
                        @foreach($accounts as $account)
                            @if($account['type'] === 'expense')
                                <option value="{{ $account['id'] }}">
                                    {{ $account['code'] }} - {{ $account['name'] }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <button
                    wire:click="selectedRule = 'expense'; updateRule()"
                    class="mt-4 w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition"
                >
                    Guardar Cambios
                </button>
            </div>
        </div>

        <!-- Rule: Bank -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Banco/Caja</h3>
                        <p class="text-sm text-gray-600">Cuenta para registrar efectivo y movimientos bancarios</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4">
                @if($rules['bank'])
                    <div class="mb-4 p-3 bg-purple-50 rounded-lg border border-purple-200">
                        <p class="text-sm text-gray-600">Cuenta configurada:</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $rules['bank']['account_code'] }}</p>
                        <p class="text-sm text-gray-600">{{ $rules['bank']['account_name'] }}</p>
                    </div>
                @else
                    <div class="mb-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <p class="text-sm text-yellow-700">Sin configuración</p>
                    </div>
                @endif

                <div @click="$wire.selectedRule = 'bank'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cambiar a:</label>
                    <select
                        wire:model="selectedAccountId"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                    >
                        <option value="">-- Selecciona una cuenta --</option>
                        @foreach($accounts as $account)
                            @if($account['type'] === 'asset')
                                <option value="{{ $account['id'] }}">
                                    {{ $account['code'] }} - {{ $account['name'] }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <button
                    wire:click="selectedRule = 'bank'; updateRule()"
                    class="mt-4 w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition"
                >
                    Guardar Cambios
                </button>
            </div>
        </div>
    </div>

    <!-- Explanation Section -->
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">¿Cómo funciona?</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                    <span class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-sm mr-2">1</span>
                    Ingresos
                </h4>
                <p class="text-sm text-gray-600">Cuando se registra una factura médica, los ingresos se acreditan a esta cuenta.</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                    <span class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-sm mr-2">2</span>
                    Cuentas por Cobrar
                </h4>
                <p class="text-sm text-gray-600">Cuando se emite una factura a crédito, se debita a esta cuenta.</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                    <span class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center text-red-600 text-sm mr-2">3</span>
                    Gastos
                </h4>
                <p class="text-sm text-gray-600">Cuando se aprueba una factura de proveedor, los gastos se debitan a esta cuenta.</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                    <span class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-sm mr-2">4</span>
                    Banco/Caja
                </h4>
                <p class="text-sm text-gray-600">Todos los movimientos de efectivo y bancos se registran en esta cuenta.</p>
            </div>
        </div>
    </div>
</div>
