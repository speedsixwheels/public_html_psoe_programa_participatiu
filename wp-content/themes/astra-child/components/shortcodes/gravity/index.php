<?php





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

// Genera un grafico por cada campo Survey del formulario usando las entradas activas.

add_shortcode('resultados_encuesta_chart', 'resultados_encuesta_chart_shortcode');   
add_shortcode('resultados_encuesta_doughnut', 'resultados_encuesta_doughnut_shortcode');

function resultados_encuesta_doughnut_shortcode($atts) {
    $atts['tipo'] = 'doughnut';

    return resultados_encuesta_chart_shortcode($atts);
}

function resultados_encuesta_chart_shortcode($atts) {
    if (!class_exists('GFAPI')) {
        return 'Gravity Forms no está activo.';
    }

    $atts = shortcode_atts([
        'form_id'  => '',
        'tipo'     => 'bar',
        'columnas' => 1,
       
    ], $atts);

    $form_id      = absint($atts['form_id']);
    $chart_type   = resultados_encuesta_chart_get_chart_type($atts['tipo']);
    $columns      = resultados_encuesta_chart_get_columns($atts['columnas']);
    $column_gap   = 24;
    $wrap_style   = '--gf-survey-columns:' . $columns . ';--gf-survey-gap:' . $column_gap . 'px;';
    $item_width   = 'calc((100% - (var(--gf-survey-columns) - 1) * var(--gf-survey-gap)) / var(--gf-survey-columns))';
    $item_style   = 'flex:1 1 ' . $item_width . ';max-width:' . $item_width . ';min-width:280px;';

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


    echo '<div class="gf-survey-results-wrap" style="' . esc_attr($wrap_style) . '">';

  


    foreach ($fields as $field) {
        $resultados = [];

        $resultados_altres = [];

       

        foreach ($entries as $entry) {
                 $entry_con_labels = resultados_encuesta_chart_get_entry_labeled_values($entry, $form);

              

                foreach ($entry_con_labels as $entry_key => $entry_data) {
                
                    if (strpos($entry_key, 'altres') !== false) {
                        $field_values = is_array($entry_data['value']) ? $entry_data['value'] : array($entry_data['value']);
                        foreach ($field_values as $field_value) {
                            if ($field_value !== '') {
                                if (!isset($resultados_altres[$field_value])) {
                                    $resultados_altres[$field_value] = [
                                        'entry_key'   => $entry_key,
                                        'field_value' => $field_value,
                                        'count'       => 0,
                                    ];
                                }

                                $resultados_altres[$field_value]['count']++;
                            }
                        }
                    }
                }

          
                 
          
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
        $admin_label = trim((string) resultados_encuesta_chart_get_field_property($field, 'adminLabel', ''));
        $uses_segment_colors = in_array($chart_type, array('bar', 'pie', 'doughnut', 'polarArea'), true);
        $shows_legend        = in_array($chart_type, array('pie', 'doughnut', 'polarArea'), true);
        $is_horizontal_bar   = $chart_type === 'bar';
        $uses_html_legend    = $chart_type === 'doughnut';
        $legend_position     = $chart_type === 'doughnut' ? 'bottom' : 'right';
        $colors = $uses_segment_colors ? resultados_encuesta_chart_get_bar_colors(count($data)) : null;
        $canvas_height = 280;

        if ($is_horizontal_bar) {
            $canvas_height = max(280, count($labels) * 52);
        } elseif ($shows_legend && !$uses_html_legend) {
            $canvas_height = max(280, count($labels) * 30);
        }

        $canvas_id = 'chart_encuesta_' . uniqid();
        ?>

        <div class="gf-survey-results-item" style="<?php echo esc_attr($item_style); ?>">
            <div class="gf-survey-results-card">
                <h3 class="gf-survey-results-title"><?php echo esc_html($title); ?></h3>
           

                <div class="gf-survey-results-canvas-wrap" style="height:<?php echo esc_attr($canvas_height); ?>px;">
                    <canvas id="<?php echo esc_attr($canvas_id); ?>"></canvas>
                </div>

                <?php if ($uses_html_legend && $colors) : ?>
                <ul class="gf-survey-results-legend" aria-label="Leyenda del gráfico">
                    <?php foreach ($labels as $index => $label) : ?>
                    <li class="gf-survey-results-legend-item">
                        <span class="gf-survey-results-legend-swatch" style="background-color:<?php echo esc_attr($colors['background'][$index]); ?>;"></span>
                        <span class="gf-survey-results-legend-text"><?php echo esc_html($label); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

            
                <?php if (!empty($resultados_altres)) : ?>
                    <br />
                    <h4>Altres</h4>
                    <hr />
                    <div style="color: red">Revisar ya que a cada pregunta només s'han de mostrar els seus altres de respostes</div>
                    <ul class="gf-survey-results-altres">
                        <?php foreach ($resultados_altres as $altres) : ?>
                            <li>
                                <strong><?php echo esc_html($altres['field_value']); ?></strong> - <?php echo esc_html($altres['count']); ?> vots
                            </li>
                        <?php endforeach; ?>
                    </ul>  
                 <?php endif; ?>   
                

               

                
                    
               
             

                <p class="gf-survey-results-meta">
                Total de respostes:
                <strong><?php echo esc_html($total); ?></strong>
                </p>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {

            const ctx = document.getElementById('<?php echo esc_js($canvas_id); ?>');
            const chartFontFamily = '"Open Sans", sans-serif';

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
                    maintainAspectRatio: false,
                    indexAxis: <?php echo wp_json_encode($is_horizontal_bar ? 'y' : 'x'); ?>,
                    font: {
                        family: chartFontFamily,
                        size: 12
                    },
                    color: '#000000',
                    plugins: {
                        legend: {
                            display: <?php echo wp_json_encode($shows_legend && !$uses_html_legend); ?>,
                            position: <?php echo wp_json_encode($shows_legend ? $legend_position : 'bottom'); ?>,
                            align: 'start',
                            labels: {
                                color: '#000000',
                                font: {
                                    family: chartFontFamily,
                                    size: 12,
                                    weight: '600'
                                },
                                padding: 12,
                                usePointStyle: true,
                                boxWidth: 10
                            }
                        },
                        tooltip: {
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            titleFont: {
                                family: chartFontFamily,
                                size: 12
                            },
                            bodyFont: {
                                family: chartFontFamily,
                                size: 12  
                            },
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
                    <?php if (!$shows_legend) : ?>
                    scales: {
                        x: {
                            beginAtZero: <?php echo wp_json_encode($is_horizontal_bar); ?>,
                            ticks: {
                                color: '#000000',
                                font: {
                                    family: chartFontFamily,
                                    size: 12,
                                    weight: '600'
                                },
                                precision: 0
                            }
                        },
                        y: {
                            beginAtZero: <?php echo wp_json_encode(!$is_horizontal_bar); ?>,
                            ticks: {
                                color: '#000000',
                                font: {
                                    family: chartFontFamily,
                                    size: 12,
                                    weight: '600'
                                },
                                precision: 0
                            }
                        }
                    }
                    <?php endif; ?>
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
