<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'customize_register', function ( $wp_customize ) {
  $wp_customize->add_section( 'psoe_home', [
    'title' => __( 'Portada PSOE 2027', 'psoe-participatiu' ),
    'priority' => 30,
  ] );

  $fields = [
    'psoe_hero_badge' => [ 'label' => 'Badge hero', 'default' => 'Programa electoral colaborativo' ],
    'psoe_hero_title' => [ 'label' => 'Título hero', 'default' => 'Tu voz, nuestro compromiso. El futuro de España.' ],
    'psoe_hero_sub'   => [ 'label' => 'Subtítulo hero', 'default' => 'Construyamos juntos el Programa Electoral 2027. Participa con tus ideas, debate con otros ciudadanos y vota las propuestas para una España mejor.' ],
    'psoe_stat1_n' => [ 'label' => 'Stat 1 número', 'default' => '1.250' ],
    'psoe_stat1_l' => [ 'label' => 'Stat 1 etiqueta', 'default' => 'Ideas recibidas' ],
    'psoe_stat2_n' => [ 'label' => 'Stat 2 número', 'default' => '15.4K' ],
    'psoe_stat2_l' => [ 'label' => 'Stat 2 etiqueta', 'default' => 'Apoyos ciudadanos' ],
    'psoe_stat3_n' => [ 'label' => 'Stat 3 número', 'default' => '320' ],
    'psoe_stat3_l' => [ 'label' => 'Stat 3 etiqueta', 'default' => 'Conversaciones activas' ],
    'psoe_stat4_n' => [ 'label' => 'Stat 4 número', 'default' => '12' ],
    'psoe_stat4_l' => [ 'label' => 'Stat 4 etiqueta', 'default' => 'Áreas temáticas' ],
    'psoe_news_title' => [ 'label' => 'Newsletter título', 'default' => 'Mantente informado' ],
    'psoe_news_sub'   => [ 'label' => 'Newsletter subtítulo', 'default' => 'Recibe actualizaciones sobre el proceso de elaboración del programa y las propuestas más votadas directamente en tu correo.' ],
  ];

  foreach ( $fields as $id => $f ) {
    $wp_customize->add_setting( $id, [ 'default' => $f['default'], 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ] );
    $wp_customize->add_control( $id, [ 'label' => $f['label'], 'section' => 'psoe_home', 'type' => 'text' ] );
  }

  $wp_customize->add_setting( 'psoe_hero_img', [ 'sanitize_callback' => 'esc_url_raw' ] );
  $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'psoe_hero_img', [
    'label' => __( 'Imagen hero', 'psoe-participatiu' ),
    'section' => 'psoe_home',
  ] ) );
} );
