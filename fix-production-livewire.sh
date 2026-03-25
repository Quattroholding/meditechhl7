#!/bin/bash

echo "🚀 Corrigiendo Livewire v4 en Producción"
echo "========================================"
echo ""

# 1. Limpiar caché
echo "🧹 Limpiando caché..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
echo "✅ Caché limpiado"
echo ""

# 2. Publicar assets de Livewire (SIN --force en v4)
echo "📦 Publicando assets de Livewire..."
php artisan livewire:publish --assets
echo "✅ Assets publicados"
echo ""

# 3. Verificar permisos
echo "🔐 Configurando permisos..."
chmod -R 755 public/vendor/
echo "✅ Permisos configurados"
echo ""

# 4. Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Optimización completada"
echo ""

# 5. Verificar archivos
echo "🔍 Verificando archivos de Livewire..."
if [ -f "public/vendor/livewire/livewire.js" ]; then
    echo "✅ livewire.js existe"
    ls -lh public/vendor/livewire/livewire.js
else
    echo "❌ ERROR: livewire.js NO existe"
fi
echo ""

if [ -f "public/vendor/livewire/manifest.json" ]; then
    echo "✅ manifest.json existe"
    cat public/vendor/livewire/manifest.json
else
    echo "❌ ERROR: manifest.json NO existe"
fi
echo ""

# 6. Reiniciar Octane (si está disponible)
echo "🔄 Reiniciando servicios..."
if command -v supervisorctl &> /dev/null; then
    echo "Reiniciando workers..."
    sudo supervisorctl restart all
    echo "✅ Workers reiniciados"
else
    echo "⚠️  supervisorctl no disponible, omitiendo reinicio de workers"
    if php artisan list | grep -q "octane:reload"; then
        php artisan octane:reload
        echo "✅ Octane recargado"
    fi
fi
echo ""

echo "=============================="
echo "✅ Proceso completado!"
echo ""
echo "Próximo paso:"
echo "  Verifica: https://ventas.meditecpty.com/vendor/livewire/livewire.js"
