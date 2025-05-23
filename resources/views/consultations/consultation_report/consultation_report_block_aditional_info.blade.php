<table class="custom-table">
    <tr>
        <td><b>{{__('consultation.user_identification')}}:</b> <div class="aditional-info-field">{{ $data->patient->identifier }}</div></td>
        <td><b>{{__('consultation.fullname')}}:</b> <div class="aditional-info-field">{{ $data->patient->name }}</div></td>
        <td><b>{{__('consultation.marital_status')}}:</b> <div class="aditional-info-field">{{ $data->patient->marital_status }}</div></td>
        <td><b>{{__('consultation.occupation')}}:</b> <div class="aditional-info-field">***</div></td>
    </tr>
    <tr>
        <td><b>{{__('consultation.address')}}:</b> <div class="aditional-info-field">{{ $data->patient->address }}</div></td>
        <td><b>{{__('consultation.home_phone')}}:</b> <div class="aditional-info-field">{{ $data->patient->phone }}</div></td>
        <td><b>{{__('consultation.residence_place')}}:</b> <div class="aditional-info-field">***</div></td>
        {{--}}
        <td><b>{{__('consultation.companion')}}:</b> <div class="aditional-info-field">{{ $data['consultation']['companion'] }}</div></td>
        {{--}}
    </tr>
    {{--}}
    <tr>
        <td><b>{{__('consultation.companion_phone')}}:</b> <div class="aditional-info-field">{{ $data['consultation']['companion_phone'] }}</div></td>
        <td><b>{{__('consultation.responsible')}}:</b> <div class="aditional-info-field">{{ $data['consultation']['responsible'] }}</div></td>
        <td><b>{{__('consultation.responsible_phone')}}:</b> <div class="aditional-info-field">{{ $data['consultation']['responsible_phone'] }}</div></td>
        <td><b>{{__('consultation.responsible_relationship')}}:</b> <div class="aditional-info-field">{{ $data['consultation']['responsible_relationship'] }}</div></td>
    </tr>

    <tr>
        <td><b>{{__('consultation.insurance_company')}}:</b> <div class="aditional-info-field">{{ $data->patient->['patient_type'] }}</div></td>
        <td><b>{{__('consultation.linkage_type')}}:</b> <div class="aditional-info-field">***</div></td>
        <td></td>
        <td></td>
    </tr>
   {{--}}
</table>

<style>
    .aditional-info-field {
        display: inline-block;
        margin-left: 10px;
        margin-top: 5px;
        min-width: 100px;
        background-color: #f6f6f6;
        min-height: 20px;
        padding-left: 10px;
        font-size: 9px; /* Reducción del tamaño de la fuente */
        padding-right: 10px;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
    }

    .custom-table td {
        padding: 5px 10px;
    }
</style>
