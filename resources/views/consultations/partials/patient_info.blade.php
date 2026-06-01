<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight{{$id}}" aria-labelledby="offcanvasRightLabel" style="width: 1000px">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">{{ __('consultation.patient_info.patient_history') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div> <!-- end offcanvas-header-->

    <div class="offcanvas-body">
        <div class="col-sm-12">
            <div class="row">
                <div>
                    {{--}}
                    <livewire:consultation.patient-history patient_id="{{$id}}"/>
                    {{--}}

                    <livewire:patient.medical-history2 patient_id="{{$id}}" wire:key="{{time().$id}}"/>
                </div>
            </div>
        </div>
    </div> <!-- end offcanvas-body-->
</div> <!-- end offcanvas-->
