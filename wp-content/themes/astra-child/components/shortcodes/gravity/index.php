<?php


// Genera un grafico por cada campo Survey del formulario usando las entradas activas.

add_shortcode('resultados_encuesta_chart', 'resultados_encuesta_chart_shortcode');   


// Genera colores aleatorios para asignar un color distinto a cada barra de la encuesta.
function resultados_encuesta_chart_get_bar_colors($total_items) {
    $background_colors = array();
    $border_colors     = array();

    for ($index = 0; $index < $total_items; $index++) {
        $red   = wp_rand(40, 215);
        $green = wp_rand(40, 215);
        $blue  = wp_rand(40, 215);

        $background_colors[] = 'rgba(' . $red . ', ' . $green . ', ' . $blue . ', 0.7)';
        $border_colors[]     = 'rgb(' . $red . ', ' . $green . ', ' . $blue . ')';
    }

    return array(
        'background' => $background_colors,
        'border'     => $border_colors,
    );
}


// Normaliza el numero de columnas para la rejilla flexible de graficas.
function resultados_encuesta_chart_get_columns($columns) {
    $columns = absint($columns);

    if ($columns < 1) {
        return 1;
    }

    return min($columns, 6);
}

function resultados_encuesta_chart_shortcode($atts) {
    if (!class_exists('GFAPI')) {
        return 'Gravity Forms no está activo.';
    }

    $atts = shortcode_atts([
        'form_id'  => '',
        'tipo'     => 'bar',
        'columnas' => 2,
       
    ], $atts);

    $form_id      = absint($atts['form_id']);
    $chart_type   = resultados_encuesta_chart_get_chart_type($atts['tipo']);
    $columns      = resultados_encuesta_chart_get_columns($atts['columnas']);
    $column_gap   = 24;
    $item_width   = 'calc((100% - ' . (($columns - 1) * $column_gap) . 'px) / ' . $columns . ')';
    $wrapper_style = 'display:flex;flex-wrap:wrap;gap:' . $column_gap . 'px;max-width:1400px;margin:40px auto;align-items:stretch;';
    $item_style    = 'flex:1 1 ' . $item_width . ';max-width:' . $item_width . ';min-width:280px;';

    if (empty($form_id)) {
        return 'Debes indicar el formulario. Ejemplo: [resultados_encuesta_chart form_id="1"]';
    }
    $form      = GFAPI::get_form($form_id);

    if (is_wp_error($form) || empty($form)) {
        return 'No se ha podido cargar el formulario.';
    }

    $fields = resultados_encuesta_chart_get_survey_fields($form);

    if (empty($fields)) {
        return 'Este formulario no tiene campos de encuesta.';
    }
	

    $entries = GFAPI::get_entries($form_id, [
        'status' => 'active'
    ], null, [
        'offset'    => 0,
        'page_size' => 10000
    ]);

	

    if (is_wp_error($entries) || empty($entries)) {
        return 'Todavía no hay resultados.';
    }

    ob_start();


    echo '<div class="gf-survey-results-wrap" style="' . esc_attr($wrapper_style) . '">';

    foreach ($fields as $field) {
        $resultados = [];

        foreach ($entries as $entry) {
            $valores = resultados_encuesta_chart_get_entry_answers($entry, $field);

            foreach ($valores as $valor) {
                if ($valor === '') {
                    continue;
                }

                if (!isset($resultados[$valor])) {
                    $resultados[$valor] = 0;
                }

                $resultados[$valor]++;
            }
        }

	

        if (empty($resultados)) {
            continue;
        }

        $labels = array_keys($resultados);
        $data   = array_values($resultados);
        $total  = array_sum($data);
        $field_id = resultados_encuesta_chart_get_field_property($field, 'id');
        $title  = resultados_encuesta_chart_get_field_property($field, 'label', 'Resultados pregunta ' . $field_id);
        $colors = $chart_type === 'bar' ? resultados_encuesta_chart_get_bar_colors(count($data)) : null;

        $canvas_id = 'chart_encuesta_' . uniqid();
        ?>

        <div class="gf-survey-results-item" style="<?php echo esc_attr($item_style); ?>">
            <div class="gf-survey-results-card">
                <h3 class="gf-survey-results-title"><?php echo esc_html($title); ?></h3>

                <div class="gf-survey-results-canvas-wrap">
                    <canvas id="<?php echo esc_attr($canvas_id); ?>"></canvas>
                </div>

                <p class="gf-survey-results-meta">
                Total respuestas:
                <strong><?php echo esc_html($total); ?></strong>
                </p>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {

            const ctx = document.getElementById('<?php echo esc_js($canvas_id); ?>');

            if (!ctx) return;

            new Chart(ctx, {
                type: <?php echo wp_json_encode($chart_type); ?>,
                data: {
                    labels: <?php echo wp_json_encode($labels); ?>,
                    datasets: [{
                        label: 'Votos',
                        data: <?php echo wp_json_encode($data); ?>,
                        backgroundColor: <?php echo wp_json_encode($colors ? $colors['background'] : '#33658a'); ?>,
                        borderColor: <?php echo wp_json_encode($colors ? $colors['border'] : '#33658a'); ?>,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = <?php echo (int) $total; ?>;
                                    const value = context.raw;
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return value + ' votos (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

        });
        </script>

        <?php
    }

    echo '</div>';

    echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';

    return ob_get_clean();
}   