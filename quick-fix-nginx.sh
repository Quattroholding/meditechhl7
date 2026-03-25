#!/bin/bash

echo "🚀 Quick Fix para Nginx + Livewire v4"
echo "======================================"
echo ""

# 1. Copiar configuración actualizada
echo "1️⃣ Copiando configuración de Nginx..."
if [ -f "public/digitalocean_ventas" ]; then
    sudo cp public/digitalocean_ventas /etc/nginx/sites-available/ventas.meditecpty.com
    echo "✅ Configuración copiada"
else
    echo "❌ No se encontró public/digitalocean_ventas"
    exit 1
fi
echo ""

# 2. Crear symlink si no existe
echo "2️⃣ Verificando symlink..."
if [ -L "/etc/nginx/sites-enabled/ventas.meditecpty.com" ]; then
    echo "✅ Symlink ya existe"
else
    echo "Creando symlink..."
    sudo ln -sf /etc/nginx/sites-available/ventas.meditecpty.com /etc/nginx/sites-enabled/
    echo "✅ Symlink creado"
fi
echo ""

# 3. Probar configuración
echo "3️⃣ Probando configuración de Nginx..."
if sudo nginx -t; then
    echo "✅ Configuración válida"
else
    echo "❌ Error en configuración de Nginx"
    echo "   Revisa el error arriba"
    exit 1
fi
echo ""

# 4. Recargar Nginx
echo "4️⃣ Recargando Nginx..."
sudo systemctl reload nginx
if [ $? -eq 0 ]; then
    echo "✅ Nginx recargado"
else
    echo "❌ Error al recargar Nginx"
    exit 1
fi
echo ""

# 5. Test local
echo "5️⃣ Test local..."
sleep 2
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/vendor/livewire/livewire.js)
echo "Respuesta: HTTP $HTTP_CODE"
if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ ¡Nginx sirve el archivo correctamente!"
else
    echo "❌ Nginx aún no sirve el archivo (código: $HTTP_CODE)"
    echo ""
    echo "Mostrando configuración actual de /vendor/livewire/:"
    grep -A 5 "vendor/livewire" /etc/nginx/sites-available/ventas.meditecpty.com || echo "No encontrado"
fi
echo ""

# 6. Instrucciones finales
echo "======================================"
if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ CORRECCIÓN EXITOSA"
    echo ""
    echo "Próximos pasos:"
    echo "1. Limpia caché de Cloudflare:"
    echo "   https://dash.cloudflare.com"
    echo "   → Caching → Purge Everything"
    echo ""
    echo "2. Espera 2-3 minutos"
    echo ""
    echo "3. Prueba en tu navegador (modo incógnito):"
    echo "   https://ventas.meditecpty.com/vendor/livewire/livewire.js"
else
    echo "⚠️  Nginx configurado pero aún hay problemas"
    echo ""
    echo "Ejecuta el diagnóstico completo:"
    echo "  ./diagnose-production.sh"
fi
