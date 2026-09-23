<?php
/**
 * Shortcode [resultados_encuesta_chart form_id="2" tipo="doughnut"]
 *
 * Portado de astra-child (components/functions/gravity + components/shortcodes/gravity)
 * al theme PSOE Participatiu, con mejoras:
 * - ob_start() (el original llamaba a ob_get_clean() sin buffer y devolvía false).
 * - Chart.js se encola una sola vez en el footer en lugar de un <script src> por gráfico.
 * - Paleta corporativa PSOE en lugar de colores aleatorios.
 * - Bloque "Altres" con isset() y clases CSS propias.
 * - Leyenda con clase CSS en vez de <label> con estilos inline.
 *
 * Uso: [resultados_encuesta_chart form_id="2" tipo="doughnut"]
 * Tipos: bar, line, pie, doughnut, polarArea, radar. Atributo opcional: columnas="2".
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Lee una propiedad tanto si el dato llega como array como si llega como objeto.
if ( ! function_exists( 'resultados_encuesta_chart_get_field_property' ) ) {
function resultados_encuesta_chart_get_field_property( $item, $property, $default = null ) {
	if ( is_array( $item ) && array_key_exists( $property, $item ) ) {
		return $item[ $property ];
	}
	if ( is_object( $item ) && isset( $item->{$property} ) ) {
		return $item->{$property};
	}
	return $default;
}
}

// Busca un field concreto dentro de la definición completa del formulario.
if ( ! function_exists( 'resultados_encuesta_chart_get_field' ) ) {
function resultados_encuesta_chart_get_field( $form, $field_id ) {
	$fields = resultados_encuesta_chart_get_field_property( $form, 'fields', array() );
	foreach ( $fields as $field ) {
		if ( (string) resultados_encuesta_chart_get_field_property( $field, 'id' ) === (string) $field_id ) {
			return $field;
		}
	}
	return null;
}
}

// Busca un field concreto por su etiqueta de administración.
if ( ! function_exists( 'resultados_encuesta_chart_get_field_by_admin_label' ) ) {
function resultados_encuesta_chart_get_field_by_admin_label( $form, $admin_label ) {
	$admin_label = trim( (string) $admin_label );
	if ( $admin_label === '' ) {
		return null;
	}
	$fields = resultados_encuesta_chart_get_field_property( $form, 'fields', array() );
	foreach ( $fields as $field ) {
		$field_admin_label = trim( (string) resultados_encuesta_chart_get_field_property( $field, 'adminLabel', '' ) );
		if ( $field_admin_label === $admin_label ) {
			return $field;
		}
	}
	return null;
}
}

// Obtiene el subtipo real del campo Survey.
if ( ! function_exists( 'resultados_encuesta_chart_get_input_type' ) ) {
function resultados_encuesta_chart_get_input_type( $field ) {
	$type = resultados_encuesta_chart_get_field_property( $field, 'inputType' );
	if ( empty( $type ) ) {
		$type = resultados_encuesta_chart_get_field_property( $field, 'input_type' );
	}
	if ( empty( $type ) && is_object( $field ) && method_exists( $field, 'get_input_type' ) ) {
		$type = $field->get_input_type();
	}
	if ( empty( $type ) ) {
		$type = resultados_encuesta_chart_get_field_property( $field, 'type', '' );
	}
	return strtolower( (string) $type );
}
}

// Devuelve el tipo base del field.
if ( ! function_exists( 'resultados_encuesta_chart_get_field_type' ) ) {
function resultados_encuesta_chart_get_field_type( $field ) {
	return strtolower( (string) resultados_encuesta_chart_get_field_property( $field, 'type', '' ) );
}
}

// Filtra los fields y conserva solo los de tipo encuesta.
if ( ! function_exists( 'resultados_encuesta_chart_get_survey_fields' ) ) {
function resultados_encuesta_chart_get_survey_fields( $form ) {
	$survey_fields = array();
	$fields        = resultados_encuesta_chart_get_field_property( $form, 'fields', array() );
	foreach ( $fields as $field ) {
		if ( resultados_encuesta_chart_get_field_type( $field ) !== 'survey' ) {
			continue;
		}
		$survey_fields[] = $field;
	}
	return $survey_fields;
}
}

// Normaliza y valida el tipo de gráfico permitido antes de pasarlo a Chart.js.
if ( ! function_exists( 'resultados_encuesta_chart_get_chart_type' ) ) {
function resultados_encuesta_chart_get_chart_type( $chart_type ) {
	$allowed_types = array( 'bar', 'line', 'pie', 'doughnut', 'polarArea', 'radar' );
	$normalized    = strtolower( trim( (string) $chart_type ) );
	if ( $normalized === 'polararea' ) {
		return 'polarArea';
	}
	if ( ! in_array( $normalized, array_map( 'strtolower', $allowed_types ), true ) ) {
		return 'bar';
	}
	foreach ( $allowed_types as $allowed_type ) {
		if ( strtolower( $allowed_type ) === $normalized ) {
			return $allowed_type;
		}
	}
	return 'bar';
}
}

// Convierte el valor guardado en la entrada a la etiqueta visible de la opción.
if ( ! function_exists( 'resultados_encuesta_chart_get_choice_label' ) ) {
function resultados_encuesta_chart_get_choice_label( $field, $stored_value ) {
	$stored_value = trim( (string) $stored_value );
	if ( $stored_value === '' ) {
		return '';
	}
	$choices = resultados_encuesta_chart_get_field_property( $field, 'choices', array() );
	foreach ( $choices as $choice ) {
		$choice_text  = (string) resultados_encuesta_chart_get_field_property( $choice, 'text', '' );
		$choice_value = (string) resultados_encuesta_chart_get_field_property( $choice, 'value', '' );
		$choice_value = $choice_value !== '' ? (string) $choice_value : $choice_text;
		if ( (string) $choice_value === $stored_value || $choice_text === $stored_value ) {
			return $choice_text !== '' ? $choice_text : $stored_value;
		}
	}
	return $stored_value;
}
}

// Interpreta respuestas de tipo rank tanto en JSON como en texto separado por comas.
if ( ! function_exists( 'resultados_encuesta_chart_parse_rank_answers' ) ) {
function resultados_encuesta_chart_parse_rank_answers( $value, $field ) {
	$value = trim( (string) $value );
	if ( $value === '' ) {
		return array();
	}
	$decoded = json_decode( $value, true );
	$answers = is_array( $decoded ) ? $decoded : array_map( 'trim', explode( ',', $value ) );
	$answers = array_filter( $answers, static function ( $answer ) {
		return $answer !== '';
	} );
	return array_map( static function ( $answer ) use ( $field ) {
		return resultados_encuesta_chart_get_choice_label( $field, $answer );
	}, $answers );
}
}

// Extrae una o varias respuestas de una entrada según el subtipo del campo Survey.
if ( ! function_exists( 'resultados_encuesta_chart_get_entry_answers' ) ) {
function resultados_encuesta_chart_get_entry_answers( $entry, $field ) {
	$field_id    = (string) resultados_encuesta_chart_get_field_property( $field, 'id' );
	$input_type  = resultados_encuesta_chart_get_input_type( $field );
	$field_value = isset( $entry[ $field_id ] ) ? $entry[ $field_id ] : '';

	if ( $input_type === 'checkbox' ) {
		$answers = array();
		$inputs  = resultados_encuesta_chart_get_field_property( $field, 'inputs', array() );
		foreach ( $inputs as $input ) {
			$input_id    = (string) resultados_encuesta_chart_get_field_property( $input, 'id' );
			$input_label = (string) resultados_encuesta_chart_get_field_property( $input, 'label', '' );
			$input_value = isset( $entry[ $input_id ] ) ? trim( (string) $entry[ $input_id ] ) : '';
			if ( $input_value === '' ) {
				continue;
			}
			$answers[] = $input_label !== '' ? $input_label : resultados_encuesta_chart_get_choice_label( $field, $input_value );
		}
		if ( ! empty( $answers ) ) {
			return $answers;
		}
		return resultados_encuesta_chart_parse_rank_answers( $field_value, $field );
	}

	if ( $input_type === 'likert' ) {
		$answers = array();
		$inputs  = resultados_encuesta_chart_get_field_property( $field, 'inputs', array() );
		foreach ( $inputs as $input ) {
			$input_id    = (string) resultados_encuesta_chart_get_field_property( $input, 'id' );
			$input_value = isset( $entry[ $input_id ] ) ? trim( (string) $entry[ $input_id ] ) : '';
			if ( $input_value === '' ) {
				continue;
			}
			$answers[] = resultados_encuesta_chart_get_choice_label( $field, $input_value );
		}
		if ( ! empty( $answers ) ) {
			return $answers;
		}
	}

	if ( $input_type === 'rank' ) {
		return resultados_encuesta_chart_parse_rank_answers( $field_value, $field );
	}

	$field_value = trim( (string) $field_value );
	if ( $field_value === '' ) {
		return array();
	}
	return array( resultados_encuesta_chart_get_choice_label( $field, $field_value ) );
}
}

// Convierte una entry en un array asociativo por admin_label con etiquetas y valores legibles.
if ( ! function_exists( 'resultados_encuesta_chart_get_entry_labeled_values' ) ) {
function resultados_encuesta_chart_get_entry_labeled_values( $entry, $form ) {
	$entry_labeled_values = array();
	$entry_id             = isset( $entry['id'] ) ? (string) $entry['id'] : '';
	$fields               = resultados_encuesta_chart_get_field_property( $form, 'fields', array() );
	if ( $entry_id === '' ) {
		return $entry_labeled_values;
	}
	foreach ( $fields as $field ) {
		$field_id          = (string) resultados_encuesta_chart_get_field_property( $field, 'id' );
		$field_label       = trim( (string) resultados_encuesta_chart_get_field_property( $field, 'label', 'Campo ' . $field_id ) );
		$field_admin_label = trim( (string) resultados_encuesta_chart_get_field_property( $field, 'adminLabel', '' ) );
		$entry_key         = $field_admin_label !== '' ? $field_admin_label : $field_id;
		if ( $entry_key === '' ) {
			continue;
		}
		if ( resultados_encuesta_chart_get_field_type( $field ) === 'survey' ) {
			$field_values = resultados_encuesta_chart_get_entry_answers( $entry, $field );
		} else {
			$field_values = array();
			$field_value  = isset( $entry[ $field_id ] ) ? trim( (string) $entry[ $field_id ] ) : '';
			if ( $field_value !== '' ) {
				$field_values[] = $field_value;
			}
		}
		if ( empty( $field_values ) ) {
			continue;
		}
		$entry_labeled_values[ $entry_key ] = array(
			'field_id'    => $field_id,
			'label'       => $field_label,
			'admin_label' => $field_admin_label,
			'value'       => count( $field_values ) === 1 ? reset( $field_values ) : $field_values,
		);
	}
	return $entry_labeled_values;
}
}

// Paleta corporativa PSOE (cíclica) para los segmentos del gráfico.
if ( ! function_exists( 'resultados_encuesta_chart_get_bar_colors' ) ) {
function resultados_encuesta_chart_get_bar_colors( $total_items ) {
	$palette = array(
		array( 227, 6, 19 ),   // brand
		array( 122, 3, 11 ),   // darker
		array( 255, 77, 90 ),  // light
		array( 28, 18, 20 ),   // ink
		array( 232, 160, 166 ),// rosa pálido
		array( 91, 75, 78 ),   // ink-soft
		array( 176, 5, 15 ),   // brand-dark
		array( 200, 120, 128 ),
	);
	$count             = count( $palette );
	$background_colors = array();
	$border_colors     = array();
	for ( $index = 0; $index < $total_items; $index++ ) {
		list( $red, $green, $blue ) = $palette[ $index % $count ];
		$background_colors[] = 'rgba(' . $red . ', ' . $green . ', ' . $blue . ', 0.75)';
		$border_colors[]     = 'rgb(' . $red . ', ' . $green . ', ' . $blue . ')';
	}
	return array(
		'background' => $background_colors,
		'border'     => $border_colors,
	);
}
}

// Normaliza el número de columnas para la rejilla flexible de gráficas.
if ( ! function_exists( 'resultados_encuesta_chart_get_columns' ) ) {
function resultados_encuesta_chart_get_columns( $columns ) {
	$columns = absint( $columns );
	if ( $columns < 1 ) {
		return 1;
	}
	return min( $columns, 6 );
}
}

// Encola Chart.js una sola vez (en el footer).
function psoe_resultados_encuesta_enqueue_chart() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;
	wp_enqueue_script(
		'chart-js',
		'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
		array(),
		'4.4.1',
		true
	);
}

// Genera un gráfico por cada campo Survey del formulario usando las entradas activas.
add_shortcode( 'resultados_encuesta_chart', 'resultados_encuesta_chart_shortcode' );
add_shortcode( 'resultados_encuesta_doughnut', 'resultados_encuesta_doughnut_shortcode' );

function resultados_encuesta_doughnut_shortcode( $atts ) {
	$atts['tipo'] = 'doughnut';
	return resultados_encuesta_chart_shortcode( $atts );
}

function resultados_encuesta_chart_shortcode( $atts ) {
	if ( ! class_exists( 'GFAPI' ) ) {
		return esc_html__( 'Gravity Forms no está activo.', 'psoe-participatiu' );
	}

	$atts = shortcode_atts( array(
		'form_id'  => '',
		'tipo'     => 'bar',
		'columnas' => 1,
	), $atts );

	$form_id    = absint( $atts['form_id'] );
	$chart_type = resultados_encuesta_chart_get_chart_type( $atts['tipo'] );
	$columns    = resultados_encuesta_chart_get_columns( $atts['columnas'] );

	if ( empty( $form_id ) ) {
		return esc_html__( 'Debes indicar el formulario. Ejemplo: [resultados_encuesta_chart form_id="1"]', 'psoe-participatiu' );
	}

	$form = GFAPI::get_form( $form_id );
	if ( is_wp_error( $form ) || empty( $form ) ) {
		return esc_html__( 'No se ha podido cargar el formulario.', 'psoe-participatiu' );
	}

	$fields = resultados_encuesta_chart_get_survey_fields( $form );
	if ( empty( $fields ) ) {
		return esc_html__( 'Este formulario no tiene campos de encuesta.', 'psoe-participatiu' );
	}

	$entries = GFAPI::get_entries( $form_id, array( 'status' => 'active' ), null, array(
		'offset'    => 0,
		'page_size' => 10000,
	) );
	if ( is_wp_error( $entries ) || empty( $entries ) ) {
		return esc_html__( 'Todavía no hay resultados.', 'psoe-participatiu' );
	}

	psoe_resultados_encuesta_enqueue_chart();

	$column_gap = 24;
	$wrap_style = '--gf-survey-columns:' . $columns . ';--gf-survey-gap:' . $column_gap . 'px;';
	$item_width = 'calc((100% - (var(--gf-survey-columns) - 1) * var(--gf-survey-gap)) / var(--gf-survey-columns))';
	$item_style = 'flex:1 1 ' . $item_width . ';max-width:' . $item_width . ';min-width:280px;';

	ob_start();

	echo '<div class="gf-survey-results-wrap" style="' . esc_attr( $wrap_style ) . '">';

	$question_number = 0;
	foreach ( $fields as $field ) {
		$resultados        = array();
		$resultados_altres = array();

		foreach ( $entries as $entry ) {
			$entry_con_labels = resultados_encuesta_chart_get_entry_labeled_values( $entry, $form );
			foreach ( $entry_con_labels as $entry_key => $entry_data ) {
				if ( strpos( $entry_key, 'altres' ) !== false ) {
					$field_values = is_array( $entry_data['value'] ) ? $entry_data['value'] : array( $entry_data['value'] );
					foreach ( $field_values as $field_value ) {
						if ( $field_value !== '' ) {
							if ( ! isset( $resultados_altres[ $entry_key ][ $field_value ] ) ) {
								$resultados_altres[ $entry_key ][ $field_value ] = array(
									'entry_key'   => $entry_key,
									'field_value' => $field_value,
									'count'       => 0,
								);
							}
							$resultados_altres[ $entry_key ][ $field_value ]['count']++;
						}
					}
				}
			}

			$valores = resultados_encuesta_chart_get_entry_answers( $entry, $field );
			foreach ( $valores as $valor ) {
				if ( $valor === '' ) {
					continue;
				}
				if ( ! isset( $resultados[ $valor ] ) ) {
					$resultados[ $valor ] = 0;
				}
				$resultados[ $valor ]++;
			}
		}

		if ( empty( $resultados ) ) {
			continue;
		}

		$question_number++;

		$labels            = array_keys( $resultados );
		$data              = array_values( $resultados );
		$total             = array_sum( $data );
		$field_id          = resultados_encuesta_chart_get_field_property( $field, 'id' );
		$title             = resultados_encuesta_chart_get_field_property( $field, 'label', 'Resultados pregunta ' . $field_id );
		$uses_segment_colors = in_array( $chart_type, array( 'bar', 'pie', 'doughnut', 'polarArea' ), true );
		$shows_legend        = in_array( $chart_type, array( 'pie', 'doughnut', 'polarArea' ), true );
		$is_horizontal_bar   = $chart_type === 'bar';
		$uses_html_legend    = $chart_type === 'doughnut';
		$legend_position     = $chart_type === 'doughnut' ? 'bottom' : 'right';
		$colors              = $uses_segment_colors ? resultados_encuesta_chart_get_bar_colors( count( $data ) ) : null;
		$canvas_height       = 280;

		if ( $is_horizontal_bar ) {
			$canvas_height = max( 280, count( $labels ) * 52 );
		} elseif ( $shows_legend && ! $uses_html_legend ) {
			$canvas_height = max( 280, count( $labels ) * 30 );
		}

		$canvas_id = 'chart_encuesta_' . uniqid();
		?>
		<div class="gf-survey-results-item" style="<?php echo esc_attr( $item_style ); ?>">
			<div class="gf-survey-results-card">
				<h3 class="gf-survey-results-title"><?php echo esc_html( $title ); ?></h3>

				<div class="gf-survey-results-canvas-wrap" style="height:<?php echo esc_attr( $canvas_height ); ?>px;">
					<canvas id="<?php echo esc_attr( $canvas_id ); ?>"></canvas>
				</div>

				<?php if ( $uses_html_legend && $colors ) : ?>
				<ul class="gf-survey-results-legend" aria-label="<?php esc_attr_e( 'Leyenda del gráfico', 'psoe-participatiu' ); ?>">
					<?php foreach ( $labels as $index => $label ) : ?>
					<li class="gf-survey-results-legend-item">
						<span class="gf-survey-results-legend-swatch" style="background-color:<?php echo esc_attr( $colors['background'][ $index ] ); ?>;"></span>
						<span class="gf-survey-results-legend-text"><?php echo esc_html( $label ); ?></span>
						<span class="gf-survey-results-legend-stats">
							<?php
							$votes      = isset( $data[ $index ] ) ? (int) $data[ $index ] : 0;
							$percentage = $total > 0 ? ( $votes / $total ) * 100 : 0;
							echo esc_html(
								sprintf(
									_n( '%s voto', '%s votos', $votes, 'psoe-participatiu' ),
									number_format_i18n( $votes )
								) . ' (' . number_format_i18n( $percentage, 1 ) . '%)'
							);
							?>
						</span>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>

				<?php $index_altres = 'bloc_' . $question_number . '_altres'; ?>
				<?php if ( ! empty( $resultados_altres[ $index_altres ] ) ) : ?>
				<div class="gf-survey-results-other">
					<h4 class="gf-survey-results-other-title"><?php esc_html_e( 'Altres', 'psoe-participatiu' ); ?></h4>
					<ul class="gf-survey-results-other-list">
						<?php foreach ( $resultados_altres[ $index_altres ] as $key_altres => $value_altres ) : ?>
						<li class="gf-survey-results-other-item">
							<span class="gf-survey-results-other-text"><?php echo esc_html( $key_altres ); ?></span>
							<span class="gf-survey-results-other-count"><?php echo esc_html( $value_altres['count'] ); ?></span>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php endif; ?>

				<p class="gf-survey-results-meta">
					<?php esc_html_e( 'Total de respostes:', 'psoe-participatiu' ); ?>
					<strong><?php echo esc_html( $total ); ?></strong>
				</p>
			</div>
		</div>

		<script>
		document.addEventListener('DOMContentLoaded', function () {
			const ctx = document.getElementById('<?php echo esc_js( $canvas_id ); ?>');
			if (!ctx || typeof Chart === 'undefined') return;

			new Chart(ctx, {
				type: <?php echo wp_json_encode( $chart_type ); ?>,
				data: {
					labels: <?php echo wp_json_encode( $labels ); ?>,
					datasets: [{
						label: 'Votos',
						data: <?php echo wp_json_encode( $data ); ?>,
						backgroundColor: <?php echo wp_json_encode( $colors ? $colors['background'] : '#E30613' ); ?>,
						borderColor: <?php echo wp_json_encode( $colors ? $colors['border'] : '#E30613' ); ?>,
						borderWidth: 1
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					indexAxis: <?php echo wp_json_encode( $is_horizontal_bar ? 'y' : 'x' ); ?>,
					font: {
						family: '"Inter", sans-serif',
						size: 12
					},
					color: '#1C1214',
					plugins: {
						legend: {
							display: <?php echo wp_json_encode( $shows_legend && ! $uses_html_legend ); ?>,
							position: <?php echo wp_json_encode( $shows_legend ? $legend_position : 'bottom' ); ?>,
							align: 'start',
							labels: {
								color: '#1C1214',
								font: {
									family: '"Inter", sans-serif',
									size: 12,
									weight: '600'
								},
								padding: 12,
								usePointStyle: true,
								boxWidth: 10
							}
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
					<?php if ( ! $shows_legend ) : ?>
					scales: {
						x: {
							beginAtZero: <?php echo wp_json_encode( $is_horizontal_bar ); ?>,
							ticks: {
								color: '#1C1214',
								font: { family: '"Inter", sans-serif', size: 12, weight: '600' },
								precision: 0
							}
						},
						y: {
							beginAtZero: <?php echo wp_json_encode( ! $is_horizontal_bar ); ?>,
							ticks: {
								color: '#1C1214',
								font: { family: '"Inter", sans-serif', size: 12, weight: '600' },
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

	return ob_get_clean();
}

/* -------------------------------------------------------------------------
 * Ajustes de la plantilla "Enquesta (PSOE)": meta box en el editor de páginas
 * ------------------------------------------------------------------------- */
add_action( 'add_meta_boxes', function () {
	add_meta_box(
		'psoe_enquesta_settings',
		__( 'Enquesta (plantilla)', 'psoe-participatiu' ),
		'psoe_enquesta_settings_box',
		'page',
		'side',
		'default'
	);
} );

function psoe_enquesta_settings_box( $post ) {
	wp_nonce_field( 'psoe_enquesta_save', 'psoe_enquesta_nonce' );
	$form_id  = (int) get_post_meta( $post->ID, '_psoe_enquesta_form_id', true );
	$tipo     = get_post_meta( $post->ID, '_psoe_enquesta_tipo', true ) ?: 'doughnut';
	$columnas = (int) get_post_meta( $post->ID, '_psoe_enquesta_columnas', true );
	$columnas = $columnas >= 1 ? min( $columnas, 6 ) : 1;
	$tipos    = array( 'doughnut', 'bar', 'pie', 'polarArea', 'line', 'radar' );
	?>
	<p class="description"><?php esc_html_e( 'Solo se usa con la plantilla “Resultats (PSOE)”.', 'psoe-participatiu' ); ?></p>
	<p>
		<label for="psoe_enquesta_form_id"><strong><?php esc_html_e( 'Form de resultados (ID)', 'psoe-participatiu' ); ?></strong></label><br>
		<input type="number" id="psoe_enquesta_form_id" name="psoe_enquesta_form_id" value="<?php echo esc_attr( $form_id > 0 ? $form_id : 2 ); ?>" min="1" style="width:100%">
	</p>
	<p>
		<label for="psoe_enquesta_tipo"><strong><?php esc_html_e( 'Tipo de gráfico', 'psoe-participatiu' ); ?></strong></label><br>
		<select id="psoe_enquesta_tipo" name="psoe_enquesta_tipo" style="width:100%">
			<?php foreach ( $tipos as $t ) : ?>
				<option value="<?php echo esc_attr( $t ); ?>" <?php selected( $tipo, $t ); ?>><?php echo esc_html( $t ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="psoe_enquesta_columnas"><strong><?php esc_html_e( 'Columnas', 'psoe-participatiu' ); ?></strong></label><br>
		<input type="number" id="psoe_enquesta_columnas" name="psoe_enquesta_columnas" value="<?php echo esc_attr( $columnas ); ?>" min="1" max="6" style="width:100%">
	</p>
	<?php
}

add_action( 'save_post_page', function ( $post_id ) {
	if ( ! isset( $_POST['psoe_enquesta_nonce'] ) || ! wp_verify_nonce( $_POST['psoe_enquesta_nonce'], 'psoe_enquesta_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, '_psoe_enquesta_form_id', max( 1, (int) ( $_POST['psoe_enquesta_form_id'] ?? 2 ) ) );
	$tipo = sanitize_key( $_POST['psoe_enquesta_tipo'] ?? 'doughnut' );
	update_post_meta( $post_id, '_psoe_enquesta_tipo', in_array( $tipo, array( 'doughnut', 'bar', 'pie', 'polarArea', 'line', 'radar' ), true ) ? $tipo : 'doughnut' );
	update_post_meta( $post_id, '_psoe_enquesta_columnas', min( 6, max( 1, (int) ( $_POST['psoe_enquesta_columnas'] ?? 1 ) ) ) );
} );

/* -------------------------------------------------------------------------
 * Cabecera BLOC en el servidor: transforma <h1 class="bloc">BLOC N…</h1>
 * del contenido en la cabecera elegante, sin depender del JS (caché, etc.).
 * El JS la ignora después gracias a la clase psoe-bloc-head.
 * ------------------------------------------------------------------------- */
function psoe_enquesta_style_bloc_h1( $content ) {
	if ( is_admin() || ! is_page() ) {
		return $content;
	}
	if ( stripos( $content, 'bloc' ) === false ) {
		return $content;
	}
	return preg_replace_callback(
		'/<h1([^>]*\bclass\s*=\s*(?:"[^"]*\bbloc\b[^"]*"|\'[^\']*\bbloc\b[^\']*\')[^>]*)>(?:\s|<[^>]+>)*?BLOC\s*(\d+)\s*:?\s*(.*?)\s*<\/h1>/is',
		function ( $m ) {
			$num  = $m[2];
			$rest = trim( wp_strip_all_tags( $m[3] ) );
			$tagline = '';
			if ( preg_match( '/\(([^()]*)\)\s*$/', $rest, $pm ) ) {
				$tagline = trim( $pm[1] );
				$rest    = rtrim( trim( substr( $rest, 0, -strlen( $pm[0] ) ) ), ":–—- \t" );
			}
			$title = $rest !== '' ? $rest : 'Bloc ' . $num;
			return '<h1 class="bloc psoe-bloc-head"><span class="psoe-bloc-num">BLOC '
				. esc_html( $num )
				. '</span><span class="psoe-bloc-titles"><strong>'
				. esc_html( $title )
				. '</strong>'
				. ( $tagline !== '' ? '<em>' . esc_html( $tagline ) . '</em>' : '' )
				. '</span></h1>';
		},
		$content
	);
}
add_filter( 'the_content', 'psoe_enquesta_style_bloc_h1', 20 );
