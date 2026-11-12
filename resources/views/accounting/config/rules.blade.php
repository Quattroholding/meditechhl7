@extends('layout.app')

@section('title', 'Configuración de Reglas Contables')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <livewire:accounting.config.accounting-rule-config-panel />
        </div>
    </div>
@endsection
