<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function psoe_time_ago( $post_id = null ) {
  $t = get_the_time( 'U', $post_id );
  $diff = human_time_diff( $t, current_time( 'timestamp' ) );
  // human_time_diff devuelve "2 horas" — lo acortamos al estilo "Hace 2h"
  return sprintf( __( 'Hace %s', 'psoe-participatiu' ), $diff );
}

function psoe_categoria_chip( $post_id = null ) {
  $post_id = $post_id ?: get_the_ID();
  $terms = get_the_terms( $post_id, 'categoria_propuesta' );
  if ( ! $terms || is_wp_error( $terms ) ) {
    $terms = get_the_category( $post_id );
    $name = $terms ? $terms[0]->name : __( 'General', 'psoe-participatiu' );
    $bg = '#F1F5F9'; $fg = '#475569';
  } else {
    $name = $terms[0]->name;
    $bg = get_term_meta( $terms[0]->term_id, 'color', true ) ?: '#F1F5F9';
    $fg = '#1E293B';
  }
  printf(
    '<span class="chip" style="background:%s;color:%s">%s</span>',
    esc_attr( $bg ), esc_attr( $fg ), esc_html( $name )
  );
}

function psoe_avatar_or_initial( $name, $size = 28 ) {
  $author_id = get_the_author_meta( 'ID' );
  $url = get_avatar_url( $author_id, [ 'size' => 96 ] );
  if ( $url ) {
    printf( '<img src="%s" alt="%s" width="%d" height="%d" class="rounded-full object-cover" style="width:%dpx;height:%dpx">', esc_url( $url ), esc_attr( $name ), $size, $size, $size, $size );
  } else {
    printf( '<span class="rounded-full bg-brand-soft text-brand font-bold inline-flex items-center justify-center" style="width:%dpx;height:%dpx">%s</span>', $size, $size, esc_html( mb_substr( $name, 0, 1 ) ) );
  }
}
