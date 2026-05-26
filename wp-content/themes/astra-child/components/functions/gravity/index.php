<?php

// Lee una propiedad tanto si el dato llega como array como si llega como objeto.
function resultados_encuesta_chart_get_field_property($item, $property, $default = null) {
    if (is_array($item) && array_key_exists($property, $item)) {
        return $item[$property];
    }

    if (is_object($item) && isset($item->{$property})) {
        return $item->{$property};
    }

    return $default;
}


// Busca un field concreto dentro de la definicion completa del formulario.
function resultados_encuesta_chart_get_field($form, $field_id) {
    $fields = resultados_encuesta_chart_get_field_property($form, 'fields', array());

    foreach ($fields as $field) {
        if ((string) resultados_encuesta_chart_get_field_property($field, 'id') === (string) $field_id) {
            return $field;
        }
    }

    return null;
}


// Obtiene el subtipo real del campo Survey para saber como leer sus respuestas.
function resultados_encuesta_chart_get_input_type($field) {
    $type = resultados_encuesta_chart_get_field_property($field, 'inputType');

    if (empty($type)) {
        $type = resultados_encuesta_chart_get_field_property($field, 'input_type');
    }

    if (empty($type) && is_object($field) && method_exists($field, 'get_input_type')) {
        $type = $field->get_input_type();
    }

    if (empty($type)) {
        $type = resultados_encuesta_chart_get_field_property($field, 'type', '');
    }

    return strtolower((string) $type);
}


// Devuelve el tipo base del field tal como lo define Gravity Forms.
function resultados_encuesta_chart_get_field_type($field) {
    return strtolower((string) resultados_encuesta_chart_get_field_property($field, 'type', ''));
}


// Filtra todos los fields del formulario y conserva solo los de tipo encuesta.
function resultados_encuesta_chart_get_survey_fields($form) {
    $survey_fields = array();
    $fields        = resultados_encuesta_chart_get_field_property($form, 'fields', array());

    foreach ($fields as $field) {
        if (resultados_encuesta_chart_get_field_type($field) !== 'survey') {
            continue;
        }

        $survey_fields[] = $field;
    }

    return $survey_fields;
}


// Normaliza y valida el tipo de grafico permitido antes de pasarlo a Chart.js.
function resultados_encuesta_chart_get_chart_type($chart_type) {
    $allowed_types = array('bar', 'line', 'pie', 'doughnut', 'polarArea', 'radar');
    $normalized    = strtolower(trim((string) $chart_type));

    if ($normalized === 'polararea') {
        return 'polarArea';
    }

    if (!in_array($normalized, array_map('strtolower', $allowed_types), true)) {
        return 'bar';
    }

    foreach ($allowed_types as $allowed_type) {
        if (strtolower($allowed_type) === $normalized) {
            return $allowed_type;
        }
    }

    return 'bar';
}


// Convierte el valor guardado en la entrada a la etiqueta visible de la opcion.
function resultados_encuesta_chart_get_choice_label($field, $stored_value) {
    $stored_value = trim((string) $stored_value);

    if ($stored_value === '') {
        return '';
    }

    $choices = resultados_encuesta_chart_get_field_property($field, 'choices', array());

    foreach ($choices as $choice) {
        $choice_text  = (string) resultados_encuesta_chart_get_field_property($choice, 'text', '');
        $choice_value = resultados_encuesta_chart_get_field_property($choice, 'value', '');
        $choice_value = $choice_value !== '' ? (string) $choice_value : $choice_text;

        if ((string) $choice_value === $stored_value || $choice_text === $stored_value) {
            return $choice_text !== '' ? $choice_text : $stored_value;
        }
    }

    return $stored_value;
}


// Interpreta respuestas de tipo rank tanto en JSON como en texto separado por comas.
function resultados_encuesta_chart_parse_rank_answers($value, $field) {
    $value = trim((string) $value);

    if ($value === '') {
        return array();
    }

    $answers = array();
    $decoded = json_decode($value, true);

    if (is_array($decoded)) {
        $answers = $decoded;
    } else {
        $answers = array_map('trim', explode(',', $value));
    }

    $answers = array_filter($answers, static function($answer) {
        return $answer !== '';
    });

    return array_map(static function($answer) use ($field) {
        return resultados_encuesta_chart_get_choice_label($field, $answer);
    }, $answers);
}


// Extrae una o varias respuestas de una entrada segun el subtipo del campo Survey.
function resultados_encuesta_chart_get_entry_answers($entry, $field) {
    $field_id    = (string) resultados_encuesta_chart_get_field_property($field, 'id');
    $input_type  = resultados_encuesta_chart_get_input_type($field);
    $field_value = isset($entry[$field_id]) ? $entry[$field_id] : '';

    if ($input_type === 'checkbox') {
        $answers = array();
        $inputs  = resultados_encuesta_chart_get_field_property($field, 'inputs', array());

        foreach ($inputs as $input) {
            $input_id    = (string) resultados_encuesta_chart_get_field_property($input, 'id');
            $input_label = (string) resultados_encuesta_chart_get_field_property($input, 'label', '');
            $input_value = isset($entry[$input_id]) ? trim((string) $entry[$input_id]) : '';

            if ($input_value === '') {
                continue;
            }

            $answers[] = $input_label !== '' ? $input_label : resultados_encuesta_chart_get_choice_label($field, $input_value);
        }

        if (!empty($answers)) {
            return $answers;
        }

        return resultados_encuesta_chart_parse_rank_answers($field_value, $field);
    }

    if ($input_type === 'likert') {
        $answers = array();
        $inputs  = resultados_encuesta_chart_get_field_property($field, 'inputs', array());

        foreach ($inputs as $input) {
            $input_id    = (string) resultados_encuesta_chart_get_field_property($input, 'id');
            $input_value = isset($entry[$input_id]) ? trim((string) $entry[$input_id]) : '';

            if ($input_value === '') {
                continue;
            }

            $answers[] = resultados_encuesta_chart_get_choice_label($field, $input_value);
        }

        if (!empty($answers)) {
            return $answers;
        }
    }

    if ($input_type === 'rank') {
        return resultados_encuesta_chart_parse_rank_answers($field_value, $field);
    }

    $field_value = trim((string) $field_value);

    if ($field_value === '') {
        return array();
    }

    return array(resultados_encuesta_chart_get_choice_label($field, $field_value));
}




?>