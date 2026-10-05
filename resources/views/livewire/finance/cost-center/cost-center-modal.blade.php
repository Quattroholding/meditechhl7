<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="sticky top-0 bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold">
                {{ $this->costCenter ? 'Editar Centro de Costo' : 'Nuevo Centro de Costo' }}
            </h3>
            <button
                wire:click="$dispatch('closeModal')"
                class="text-gray-500 hover:text-gray-700"
            >
                ✕
            </button>
        </div>

        {{-- Form --}}
        <form wire:submit.prevent="save" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                {{-- Code --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Código *</label>
                    <input
                        type="text"
                        wire:model="code"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ej: CC-001"
                    />
                    @error('code')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre *</label>
                    <input
                        type="text"
                        wire:model="name"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ej: Consultorios"
                    />
                    @error('name')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium mb-1">Descripción</label>
                <textarea
                    wire:model="description"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="2"
                    placeholder="Descripción opcional..."
                ></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Branch --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Sucursal *</label>
                    <select
                        wire:model="branch_id"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Seleccionar sucursal...</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Speciality --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Especialidad</label>
                    <select
                        wire:model="medical_speciality_id"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Seleccionar especialidad...</option>
                        @foreach ($specialities as $speciality)
                            <option value="{{ $speciality->id }}">
                                {{ $speciality->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium mb-1">Estado</label>
                <select
                    wire:model="status"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-2 pt-4 border-t">
                <button
                    type="button"
                    wire:click="$dispatch('closeModal')"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-50"
                >
                    Cancelar
                </button>
                @if ($this->costCenter)
                    <button
                        type="button"
                        wire:click="delete"
                        wire:confirm="¿Está seguro de que desea eliminar este centro de costo?"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        Eliminar
                    </button>
                @endif
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
