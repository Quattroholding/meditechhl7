{{-- Header Section with 3 columns: Logo (25%) | Practitioner Info (50%) | Facility Info (25%) --}}
<div class="header-section">
    <!-- Column 1: Logo (25%) -->
    <div class="logo-section">
        @if($doctorProfile && file_exists(public_path('storage/' . $doctorProfile->logo)))
            <img src="data:image/{{ pathinfo($doctorProfile->logo, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->logo))) }}" alt="Logo" class="facility-logo">
        @endif
    </div>

    <!-- Column 2: Practitioner Info (50%) -->
    <div class="practitioner-info">
        <strong>{{ $practitioner->name }}</strong><br>
        <strong style="font-size: 18px">{{ $encounter->appointment->medicalSpeciality->name ?? '' }}</strong>
    </div>

    <!-- Column 3: Facility Info (25%) -->
    <div class="facility-info">
        @if($doctorProfile)
            {{ $doctorProfile->facility ?? '' }}<br>
            {{ $doctorProfile->address ?? '' }}<br>
            Tel: {{ $doctorProfile->phone ?? '' }}
        @elseif($encounter->appointment->client)
            {{ $encounter->appointment->client->name ?? '' }}<br>
            {{ $encounter->appointment->client->address ?? '' }}<br>
            Tel: {{ $encounter->appointment->client->whatsapp ?? '' }}
        @endif
    </div>
</div>
