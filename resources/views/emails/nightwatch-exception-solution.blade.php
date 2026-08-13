@component('mail::message')
# 🤖 Solución AI para Excepción Detectada

**Excepción:** {{ $exceptionClass }}
**Prioridad:** <span style="color: {{ $priority === 'High' ? '#ef4444' : ($priority === 'Medium' ? '#f59e0b' : '#6b7280') }}">{{ $priority }}</span>

---

## 📋 Resumen de la Excepción

**Mensaje:** {{ $issueData['message'] }}
**Archivo:** `{{ $issueData['file'] }}:{{ $issueData['line'] }}`
**Ocurrencias:** {{ $issueData['occurrence_count'] }}
**Primera vez:** {{ $issueData['first_seen_at'] }}
**Última vez:** {{ $issueData['last_seen_at'] }}

---

## 💡 Análisis y Solución AI

{!! Str::markdown($aiSolution) !!}

---

@if(!empty($issueData['nightwatch_url']))
@component('mail::button', ['url' => $issueData['nightwatch_url']])
Ver Issue en Nightwatch
@endcomponent
@endif

**Nota:** Esta solución fue generada automáticamente por Claude AI. Por favor, revisa y prueba la solución antes de aplicarla en producción.

Saludos,
{{ config('app.name') }} - Sistema de Monitoreo AI
@endcomponent
