#!/bin/bash

echo "🔍 Diagnóstico Completo de Livewire en Producción"
echo "=================================================="
echo ""

# 1. Verificar archivos locales
echo "1️⃣ Archivos en el servidor:"
if [ -f "public/vendor/livewire/livewire.js" ]; then
    echo "✅ livewire.js existe"
    ls -lh public/vendor/livewire/livewire.js
else
    echo "❌ livewire.js NO existe"
    echo "   Ejecuta: php artisan livewire:publish --assets"
fi
echo ""

# 2. Verificar manifest
echo "2️⃣ Manifest actual:"
if [ -f "public/vendor/livewire/manifest.json" ]; then
    cat public/vendor/livewire/manifest.json
    echo ""
else
    echo "❌ manifest.json NO existe"
fi
echo ""

# 3. Test directo al archivo (sin pasar por Nginx virtualmente)
echo "3️⃣ Test acceso directo al archivo:"
if [ -r "public/vendor/livewire/livewire.js" ]; then
    echo "✅ Archivo es legible por el usuario actual"
    stat public/vendor/livewire/livewire.js | grep -E "Access|Uid"
else
    echo "❌ Archivo NO es legible"
fi
echo ""

# 4. Test local HTTP
echo "4️⃣ Test HTTP local (localhost):"
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/vendor/livewire/livewire.js)
echo "HTTP Status: $HTTP_CODE"
if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ Nginx sirve el archivo correctamente en localhost"
else
    echo "❌ Nginx NO sirve el archivo (código: $HTTP_CODE)"
    echo "   Esto indica un problema de configuración de Nginx"
fi
echo ""

# 5. Verificar configuración de Nginx activa
echo "5️⃣ Configuración de Nginx activa:"
echo "Ubicaciones posibles:"
if [ -f "/etc/nginx/sites-enabled/ventas.meditecpty.com" ]; then
    echo "✅ /etc/nginx/sites-enabled/ventas.meditecpty.com"
    NGINX_CONFIG="/etc/nginx/sites-enabled/ventas.meditecpty.com"
elif [ -L "/etc/nginx/sites-enabled/ventas.meditecpty.com" ]; then
    NGINX_CONFIG=$(readlink -f /etc/nginx/sites-enabled/ventas.meditecpty.com)
    echo "✅ Symlink a: $NGINX_CONFIG"
elif [ -f "/etc/nginx/sites-available/ventas.meditecpty.com" ]; then
    echo "⚠️  Existe en sites-available pero NO en sites-enabled"
    echo "   Necesitas: sudo ln -s /etc/nginx/sites-available/ventas.meditecpty.com /etc/nginx/sites-enabled/"
    NGINX_CONFIG="/etc/nginx/sites-available/ventas.meditecpty.com"
else
    echo "❌ No se encontró configuración de Nginx"
    NGINX_CONFIG=""
fi
echo ""

# 6. Verificar regla de /vendor/livewire/ en Nginx
echo "6️⃣ Regla de /vendor/livewire/ en Nginx:"
if [ -n "$NGINX_CONFIG" ] && [ -f "$NGINX_CONFIG" ]; then
    if grep -q "vendor/livewire" "$NGINX_CONFIG"; then
        echo "✅ Se encontró configuración para /vendor/livewire/"
        grep -A 3 "vendor/livewire" "$NGINX_CONFIG"
    else
        echo "❌ NO se encontró configuración para /vendor/livewire/"
        echo "   La configuración no se aplicó correctamente"
    fi
else
    echo "⚠️  No se pudo verificar (archivo de configuración no encontrado)"
fi
echo ""

# 7. Verificar última recarga de Nginx
echo "7️⃣ Estado de Nginx:"
if command -v systemctl &> /dev/null; then
    systemctl status nginx | grep -E "Active|Loaded" | head -2
    echo ""
    echo "Última recarga:"
    journalctl -u nginx --no-pager -n 3 | tail -3
else
    echo "⚠️  systemctl no disponible"
fi
echo ""

# 8. Test HTTPS externo (si curl está disponible)
echo "8️⃣ Test HTTPS externo:"
EXTERNAL_CODE=$(curl -s -o /dev/null -w "%{http_code}" https://ventas.meditecpty.com/vendor/livewire/livewire.js 2>/dev/null)
echo "HTTPS Status: $EXTERNAL_CODE"
if [ "$EXTERNAL_CODE" = "200" ]; then
    echo "✅ ¡Funciona! El archivo es accesible desde internet"
elif [ "$EXTERNAL_CODE" = "404" ]; then
    echo "❌ Error 404 desde internet"
    echo "   Posibles causas:"
    echo "   - Caché de Cloudflare (necesita purgar)"
    echo "   - Configuración de Nginx no está activa"
    echo "   - Symlink no creado en sites-enabled"
else
    echo "⚠️  Respuesta inesperada: $EXTERNAL_CODE"
fi
echo ""

# 9. Instrucciones según resultados
echo "=================================================="
echo "📋 SOLUCIONES SEGÚN LOS ERRORES ENCONTRADOS:"
echo ""
if [ "$HTTP_CODE" != "200" ]; then
    echo "🔴 Nginx local NO sirve el archivo:"
    echo "   1. Verifica que la configuración esté en sites-enabled:"
    echo "      sudo ln -sf /etc/nginx/sites-available/ventas.meditecpty.com /etc/nginx/sites-enabled/"
    echo "   2. Prueba la configuración:"
    echo "      sudo nginx -t"
    echo "   3. Recarga Nginx:"
    echo "      sudo systemctl reload nginx"
    echo ""
fi

if [ "$HTTP_CODE" = "200" ] && [ "$EXTERNAL_CODE" = "404" ]; then
    echo "🟡 Funciona localmente pero NO desde internet:"
    echo "   1. Limpia el caché de Cloudflare:"
    echo "      https://dash.cloudflare.com → Caching → Purge Everything"
    echo "   2. Espera 2-3 minutos"
    echo "   3. Prueba en modo incógnito del navegador"
    echo ""
fi

if [ "$HTTP_CODE" = "200" ] && [ "$EXTERNAL_CODE" = "200" ]; then
    echo "✅ TODO FUNCIONA CORRECTAMENTE"
    echo "   Si aún ves errores en el navegador:"
    echo "   - Limpia caché del navegador (Ctrl+Shift+R)"
    echo "   - Prueba en modo incógnito"
    echo "   - Limpia caché de Cloudflare"
fi
