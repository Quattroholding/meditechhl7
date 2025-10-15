<div class="personal-notes-content">
    @if($sectionData && count($sectionData) > 0)
        <!-- Header -->
        <div style="margin-bottom: 25px;">
            <h3 style="margin: 0; color: #1e293b; font-size: 20px; font-weight: 600;">
                📒 Notas Personales del Paciente
            </h3>
            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">
                Registro de notas privadas sobre el paciente ({{ count($sectionData) }} {{ count($sectionData) == 1 ? 'nota' : 'notas' }})
            </p>
        </div>

        <!-- Notes Table -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="min-width: 100%;">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #e5e7eb;">
                        <tr>
                            <th style="padding: 16px; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; width: 150px;">
                                Fecha
                            </th>
                            <th style="padding: 16px; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                Nota
                            </th>
                            <th style="padding: 16px; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; width: 150px;">
                                Creado por
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sectionData as $index => $personalNote)
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#f9fafb'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <!-- Fecha -->
                            <td style="padding: 16px; vertical-align: top;">
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span style="font-weight: 600; color: #1e293b; font-size: 14px;">
                                        {{ $personalNote->created_at }}
                                    </span>
                                </div>
                            </td>

                            <!-- Nota -->
                            <td style="padding: 16px;">
                                <div style="color: #374151; font-size: 14px; line-height: 1.6; max-width: 600px;">
                                    @if(strlen($personalNote->note) > 200)
                                        <div x-data="{ expanded: false }">
                                            <div x-show="!expanded">
                                                <p style="margin: 0; white-space: pre-wrap;">{{ Str::limit($personalNote->note, 200) }}</p>
                                                <button @click="expanded = true"
                                                        style="margin-top: 8px; color: #3b82f6; background: none; border: none; padding: 0; font-size: 13px; cursor: pointer; font-weight: 500;"
                                                        onmouseover="this.style.textDecoration='underline'"
                                                        onmouseout="this.style.textDecoration='none'">
                                                    Ver más →
                                                </button>
                                            </div>
                                            <div x-show="expanded" x-cloak>
                                                <p style="margin: 0; white-space: pre-wrap;">{{ $personalNote->note }}</p>
                                                <button @click="expanded = false"
                                                        style="margin-top: 8px; color: #3b82f6; background: none; border: none; padding: 0; font-size: 13px; cursor: pointer; font-weight: 500;"
                                                        onmouseover="this.style.textDecoration='underline'"
                                                        onmouseout="this.style.textDecoration='none'">
                                                    Ver menos ←
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <p style="margin: 0; white-space: pre-wrap;">{{ $personalNote->note }}</p>
                                    @endif
                                </div>
                            </td>

                            <!-- Creado por -->
                            <td style="padding: 16px; vertical-align: top;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    @if($personalNote->user)
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 12px; flex-shrink: 0;">
                                            {{ strtoupper(substr($personalNote->user->name, 0, 1)) }}
                                        </div>
                                        <div style="display: flex; flex-direction: column; min-width: 0;">
                                            <span style="font-weight: 500; color: #1e293b; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $personalNote->user->name }}
                                            </span>
                                            <span style="color: #64748b; font-size: 11px;">
                                                {{ $personalNote->user->getRoleNames()->first() ?? 'Usuario' }}
                                            </span>
                                        </div>
                                    @else
                                        <span style="color: #9ca3af; font-size: 13px;">Sistema</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Info -->
            <div style="padding: 16px; background: #f8fafc; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                <div style="color: #64748b; font-size: 14px;">
                    Mostrando <strong>{{ count($sectionData) }}</strong> {{ count($sectionData) == 1 ? 'nota' : 'notas' }}
                </div>
                <div style="color: #64748b; font-size: 12px;">
                    💡 Notas privadas solo visibles para el equipo médico
                </div>
            </div>
        </div>

        <!-- CSS adicional -->
        <style>
            [x-cloak] { display: none !important; }
        </style>

    @else
        <!-- Empty State -->
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">📒</div>
            <h3 style="margin: 0 0 10px 0; color: #1e293b; font-size: 20px; font-weight: 600;">
                No hay Notas Personales Registradas
            </h3>
            <p style="margin: 0; font-size: 14px;">
                Este paciente no tiene notas personales registradas en el período seleccionado.
            </p>
        </div>
    @endif
</div>
