#!/bin/bash

# Script para encontrar texto hardcodeado en español en archivos Blade
# Uso: ./find-hardcoded-text.sh

echo "🔍 Buscando texto hardcodeado en español..."
echo "================================================"
echo ""

# Buscar texto que empiece con mayúscula en español dentro de tags HTML
grep -rn ">[[:space:]]*[A-ZÁÉÍÓÚÑ][a-záéíóúñ]" resources/views/livewire \
    --include="*.blade.php" \
    --color=always \
    | head -100

echo ""
echo "================================================"
echo "📊 Total de líneas encontradas:"
grep -r ">[[:space:]]*[A-ZÁÉÍÓÚÑ][a-záéíóúñ]" resources/views/livewire \
    --include="*.blade.php" \
    | wc -l

echo ""
echo "📁 Archivos con más texto hardcodeado:"
grep -r ">[[:space:]]*[A-ZÁÉÍÓÚÑ][a-záéíóúñ]" resources/views/livewire \
    --include="*.blade.php" \
    | cut -d: -f1 \
    | sort \
    | uniq -c \
    | sort -rn \
    | head -20
