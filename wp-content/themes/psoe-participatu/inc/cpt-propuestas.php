<?php
// CPT Propuestas + taxonomías
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function () {
  register_post_type( 'propuesta', [
    'label' => __( 'Propuestas', 'psoe-participatiu' ),
    'labels' => [
      'name' => __( 'Propuestas', 'psoe-participatiu' ),
      'singular_name' => __( 'Propuesta', 'psoe-participatiu' ),
      'add_new_item' => __( 'Añadir propuesta', 'psoe-participatiu' ),
      'edit_item' => __( 'Editar propuesta', 'psoe-participatiu' ),
    ],
    'public' => true,
    'show_in_rest' => true,
    'has_archive' => true,
    'rewrite' => [ 'slug' => 'propuestas' ],
    'menu_icon' => 'dashicons-lightbulb',
    'supports' => [ 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'comments' ],
  ] );

  register_taxonomy( 'categoria_propuesta', 'propuesta', [
    'label' => __( 'Categorías', 'psoe-participatiu' ),
    'public' => true,
    'show_in_rest' => true,
    'hierarchical' => true,
    'rewrite' => [ 'slug' => 'categoria' ],
  ] );

  register_taxonomy( 'area_tematica', [ 'propuesta', 'post' ], [
    'label' => __( 'Áreas temáticas', 'psoe-participatiu' ),
    'public' => true,
    'show_in_rest' => true,
    'hierarchical' => true,
    'rewrite' => [ 'slug' => 'area' ],
  ] );
} );

// Meta votos
add_action( 'add_meta_boxes', function () {
  add_meta_box( 'psoe_votos', __( 'Votos / metadatos', 'psoe-participatiu' ), function ( $post ) {
    $votos = (int) get_post_meta( $post->ID, '_psoe_votos', true );
    wp_nonce_field( 'psoe_meta', 'psoe_meta_nonce' );
    echo '<label for="psoe_votos_field"><strong>' . esc_html__( 'Nº de apoyos', 'psoe-participatiu' ) . '</strong></label><br>';
    echo '<input type="number" id="psoe_votos_field" name="psoe_votos_field" value="' . esc_attr( $votos ) . '" min="0" style="width:120px;margin-top:6px">';
  }, 'propuesta', 'side' );
} );

add_action( 'save_post_propuesta', function ( $post_id ) {
  if ( ! isset( $_POST['psoe_meta_nonce'] ) || ! wp_verify_nonce( $_POST['psoe_meta_nonce'], 'psoe_meta' ) ) return;
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
  if ( ! current_user_can( 'edit_post', $post_id ) ) return;
  if ( isset( $_POST['psoe_votos_field'] ) ) {
    update_post_meta( $post_id, '_psoe_votos', max( 0, (int) $_POST['psoe_votos_field'] ) );
  }
} );

// Al publicar, inicializar votos a 0
add_action( 'wp_insert_post', function ( $post_id, $post ) {
  if ( $post->post_type === 'propuesta' && $post->post_status === 'publish' && get_post_meta( $post_id, '_psoe_votos', true ) === '' ) {
    update_post_meta( $post_id, '_psoe_votos', 0 );
  }
}, 10, 2 );

// Seed de taxonomías si están vacías
add_action( 'after_switch_theme', function () {
  $cats = [ 'Digitalización' => '#DBEAFE', 'Ecología' => '#DCFCE7', 'Vivienda' => '#EDE9FE', 'Sanidad' => '#FCE7F3', 'Educación' => '#FEF3C7', 'Empleo' => '#FFEDD5' ];
  foreach ( $cats as $name => $color ) {
    if ( ! term_exists( $name, 'categoria_propuesta' ) ) {
      $t = wp_insert_term( $name, 'categoria_propuesta' );
      if ( ! is_wp_error( $t ) ) update_term_meta( $t['term_id'], 'color', $color );
    }
  }
  $areas = [ 'Sanidad Pública', 'Educación de Calidad', 'Transición Ecológica', 'Empleo Digno' ];
  foreach ( $areas as $a ) {
    if ( ! term_exists( $a, 'area_tematica' ) ) wp_insert_term( $a, 'area_tematica' );
  }
} );
