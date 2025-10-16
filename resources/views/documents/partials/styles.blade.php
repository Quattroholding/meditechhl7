<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'DejaVu Sans', Arial, sans-serif;
        font-size: 12px;
        line-height: 1.4;
        color: #000;
        background: #{{ $doctorProfile->recepy_background_color ?? 'ffffff' }};
        margin: 0;
        padding: 5mm;
        height: 150vh;
    }

    .document-container {
        border: 2px solid #000;
        border-radius: 15px;
        height: calc(100vh - 30mm);
        position: relative;
        padding: 10px;
        page-break-after: always;
        box-sizing: border-box;
    }

    .document-container:last-child {
        page-break-after: avoid;
    }

    /* Header Styles */
    .header-section {
        display: table;
        width: 100%;
        margin-bottom: 15px;
        min-height: 110px;
    }

    .logo-section {
        display: table-cell;
        width: 25%;
        text-align: center;
        vertical-align: middle;
        padding: 5px;
    }

    .facility-logo {
        max-width: 90px;
        max-height: 90px;
    }

    .practitioner-info {
        display: table-cell;
        width: 50%;
        text-align: center;
        vertical-align: middle;
        padding: 10px;
        font-size: 12px;
    }

    .facility-info {
        display: table-cell;
        width: 25%;
        vertical-align: middle;
        padding: 5px;
        font-size: 11px;
        text-align: right;
    }

    /* Patient Info Styles */
    .patient-info-section {
        margin-bottom: 15px;
        font-size: 12px;
    }

    .patient-info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 5px;
    }

    .patient-info-table td {
        padding: 2px 5px;
        vertical-align: bottom;
    }

    .patient-info-table td.label {
        font-weight: bold;
        white-space: nowrap;
    }

    .patient-info-table td.value {
        border-bottom: 1px solid #000;
        padding-bottom: 1px;
    }

    /* Content Section Styles */
    .content-section {
        border: 2px solid #000;
        border-radius: 10px;
        margin-bottom: 15px;
        position: relative;
    }

    .section-header {
        position: absolute;
        top: 2px;
        left: 10px;
        background: {{ '#' . ($doctorProfile->recepy_background_color ?? 'ffffff') }};
        padding: 0 5px;
        font-weight: bold;
        font-size: 14px;
    }

    .section-content {
        padding: 30px 15px 15px 15px;
        font-size: 11px;
    }

    .item {
        margin-bottom: 15px;
        font-size: 11px;
    }

    .item-name {
        font-weight: bold;
        margin-bottom: 3px;
    }

    .item-details {
        margin-left: 10px;
        margin-bottom: 5px;
    }

    /* Footer Styles */
    .footer-section {
        position: relative;
        width: 100%;
        margin-top: 20px;
        min-height: 80px;
    }

    .document-number-footer {
        position: absolute;
        left: 10px;
        top: 50px;
        color: red;
        font-weight: bold;
        font-size: 16px;
    }

    .seal-section {
        position: initial;
        bottom: 100px;
        right: 10px;
        text-align: right;
        z-index: 10;
    }

    .signature-section {
        position: initial;
        bottom: 40px;
        right: 10px;
        text-align: center;
        z-index: 10;
    }

    .signature-line {
        border-top: 1px solid #000;
        width: 350px;
        margin-bottom: 5px;
        margin-top: 20px;
        margin-left: 380px;
    }

    .signature-text {
        font-size: 10px;
        font-weight: bold;
        margin-left: 330px;
    }

    .doctor-signature {
        max-width: 200px;
        max-height: 60px;
        margin-bottom: 5px;
    }

    .doctor-seal {
        max-width: 200px;
        max-height: 60px;
        margin-left: 10px;
        vertical-align: top;
    }

    .rx-section {
        min-height: 380px;
    }

    .dx-section {
        min-height: 230px;
    }

    @page {
        margin: 0;
    }
</style>
