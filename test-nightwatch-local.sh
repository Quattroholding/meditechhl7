#!/bin/bash

# Script para probar el webhook de Nightwatch en localhost
# Útil para probar sin el túnel de Cloudflare

# Cambiar puerto según donde esté corriendo Laravel
PORT=9000
WEBHOOK_URL="http://127.0.0.1:${PORT}/nightwatch"
WEBHOOK_SECRET="f8c4216c30ad22543a9755153da4172c20a344f5d431f36ab71dd1e21555c9e3"

# Payload de ejemplo
PAYLOAD='{
  "event": "exception.created",
  "issue": {
    "id": "test-123",
    "ref": 123,
    "priority": "high",
    "status": "open"
  },
  "exception": {
    "class": "Illuminate\\Database\\QueryException",
    "message": "SQLSTATE[42S02]: Base table or view not found: 1146 Table '\''meditec_prod.non_existent_table'\'' does not exist",
    "file": "/var/www/html/meditech2/app/Models/Patient.php",
    "line": 142,
    "trace": "Stack trace:\n#0 /var/www/html/meditech2/vendor/laravel/framework/src/Illuminate/Database/Connection.php(825): Illuminate\\Database\\Connection->runQueryCallback()\n#1 /var/www/html/meditech2/vendor/laravel/framework/src/Illuminate/Database/Connection.php(537): Illuminate\\Database\\Connection->run()\n#2 /var/www/html/meditech2/app/Models/Patient.php(142): Illuminate\\Database\\Connection->select()\n#3 [internal function]: App\\Models\\Patient::scopeActive()",
    "code_context": "<?php\n\nnamespace App\\Models;\n\nclass Patient extends BaseModel\n{\n    public function scopeActive($query)\n    {\n        return $query->where('\''status'\'', '\''active'\''); // Línea 142\n    }\n}"
  },
  "context": {
    "type": "request",
    "method": "GET",
    "uri": "/api/patients"
  },
  "occurrence_count": 5,
  "first_seen_at": "2026-08-13T10:00:00Z",
  "last_seen_at": "2026-08-13T14:30:00Z",
  "issue_url": "https://nightwatch.laravel.com/us/environments/env-123/issues/123"
}'

# Generar firma HMAC
SIGNATURE=$(echo -n "$PAYLOAD" | openssl dgst -sha256 -hmac "$WEBHOOK_SECRET" | sed 's/^.* //')

echo "🚀 Enviando webhook de prueba LOCAL a: $WEBHOOK_URL"
echo "📝 Firma HMAC: $SIGNATURE"
echo ""

# Enviar webhook con firma
RESPONSE=$(curl -X POST "$WEBHOOK_URL" \
  -H "Content-Type: application/json" \
  -H "X-Nightwatch-Signature: $SIGNATURE" \
  -d "$PAYLOAD" \
  -s -w "\nHTTP_CODE:%{http_code}")

HTTP_CODE=$(echo "$RESPONSE" | grep "HTTP_CODE" | cut -d: -f2)
BODY=$(echo "$RESPONSE" | grep -v "HTTP_CODE")

echo "📥 Respuesta del servidor (HTTP $HTTP_CODE):"
echo "$BODY"
echo ""

if [ "$HTTP_CODE" == "200" ]; then
    echo "✅ Webhook procesado correctamente"
    echo ""
    echo "Ahora verifica:"
    echo "1. Terminal de Queue Worker: Debe mostrar procesamiento del job"
    echo "2. Terminal de Logs: php artisan pail --filter=Nightwatch"
    echo "3. Email enviado (si tienes Mailtrap configurado)"
else
    echo "❌ Error procesando webhook"
    echo "Revisa los logs: php artisan pail"
fi
