#!/bin/bash

# Script de Verificación: Sistema de Almacenamiento Externo
# Verifica que todos los componentes estén correctamente instalados

echo "======================================================================"
echo "  Verificación del Sistema de Almacenamiento Externo - Meditech2"
echo "======================================================================"
echo ""

# Colores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

errors=0
warnings=0

# Función para verificar archivos
check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✓${NC} $1"
    else
        echo -e "${RED}✗${NC} $1 - FALTA"
        ((errors++))
    fi
}

# Función para verificar sintaxis PHP
check_php_syntax() {
    if php -l "$1" > /dev/null 2>&1; then
        echo -e "${GREEN}✓${NC} Sintaxis PHP válida: $1"
    else
        echo -e "${RED}✗${NC} Error de sintaxis en: $1"
        ((errors++))
    fi
}

echo "1. Verificando dependencias de Composer..."
echo "----------------------------------------------------------------------"
if composer show spatie/flysystem-dropbox > /dev/null 2>&1; then
    VERSION=$(composer show spatie/flysystem-dropbox | grep "versions" | awk '{print $3}')
    echo -e "${GREEN}✓${NC} spatie/flysystem-dropbox instalado (versión: $VERSION)"
else
    echo -e "${RED}✗${NC} spatie/flysystem-dropbox NO está instalado"
    echo "   Ejecuta: composer require spatie/flysystem-dropbox"
    ((errors++))
fi
echo ""

echo "2. Verificando migrations..."
echo "----------------------------------------------------------------------"
check_file "database/migrations/2026_06_19_100810_add_external_storage_to_media_table.php"

# Verificar si la migration fue ejecutada
if php artisan migrate:status | grep -q "add_external_storage_to_media_table"; then
    if php artisan migrate:status | grep "add_external_storage_to_media_table" | grep -q "Ran"; then
        echo -e "${GREEN}✓${NC} Migration ejecutada correctamente"
    else
        echo -e "${YELLOW}⚠${NC} Migration existe pero no ha sido ejecutada"
        echo "   Ejecuta: php artisan migrate"
        ((warnings++))
    fi
else
    echo -e "${RED}✗${NC} Migration no encontrada en la base de datos"
    ((errors++))
fi
echo ""

echo "3. Verificando contratos e interfaces..."
echo "----------------------------------------------------------------------"
check_file "app/Contracts/Storage/ExternalStorageProvider.php"
check_php_syntax "app/Contracts/Storage/ExternalStorageProvider.php"
echo ""

echo "4. Verificando servicios de almacenamiento..."
echo "----------------------------------------------------------------------"
check_file "app/Services/Storage/DropboxStorageProvider.php"
check_php_syntax "app/Services/Storage/DropboxStorageProvider.php"
check_file "app/Services/Storage/StorageProviderFactory.php"
check_php_syntax "app/Services/Storage/StorageProviderFactory.php"
check_file "app/Services/MediaStorageService.php"
check_php_syntax "app/Services/MediaStorageService.php"
echo ""

echo "5. Verificando componentes Livewire..."
echo "----------------------------------------------------------------------"
check_file "app/Livewire/Settings/ExternalStorageSettings.php"
check_php_syntax "app/Livewire/Settings/ExternalStorageSettings.php"
check_file "app/Livewire/Consultation/FileUpload.php"
check_php_syntax "app/Livewire/Consultation/FileUpload.php"
echo ""

echo "6. Verificando vistas Blade..."
echo "----------------------------------------------------------------------"
check_file "resources/views/livewire/settings/external-storage-settings.blade.php"
check_file "resources/views/livewire/consultation/file-upload.blade.php"
check_file "resources/views/settings/external-storage.blade.php"
echo ""

echo "7. Verificando modelos actualizados..."
echo "----------------------------------------------------------------------"
check_file "app/Models/Media.php"
check_php_syntax "app/Models/Media.php"

# Verificar que Media tenga los nuevos métodos
if grep -q "isStoredExternally" app/Models/Media.php; then
    echo -e "${GREEN}✓${NC} Método isStoredExternally() encontrado en Media"
else
    echo -e "${RED}✗${NC} Método isStoredExternally() NO encontrado en Media"
    ((errors++))
fi

if grep -q "getAccessibleUrlAttribute" app/Models/Media.php; then
    echo -e "${GREEN}✓${NC} Accessor getAccessibleUrlAttribute() encontrado en Media"
else
    echo -e "${RED}✗${NC} Accessor getAccessibleUrlAttribute() NO encontrado en Media"
    ((errors++))
fi
echo ""

echo "8. Verificando enums..."
echo "----------------------------------------------------------------------"
if grep -q "EXTERNAL_STORAGE" app/Enums/PreferenceType.php; then
    echo -e "${GREEN}✓${NC} PreferenceType::EXTERNAL_STORAGE definido"
else
    echo -e "${RED}✗${NC} PreferenceType::EXTERNAL_STORAGE NO encontrado"
    ((errors++))
fi
echo ""

echo "9. Verificando rutas..."
echo "----------------------------------------------------------------------"
if php artisan route:list --name=setting.external_storage > /dev/null 2>&1; then
    ROUTE=$(php artisan route:list --name=setting.external_storage | grep "setting.external_storage")
    echo -e "${GREEN}✓${NC} Ruta registrada: setting.external_storage"
    echo "   $ROUTE"
else
    echo -e "${RED}✗${NC} Ruta setting.external_storage NO encontrada"
    ((errors++))
fi
echo ""

echo "10. Verificando providers..."
echo "----------------------------------------------------------------------"
check_php_syntax "app/Providers/AppServiceProvider.php"

if grep -q "registerDropboxDriver" app/Providers/AppServiceProvider.php; then
    echo -e "${GREEN}✓${NC} registerDropboxDriver() encontrado en AppServiceProvider"
else
    echo -e "${RED}✗${NC} registerDropboxDriver() NO encontrado en AppServiceProvider"
    ((errors++))
fi
echo ""

echo "11. Verificando seeders..."
echo "----------------------------------------------------------------------"
check_file "database/seeders/FileUploadEncounterSectionSeeder.php"
check_php_syntax "database/seeders/FileUploadEncounterSectionSeeder.php"

# Verificar si el seeder fue ejecutado
if php artisan tinker --execute="echo App\Models\EncounterSection::where('livewire_component_name', 'consultation.file-upload')->exists() ? 'yes' : 'no';" 2>/dev/null | grep -q "yes"; then
    echo -e "${GREEN}✓${NC} FileUploadEncounterSectionSeeder ejecutado correctamente"
else
    echo -e "${YELLOW}⚠${NC} FileUploadEncounterSectionSeeder no ha sido ejecutado"
    echo "   Ejecuta: php artisan db:seed --class=FileUploadEncounterSectionSeeder"
    ((warnings++))
fi
echo ""

echo "12. Verificando menú de navegación..."
echo "----------------------------------------------------------------------"
if grep -q "setting.external_storage" resources/views/layout/partials/sidebar.blade.php; then
    echo -e "${GREEN}✓${NC} Link agregado en sidebar"
else
    echo -e "${YELLOW}⚠${NC} Link NO encontrado en sidebar"
    ((warnings++))
fi
echo ""

echo "======================================================================"
echo "  RESUMEN DE VERIFICACIÓN"
echo "======================================================================"
if [ $errors -eq 0 ] && [ $warnings -eq 0 ]; then
    echo -e "${GREEN}✓ TODAS LAS VERIFICACIONES PASARON EXITOSAMENTE${NC}"
    echo ""
    echo "El sistema está listo para usar. Continúa con las pruebas manuales:"
    echo "1. Accede a: Configuraciones → Almacenamiento Externo"
    echo "2. Configura tu token de Dropbox"
    echo "3. Sube archivos en una consulta médica"
    echo ""
    echo "Para más detalles, consulta: EXTERNAL_STORAGE_TESTING.md"
elif [ $errors -eq 0 ]; then
    echo -e "${YELLOW}⚠ COMPLETADO CON ${warnings} ADVERTENCIA(S)${NC}"
    echo ""
    echo "El sistema puede funcionar, pero revisa las advertencias arriba."
else
    echo -e "${RED}✗ ENCONTRADOS ${errors} ERROR(ES) Y ${warnings} ADVERTENCIA(S)${NC}"
    echo ""
    echo "Por favor, corrige los errores antes de continuar."
    exit 1
fi

echo ""
echo "======================================================================"
