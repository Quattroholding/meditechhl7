#!/bin/bash

# Script para probar que Nightwatch capture excepciones
# Uso: ./test-nightwatch-exception.sh

echo "🧪 Generando excepción de prueba para Nightwatch..."
echo ""

# Opción 1: Generar excepción via Tinker
php artisan tinker --execute "throw new \Exception('Nightwatch Test Exception - This is a test from local environment');"

echo ""
echo "✅ Excepción generada."
echo "📊 Revisa Nightwatch en: https://nightwatch.laravel.com"
echo "🔍 Debería aparecer una nueva excepción en el environment 'local'"