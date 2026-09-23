<?php
// Votaciones AJAX (1 voto por navegador vía cookie + IP hash)
if ( ! defined( 'ABSPATH' ) ) exit;

function psoe_get_votos( $post_id ) {
  return (int) get_post_meta( $post_id, '_psoe_votos', true );
}

function psoe_format_votos( $n ) {
  if ( $n >= 1000 ) {
    $k = $n / 1000;
    return ( $k >= 10 ? round( $k, 0 ) : round( $k, 1 ) ) . 'k';
  }
  return (string) $n;
}

function psoe_has_voted( $post_id ) {
  if ( isset( $_COOKIE[ 'psoe_voted_' . $post_id ] ) ) return true;
  return false;
}

add_action( 'wp_ajax_psoe_vote', 'psoe_handle_vote' );
add_action( 'wp_ajax_nopriv_psoe_vote', 'psoe_handle_vote' );
function psoe_handle_vote() {
  check_ajax_referer( 'psoe_vote', 'nonce' );
  $post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
  if ( ! $post_id || get_post_type( $post_id ) !== 'propuesta' ) wp_send_json_error( [ 'message' => 'ID inválido' ], 400 );

  if ( psoe_has_voted( $post_id ) ) {
    wp_send_json_error( [ 'message' => 'Ya has votado', 'votos' => psoe_get_votos( $post_id ) ], 409 );
  }

  $votos = psoe_get_votos( $post_id ) + 1;
  update_post_meta( $post_id, '_psoe_votos', $votos );
  // Cookie 1 año
  setcookie( 'psoe_voted_' . $post_id, '1', time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
  wp_send_json_success( [ 'votos' => $votos, 'formateado' => psoe_format_votos( $votos ) ] );
}
