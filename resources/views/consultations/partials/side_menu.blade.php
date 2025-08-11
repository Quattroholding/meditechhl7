<div class="theme-settings22">
    <div class="patient-information-btn" style="background: rgb(45, 59, 165);"
         type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight{{$patient_id}}" aria-controls="offcanvasRight"
         role="button" tabindex="0">

        Ver Información del Paciente
    </div>
</div>
<div class="consultation-close-menu" onclick="change_menu_visibility()"
     role="button" tabindex="0" aria-label="Toggle menu visibility">
    <i class="fas fa-times me-1"></i>
    <span class="menu-text">CERRAR MENÚ</span>
</div>
<div class="menu-right" role="navigation" aria-label="Consultation sections menu">

    @php $i=0; @endphp
    @foreach($secciones as $k=>$v)
        @php $i++; @endphp
            <div id="menu-right-item-{{$i}}" class="menu-right-item menu-right-item-hiddable" onclick="scrollToMarker({{$k}})">
                <div id="mandatory-bullet-{{$i}}" class="mandatory-bullet-{{$i}} mandatory-bullet mandatory-bullet-on"></div>
                {{ $v }}
            </div>
    @endforeach
    <div class="my-3"></div>
    <livewire:consultation.finishedButton encounter_id="{{$encounter_id}}"/>
</div>

<script>
    let menu_visibility = true;

    function change_menu_visibility() {
        if (menu_visibility) {
            menu_visibility = false;
            $('.menu-right').addClass('menu-right-off');
        } else {
            menu_visibility = true;
            $('.menu-right').removeClass('menu-right-off');
        }
    }

    function scrollToMarker(markerId) {
        const marker = document.getElementById('section_marker_'+markerId);
        const parentMarker = document.getElementById('parent_section_marker_'+markerId);
        $("#section_marker_"+markerId).removeClass('hidden');

        if (marker) {
            parentMarker.scrollIntoView({
                behavior: 'smooth',  // Opción para un desplazamiento suave (opcional)
                block: 'start'      // Opción para alinear el elemento con la parte superior
            });
        } else {
            console.error(`No se encontró el elemento con el ID: ${markerId}`);
        }
    }

    // Ejemplo de uso:
    // Puedes llamar a esta función con el ID del elemento al que quieres desplazar
    // scrollToMarker('mi-marcador');
</script>
