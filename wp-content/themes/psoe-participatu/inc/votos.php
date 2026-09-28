<?php
// Votaciones AJAX (1 voto por navegador vía cookie + límite por IP hasheada)
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

/**
 * IP del cliente, solo para derivar un límite por origen.
 * No se usa X-Forwarded-For: es spoofable desde el cliente.
 * En el límite nunca se guarda la IP en claro, solo su hash con las salts
 * del sitio (RGPD), por lo que no es reversible.
 */
function psoe_client_hash() {
  $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : '';
  return substr( wp_hash( $ip ), 0, 40 );
}

/**
 * Incremento atómico de los apoyos.
 * El patrón leer → +1 → guardar (read/modify/write) perdía votos bajo
 * peticiones simultáneas; con UPDATE ... + 1 la operación es indivisible.
 */
function psoe_increment_votos( $post_id ) {
  global $wpdb;

  $updated = $wpdb->query( $wpdb->prepare(
    "UPDATE {$wpdb->postmeta}
     SET meta_value = CAST(meta_value AS UNSIGNED) + 1
     WHERE post_id = %d AND meta_key = '_psoe_votos'",
    $post_id
  ) );

  if ( ! $updated ) {
    // La meta aún no existe (propuesta recién publicada fuera del hook de init).
    if ( ! metadata_exists( 'post', $post_id, '_psoe_votos' ) ) {
      add_post_meta( $post_id, '_psoe_votos', 1 );
      return 1;
    }
    return psoe_get_votos( $post_id );
  }

  return psoe_get_votos( $post_id );
}

add_action( 'wp_ajax_psoe_vote', 'psoe_handle_vote' );
add_action( 'wp_ajax_nopriv_psoe_vote', 'psoe_handle_vote' );
function psoe_handle_vote() {
  check_ajax_referer( 'psoe_vote', 'nonce' );

  $post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
  if ( ! $post_id || get_post_type( $post_id ) !== 'propuesta' ) {
    wp_send_json_error( [ 'message' => 'ID inválido' ], 400 );
  }

  if ( psoe_has_voted( $post_id ) ) {
    wp_send_json_error( [ 'message' => 'Ya has votado', 'votos' => psoe_get_votos( $post_id ) ], 409 );
  }

  // La cookie la controla el cliente (se puede borrar), así que es solo el
  // atajo rápido. El límite real es un transient por (post, IP hasheada).
  $lock = 'psoe_v_' . $post_id . '_' . psoe_client_hash();
  if ( get_transient( $lock ) ) {
    wp_send_json_error( [ 'message' => 'Ya has votado', 'votos' => psoe_get_votos( $post_id ) ], 409 );
  }

  $votos = psoe_increment_votos( $post_id );

  set_transient( $lock, 1, DAY_IN_SECONDS );

  // Cookie 1 año — HttpOnly + SameSite para que no la manipule
  // un script de terceros ni viaje en peticiones cross-site.
  setcookie( 'psoe_voted_' . $post_id, '1', [
    'expires'  => time() + YEAR_IN_SECONDS,
    'path'     => COOKIEPATH ? COOKIEPATH : '/',
    'domain'   => COOKIE_DOMAIN,
    'secure'   => is_ssl(),
    'httponly' => true,
    'samesite' => 'Lax',
  ] );

  wp_send_json_success( [ 'votos' => $votos, 'formateado' => psoe_format_votos( $votos ) ] );
}
