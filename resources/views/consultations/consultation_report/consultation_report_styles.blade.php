<style>
    body {
        font-size: 12px;
        font-family: Sans-serif;
    }

    .table {
        width: 100vw;
        margin-bottom: 20px;
    }

    .table td {
        padding: 0px;
    }

    .table-head {
        font-size: 15px;
        height: 50px;
        vertical-align: center;
        font-weight: bold;
        text-align: left;
    }

    .table-title {
        font-weight: bold;
        font-size: 14px;
        height: 30px;
        text-align: center;
        background-color: #ededed;
    }

    .table-value {
        border: 1px solid #ededed;
        text-align: center;
        padding-top: 10px;
        font-size: 14px;
        height: 30px;
        padding-bottom: 10px;
        margin: 0px;
    }

    .gray-box {
        width: 100%;
        min-height: 10px;
        background-color: #FFFFFF;
        border: 1px dotted #8BC443;
        border-left: 3px solid rgba(0, 93, 184, 1);
        border-radius: 0px;
        padding: 10px;
        font-weight: bold;
        line-height: 20px;
        color: #005DB8;
        font-size: 12px;


    }

    .white-box {
        width: 100%;
        min-height: 10px;
        padding: 10px;
    }

    .right {
        text-align: right;
    }

    .title {
        font-weight: bold;
        font-size: 17px;
    }

    .subtitle {
        font-weight: bold;
        font-size: 16px;
        line-height: 12px;
        padding-bottom: 10px;
        padding-top: 20px;

    }


    .subtitle-header {
        font-weight: bold;
        font-size: 12px;
        line-height: 12px;
        padding-bottom: 8px;
        padding-top: 15px;
    }

    .upper {
        text-transform: uppercase;
    }

    header {
        @if(request()->has('html'))
          position: relative;
        @else
          position: fixed;
        @endif
        top: 0px;
        left: 0px;
        width: 100%;
        height: 150px;

    }
    @if(request()->has('html'))
      footer {
        position: fixed;
        bottom: -10px;
        left: 545px;
        width: 100%;
        height: 180px;
        max-width: 800px;
        margin:0 auto;
    }
    @else
      footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            width: 100%;
            height: 140px;
            max-width: 800px;
            margin:0 auto;
        }
    @endif

    body {
        @if(request()->has('html'))
          padding-top: 50px;
        @else
          padding-top: 160px;
        @endif
          padding-bottom: 70px;
    }

    .subtitle span {
        color: {{$color_texto}};
        font-weight: lighter;

    }

    .value {
        color: #171717;
        /*
        font-weight: 400;
        letter-spacing: 1px;
        font-size:12px;
        */
        text-decoration: none !important;
        background-color: #f8f6f6;
        border: 1px solid #e8e8e8;
        padding: 20px;
        font-size: 12px;
        line-height: 22px;

    }

    .space {
        width: 100%;
        height: 10px;
    }

    .meds-table {
        font-size: 12px;
    }

    .meds-table tr td:nth-child(2) {
        border-left: 1px dotted #ddd;
        padding-left: 10px;
    }

    .meds-table table tr:nth-child(2) td {
        border-top: 1px dotted #ddd;
        padding-top: 10px;
    }

    .result-table td,.result-table-multiple-lines td, .meds-table td {
        padding-bottom: 10px;
        vertical-align: top;
    }

    .result-table li,.result-table-multiple-lines li, .meds-table li {
        list-style-type: square;
        padding: 0px;
        margin: 0px;

    }

    .result-table ul,.result-table-multiple-lines ul, .meds-table ul {
        padding: 0px;
        margin: 0px;

    }

    .result-table,.result-table-multiple-lines, .meds-table {
        width: 100%;
    }

    .result-table tr td,.result-table-multiple-lines tr td {
        padding-left: 10px;
        padding-right: 10px;
    }

    .result-table tr td:last-child,.result-table-multiple-lines tr td:last-child {
        width: 70%;
        font-size: 12px;
        line-height: 22px;
        padding-right: 20px;
    }

    .result-table td,.result-table-multiple-lines td {
        width: 70%;
        padding-top: 10px;
        padding-bottom: 10px;
        min-height: 30px;
    }

    .result-table tr td:first-child,.result-table-multiple-lines  tr td:first-child{
        width: 30%;
        padding-left: 20px;
        font-weight: bold;
    }

    .meds-table th {
        padding: 10px;
    }

    .meds-table td u {
        font-weight: bold;
    }

    .result-table tr:nth-child(odd), .meds-table tr:nth-child(odd) {
        background-color: #f2f2f2;
    }


    .label {
        padding: 5px;
        margin: 5px;
        font-weight: bold;
        color: #005DB8;


    }

    .notes-title-label {
        color: #000000;
        margin-top: 20px;
        margin-bottom: 20px;
        font-weight: bold;
    }

    .paragraph {
        background-color: #f5f5f5;
        border: 1px solid #e8e8e8;
        width: 100%;
        padding: 20px;
        font-size: 12px;
        line-height: 22px;

        text-align: justify;
    }


    .table-inner-content {
        width: 100%;
        padding: 0px;
        margin: 0px;
    }

    .table-inner-content tr:nth-child(even) {
        background-color: #ececec;
    }

    .trnobg {
        background-color: #ffffff00 !important;
    }

    .table-inner-content td {
        padding: 10px;
    }

    .table-contents td {
        color: #000000;
        vertical-align: top;
    }

    .pagenum:before {
        content: counter(page);
    }

    .pagetotal:before {
        content: counter(pageTotal);
    }

    .sello {
        width: 175px;
        transform: rotate({{rand(1,5)}}deg);
    }

    .firma {
        width: 175px;
        transform: rotate({{rand(-5,5)}}deg);
        width: 50%;
    }

    .tabla_firma {
        width: 300px;
        position: fixed;
        bottom: 110px;
        right: 1%;
    }

    .tabla_firma img {
        width: 170px;
    }

    .table-inner-content-head {
        background-color: #f5f5f5;
        border: 1px solid #e8e8e8;
        font-weight: bold;
    }



    .consultation-report-vital-signs .value{
        padding: 5px 10px;
        margin-left:10px;
    }

    .consultation-report-vital-signs .values-container {
        display: inline-flex;
        margin-right: 10px;
        margin-top: 20px;
    }
</style>
