<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Configuración de Reglas Contables</h1>
            <p class="mt-2 text-sm text-gray-600">
                Configura las cuentas contables para ingresos, cuentas por cobrar, banco y gastos
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
                    <strong>Nuevo:</strong> Ahora puedes configurar reglas granulares por especialidad médica (ingresos) y tipo de pagador (CxC).
                </p>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if($showSuccess)
        <div class="rounded-lg bg-green-50 border border-green-200 p-4">
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

    <!-- Error Messages -->
    @if($errors->has('delete'))
        <div class="rounded-lg bg-red-50 border border-red-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-700">{{ $errors->first('delete') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8">
            <!-- Tab: Income -->
            <button
                wire:click="$set('currentTab', 'income')"
                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $currentTab === 'income' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Ingresos
            </button>

            <!-- Tab: Receivable -->
            <button
                wire:click="$set('currentTab', 'receivable')"
                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $currentTab === 'receivable' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Cuentas por Cobrar
            </button>

            <!-- Tab: Bank -->
            <button
                wire:click="$set('currentTab', 'bank')"
                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $currentTab === 'bank' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10m4 0a2 2 0 100-4H3a2 2 0 100 4h18zM6 7a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Banco
            </button>
        </nav>
    </div>

    <!-- Tab: INGRESOS -->
    @if($currentTab === 'income')
        <div class="space-y-6">
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Configuración de Ingresos</h2>

                <!-- Income Rules Table -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Reglas Configuradas</h3>

                    @if(count($incomeRules) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Especialidad</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cuenta Contable</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($incomeRules as $rule)
                                        <tr class="{{ $rule['is_default'] ? 'bg-green-50' : '' }}">
                                            <td class="px-6 py-4 text-sm">
                                                <div class="font-medium text-gray-900">{{ $rule['specialty_name'] }}</div>
                                                @if($rule['is_default'])
                                                    <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded">Por Defecto</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <div class="font-medium text-gray-900">{{ $rule['account_code'] }}</div>
                                                <div class="text-xs text-gray-500">{{ $rule['account_name'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                <span class="inline-block px-3 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-full">Ingresos</span>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                @if(!$rule['is_default'])
                                                    <button
                                                        wire:click="deleteRule({{ $rule['id'] }})"
                                                        wire:confirm="¿Estás seguro de que deseas eliminar esta regla?"
                                                        class="text-red-600 hover:text-red-900 font-medium"
                                                    >
                                                        Eliminar
                                                    </button>
                                                @else
                                                    <span class="text-gray-400 text-xs">No se puede eliminar</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No hay reglas configuradas</p>
                    @endif
                </div>

                <!-- Add Income Rule Form -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Agregar Regla por Especialidad</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Specialty Select -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad Médica</label>
                            <select
                                wire:model="selectedSpecialtyId"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                            >
                                <option value="">-- Selecciona una especialidad --</option>
                                @foreach($specialties as $spec)
                                    <option value="{{ $spec['id'] }}">{{ $spec['name'] }}</option>
                                @endforeach
                            </select>
                            @error('selectedSpecialtyId')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Account Select -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cuenta Contable (Ingresos)</label>
                            <select
                                wire:model="selectedIncomeAccountId"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                            >
                                <option value="">-- Selecciona una cuenta --</option>
                                @foreach($incomeAccounts as $account)
                                    <option value="{{ $account['id'] }}">
                                        {{ $account['code'] }} - {{ $account['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedIncomeAccountId')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Add Button -->
                        <div class="flex items-end">
                            <button
                                wire:click="addIncomeRuleBySpecialty"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition"
                            >
                                Agregar Regla
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab: CUENTAS POR COBRAR -->
    @if($currentTab === 'receivable')
        <div class="space-y-6">
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Configuración de Cuentas por Cobrar</h2>

                <!-- Receivable Rules Table -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Reglas Configuradas</h3>

                    @if(count($receivableRules) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Pagador</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cuenta Contable</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($receivableRules as $rule)
                                        <tr class="{{ $rule['is_default'] ? 'bg-blue-50' : '' }}">
                                            <td class="px-6 py-4 text-sm">
                                                <div class="font-medium text-gray-900">{{ $rule['payment_source_label'] }}</div>
                                                @if($rule['is_default'])
                                                    <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded">Por Defecto</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <div class="font-medium text-gray-900">{{ $rule['account_code'] }}</div>
                                                <div class="text-xs text-gray-500">{{ $rule['account_name'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                <span class="inline-block px-3 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-full">Activo</span>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                @if(!$rule['is_default'])
                                                    <button
                                                        wire:click="deleteRule({{ $rule['id'] }})"
                                                        wire:confirm="¿Estás seguro de que deseas eliminar esta regla?"
                                                        class="text-red-600 hover:text-red-900 font-medium"
                                                    >
                                                        Eliminar
                                                    </button>
                                                @else
                                                    <span class="text-gray-400 text-xs">No se puede eliminar</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No hay reglas configuradas</p>
                    @endif
                </div>

                <!-- Add Receivable Rule Form -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Agregar Regla por Tipo de Pagador</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Payment Source Select -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Pagador</label>
                            <select
                                wire:model="selectedPaymentSource"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            >
                                <option value="">-- Selecciona un tipo de pagador --</option>
                                @foreach($paymentSources as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('selectedPaymentSource')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Account Select -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cuenta Contable (Activo)</label>
                            <select
                                wire:model="selectedReceivableAccountId"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            >
                                <option value="">-- Selecciona una cuenta --</option>
                                @foreach($receivableAccounts as $account)
                                    <option value="{{ $account['id'] }}">
                                        {{ $account['code'] }} - {{ $account['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedReceivableAccountId')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Add Button -->
                        <div class="flex items-end">
                            <button
                                wire:click="addReceivableRuleByPaymentSource"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition"
                            >
                                Agregar Regla
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab: BANCO -->
    @if($currentTab === 'bank')
        <div class="space-y-6">
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Configuración de Banco</h2>

                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                    <p class="text-sm text-gray-600 mb-4">
                        La cuenta bancaria se utiliza para registrar todos los movimientos de efectivo y transferencias bancarias.
                    </p>

                    @if($bankRule)
                        <div class="bg-white border border-purple-200 rounded-lg p-4 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Cuenta Configurada</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Código</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $bankRule['account_code'] }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Nombre</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $bankRule['account_name'] }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <p class="text-sm text-yellow-700">No hay cuenta bancaria configurada</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Explanation Section -->
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">¿Cómo funcionan las reglas granulares?</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                    <span class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-sm mr-2 font-bold">1</span>
                    Ingresos por Especialidad
                </h4>
                <p class="text-sm text-gray-600">
                    Configura diferentes cuentas para diferentes especialidades. Por ejemplo: Medicina General → 4101, Cardiología → 4102, etc.
                </p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                    <span class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-sm mr-2 font-bold">2</span>
                    CxC por Tipo de Pagador
                </h4>
                <p class="text-sm text-gray-600">
                    Diferencia entre pacientes particulares (110201) y facturación a seguros (110202) automáticamente.
                </p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                    <span class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-sm mr-2 font-bold">3</span>
                    Reglas por Defecto
                </h4>
                <p class="text-sm text-gray-600">
                    Si no existe una regla específica, el sistema usa la regla marcada como "Por Defecto" automáticamente.
                </p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                    <span class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-sm mr-2 font-bold">4</span>
                    Gastos por Proveedor
                </h4>
                <p class="text-sm text-gray-600">
                    Los gastos se configuran directamente en cada proveedor, no necesitan regla centralizada.
                </p>
            </div>
        </div>
    </div>
</div>
