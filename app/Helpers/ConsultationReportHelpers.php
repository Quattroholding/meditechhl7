<?php

if (! function_exists('line_if_empty')) {
    /**
     * Retorna "---" si el valor está vacío o es "-"
     */
    function line_if_empty($value = '')
    {
        if ($value == '' || $value == '-') {
            return '---';
        }

        return $value;
    }
}

if (! function_exists('list_array_as_table')) {
    /**
     * Convierte un array en filas de tabla HTML
     */
    function list_array_as_table($array)
    {
        $html = '';
        $a = 0;

        foreach ($array as $item) {
            $a++;
            $html .= '<tr class="table-contents">';
            $html .= $item;
            $html .= '</tr>';
        }

        return $html;
    }
}

if (! function_exists('list_array')) {
    /**
     * Convierte un array en lista HTML
     */
    function list_array($array)
    {
        $html = '<ul>';
        foreach ($array as $item) {
            $html .= '<li>'.$item.'</li>';
        }
        $html .= '</ul>';

        return $html;
    }
}

if (! function_exists('get_from_array')) {
    /**
     * Obtiene un valor de un array por su ID
     */
    function get_from_array($list, $id = '')
    {
        if ($id == '') {
            return '';
        }

        if (isset($list[$id])) {
            return $list[$id];
        } else {
            return '';
        }
    }
}

if (! function_exists('gen')) {
    /**
     * Genera una tabla HTML para reportes de consulta
     */
    function gen($table, $horizontal = true)
    {
        ?>

<table class="table" cellspacing="0" cellpadding="0">

    @if(isset($table['t-title']))
        <tr style="min-height: 50px">
            <td colspan="10" class="table-head">{{ $table['t-title'] }}</td>
        </tr>
    @endif


    @if($horizontal)
        <tr>
                <?php
            foreach ($table as $key => $item) {
                if ($key != 't-title') { ?>

            <td class="table-title">

                {{ $key }}
            </td>


                <?php
                }
            } ?>
        </tr>

        <tr>
                <?php
            foreach ($table as $key => $item) {
                if ($key != 't-title') { ?>

            <td class="table-value">
                {!! $item !!}
            </td>


                <?php
                }
            } ?>

        </tr>

    @endif


</table>

        <?php
    }
}
