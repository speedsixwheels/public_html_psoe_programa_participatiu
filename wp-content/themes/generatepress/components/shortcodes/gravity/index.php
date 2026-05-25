<?php


// Genera un grafico por cada campo Survey del formulario usando las entradas activas.

add_shortcode('resultados_encuesta_chart', 'resultados_encuesta_chart_shortcode'); 

function resultados_encuesta_chart_shortcode($atts) {
    if (!class_exists('GFAPI')) {
        return 'Gravity Forms no está activo.';
    }

    $atts = shortcode_atts([
        'form_id' => '',
        'tipo'    => 'bar',
       
    ], $atts);

    $form_id    = absint($atts['form_id']);
    $chart_type = resultados_encuesta_chart_get_chart_type($atts['tipo']);

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

    echo '<div class="gf-survey-results-wrap" style="max-width:750px;margin:40px auto;">';

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

        $canvas_id = 'chart_encuesta_' . uniqid();
        ?>

        <div style="margin-bottom:50px;">
            <h3><?php echo esc_html($title); ?></h3>

            <canvas id="<?php echo esc_attr($canvas_id); ?>"></canvas>

            <p style="margin-top:15px;font-size:14px;">
                Total respuestas:
                <strong><?php echo esc_html($total); ?></strong>
            </p>
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