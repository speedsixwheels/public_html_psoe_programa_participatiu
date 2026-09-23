<?php
/**
 * Integración Gravity Forms — estilos PSOE (Image 1 + Image 2).
 *
 * - Convierte el radio "Categoría o Área Temática" en tarjetas con icono.
 * - Botón de envío rojo a ancho completo (con flecha en formularios de propuesta).
 * - "(Obligatorio)" bajo las preguntas required (estilo Image 2).
 * - Confirmaciones envueltas en tarjeta.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// Traduce la leyenda de requeridos al estilo de la captura.
add_filter( 'gform_required_legend', function () {
  return '';
} );

// Envuelve la confirmación en una tarjeta PSOE.
add_filter( 'gform_confirmation', function ( $confirmation, $form ) {
  if ( is_string( $confirmation ) && strpos( $confirmation, 'psoe-confirm' ) === false ) {
    $confirmation = '<div class="psoe-confirm">' . $confirmation . '</div>';
  }
  return $confirmation;
}, 10, 2 );

// Botón de envío rojo full-width. Mantiene el texto configurado en el form.
add_filter( 'gform_submit_button', function ( $button, $form ) {
  $text = 'Enviar';
  if ( preg_match( '/value=(["\'])(.*?)\1/', $button, $m ) ) {
    $text = $m[2];
  } elseif ( preg_match( '/>(.*?)<\/input>/s', $button, $m ) ) {
    $text = trim( wp_strip_all_tags( $m[1] ) ) ?: $text;
  }
  $is_propuesta = stripos( wp_json_encode( $form ), 'categor' ) !== false
    || stripos( $form['title'], 'propuesta' ) !== false;
  $arrow = $is_propuesta
    ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12h13M13 6l6 6-6 6"/></svg>'
    : '';
  return sprintf(
    '<button type="submit" id="gform_submit_button_%d" class="psoe-submit%s"><span>%s</span>%s</button>',
    (int) $form['id'],
    $is_propuesta ? ' psoe-submit-propuesta' : '',
    esc_html( $text ),
    $arrow
  );
}, 10, 2 );

/**
 * Iconos SVG por categoría (trazo granate como la captura).
 */
function psoe_cat_icon( $key ) {
  $s = 'width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#A63A4A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';
  switch ( $key ) {
    case 'sanidad':
      return "<svg $s><rect x=\"3\" y=\"7\" width=\"18\" height=\"13\" rx=\"2\"/><path d=\"M12 10.5v6M9 13.5h6M9 7V5.5A1.5 1.5 0 0110.5 4h3A1.5 1.5 0 0115 5.5V7\"/></svg>";
    case 'educacion':
      return "<svg $s><path d=\"M12 4L2 9l10 5 10-5-10-5z\"/><path d=\"M6 11.5V16c0 1.5 2.7 3 6 3s6-1.5 6-3v-4.5\"/><path d=\"M22 9v5\"/></svg>";
    case 'economia':
      return "<svg $s><path d=\"M18.5 5.5A8 8 0 105 13.5\"/><path d=\"M4 9h9M4 13h9M9.5 7.5A3.5 3.5 0 009 15.5\"/></svg>";
    case 'ecologia':
      return "<svg $s><path d=\"M5 19C5 9 13 5 20 5c0 8-4 14-13 14\"/><path d=\"M5 19c3-5 7-8 11-10\"/></svg>";
    case 'igualdad':
      return "<svg $s><circle cx=\"8.5\" cy=\"8\" r=\"3\"/><circle cx=\"15.5\" cy=\"8\" r=\"3\"/><path d=\"M3.5 19c.6-3 2.6-4.5 5-4.5s4.4 1.5 5 4.5M10.5 19c.6-3 2.6-4.5 5-4.5s4.4 1.5 5 4.5\"/></svg>";
    default:
      return "<svg $s><circle cx=\"5\" cy=\"12\" r=\"1.4\" fill=\"#A63A4A\"/><circle cx=\"12\" cy=\"12\" r=\"1.4\" fill=\"#A63A4A\"/><circle cx=\"19\" cy=\"12\" r=\"1.4\" fill=\"#A63A4A\"/></svg>";
  }
}

function psoe_cat_key( $label ) {
  $l = mb_strtolower( $label, 'UTF-8' );
  if ( strpos( $l, 'sanidad' ) !== false || strpos( $l, 'salud' ) !== false ) return 'sanidad';
  if ( strpos( $l, 'educaci' ) !== false || strpos( $l, 'forma' ) !== false ) return 'educacion';
  if ( strpos( $l, 'econom' ) !== false || strpos( $l, 'empleo' ) !== false || strpos( $l, 'euro' ) !== false ) return 'economia';
  if ( strpos( $l, 'transici' ) !== false || strpos( $l, 'ecolog' ) !== false || strpos( $l, 'medio' ) !== false || strpos( $l, 'verde' ) !== false ) return 'ecologia';
  if ( strpos( $l, 'igualdad' ) !== false || strpos( $l, 'femin' ) !== false || strpos( $l, 'mujer' ) !== false ) return 'igualdad';
  return 'otras';
}

// Inyecta iconos en las opciones del radio de categorías.
add_filter( 'gform_field_content', function ( $content, $field, $value, $entry_id, $form_id ) {
  if ( ! is_object( $field ) || $field->type !== 'radio' ) return $content;
  if ( stripos( (string) $field->label, 'categor' ) === false && stripos( (string) $field->label, 'rea tem' ) === false ) return $content;

  $content = preg_replace_callback(
    '/<label([^>]*)>(.*?)<\/label>/s',
    function ( $m ) {
      $inner = $m[2];
      if ( strpos( $inner, 'psoe-cat-icon' ) !== false ) return $m[0];
      $key = psoe_cat_key( wp_strip_all_tags( $inner ) );
      return '<label' . $m[1] . '><span class="psoe-cat-icon">' . psoe_cat_icon( $key ) . '</span><span>' . $inner . '</span></label>';
    },
    $content
  );
  return $content;
}, 10, 5 );
