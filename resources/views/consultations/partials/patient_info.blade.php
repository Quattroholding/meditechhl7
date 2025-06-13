<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight{{$id}}" aria-labelledby="offcanvasRightLabel" style="width: 1000px">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">Historial Paciente</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div> <!-- end offcanvas-header-->

    <div class="offcanvas-body">
        <div class="col-sm-12">
            <div class="row">
                <div wire:init>
                    {{--}}
                    <livewire:consultation.patient-history patient_id="{{$id}}"/>
                    {{--}}

                    <livewire:patient.medical-history2 patient_id="{{$id}}"/>
                </div>
            </div>
        </div>
    </div> <!-- end offcanvas-body-->
</div> <!-- end offcanvas-->
