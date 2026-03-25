#!/bin/bash

echo "🔧 Limpiando caché de Laravel..."
php artisan optimize:clear

echo "📦 Publicando assets de Livewire..."
php artisan livewire:publish --assets --force

echo "🔄 Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Proceso completado. Verifica tu sitio."
