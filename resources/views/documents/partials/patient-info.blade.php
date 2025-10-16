{{-- Patient Information Section --}}
<div class="patient-info-section">
    <!-- Primera línea: Nombre y Edad -->
    <table class="patient-info-table">
        <tr>
            <td class="label" style="width: 8%;">Nombre :</td>
            <td class="value" style="width: 75%;">{{ $patient->name }}</td>
            <td class="label" style="width: 5%;">Edad :</td>
            <td class="value" style="width: 13%;">{{ $patient->age }}</td>
        </tr>
    </table>

    <!-- Segunda línea: Identificación y Fecha -->
    <table class="patient-info-table">
        <tr>
            <td class="label" style="width: 5%;">No. de ID :</td>
            <td class="value" style="width: 22%;">{{ $patient->identifier ?? '' }}</td>
            <td class="label" style="width: 5%;">Fecha :</td>
            <td class="value" style="width: 28%;">{{ \Carbon\Carbon::parse($encounter->created_at)->format('d/m/Y') }}</td>
        </tr>
    </table>
</div>
