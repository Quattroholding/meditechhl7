#!/bin/bash

echo "🔍 Verificación Rápida del Sistema Nightwatch AI"
echo "=================================================="
echo ""

# Colores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Verificar túnel de Cloudflare
echo -n "1. ✓ Verificando túnel de Cloudflare... "
TUNNEL_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" https://local.meditecpty.com/test)
if [ "$TUNNEL_RESPONSE" == "200" ]; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}FALLO (HTTP $TUNNEL_RESPONSE)${NC}"
    echo "   → Inicia el túnel: cloudflared tunnel run"
fi

# 2. Verificar ruta del webhook
echo -n "2. ✓ Verificando ruta del webhook... "
if php artisan route:list | grep -q "webhooks/nightwatch"; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}FALLO${NC}"
    echo "   → La ruta no está registrada"
fi

# 3. Verificar API key de Anthropic
echo -n "3. ✓ Verificando API key de Anthropic... "
API_KEY=$(php artisan tinker --execute 'echo config("ai.providers.anthropic.key");' 2>/dev/null)
if [ -n "$API_KEY" ] && [ "$API_KEY" != "null" ]; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}FALLO${NC}"
    echo "   → Configura ANTHROPIC_API_KEY en .env"
fi

# 4. Verificar email de destino
echo -n "4. ✓ Verificando emails de destino... "
EMAILS=$(php artisan tinker --execute 'print_r(config("mail.nightwatch_alert_emails"));' 2>/dev/null | grep -c "@")
if [ "$EMAILS" -gt 0 ]; then
    echo -e "${GREEN}OK ($EMAILS emails)${NC}"
else
    echo -e "${RED}FALLO${NC}"
    echo "   → Configura NIGHTWATCH_ALERT_EMAILS en .env"
fi

# 5. Verificar queue worker
echo -n "5. ✓ Verificando queue worker... "
if pgrep -f "queue:listen\|queue:work" > /dev/null; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${YELLOW}NO ESTÁ CORRIENDO${NC}"
    echo "   → Ejecuta: php artisan queue:listen --queue=default,emails"
fi

# 6. Verificar webhook secret
echo -n "6. ✓ Verificando webhook secret... "
WEBHOOK_SECRET=$(grep NIGHTWATCH_WEBHOOK_SECRET .env | cut -d '=' -f2)
if [ -n "$WEBHOOK_SECRET" ]; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${YELLOW}VACÍO${NC}"
    echo "   → Puedes dejarlo vacío para desarrollo"
fi

echo ""
echo "=================================================="
echo ""

# Resumen
if [ "$TUNNEL_RESPONSE" == "200" ] && [ -n "$API_KEY" ] && [ "$EMAILS" -gt 0 ]; then
    echo -e "${GREEN}✅ Sistema listo para probar${NC}"
    echo ""
    echo "Ejecuta: ${YELLOW}./test-nightwatch-webhook.sh${NC}"
    echo ""
else
    echo -e "${RED}❌ Hay problemas que corregir${NC}"
    echo ""
    echo "Revisa los errores arriba y corrígelos antes de continuar"
    echo ""
fi
