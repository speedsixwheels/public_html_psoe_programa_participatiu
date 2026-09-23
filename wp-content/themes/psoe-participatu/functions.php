<?php
/**
 * PSOE Participatiu 2027 — functions
 *
 * @package psoe-participatiu
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'PSOE_VER', '1.6.0' );
define( 'PSOE_DIR', get_template_directory() );
define( 'PSOE_URI', get_template_directory_uri() );

// Includes
require_once PSOE_DIR . '/inc/cpt-propuestas.php';
require_once PSOE_DIR . '/inc/votos.php';
require_once PSOE_DIR . '/inc/customizer.php';
require_once PSOE_DIR . '/inc/helpers.php';
require_once PSOE_DIR . '/inc/gravity.php';
require_once PSOE_DIR . '/inc/resultados-encuesta.php';

add_action( 'after_setup_theme', function () {
  add_theme_support( 'title-tag' );
  add_theme_support( 'post-thumbnails' );
  add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
  add_theme_support( 'custom-logo', [ 'height' => 40, 'width' => 160, 'flex-height' => true, 'flex-width' => true ] );
  add_theme_support( 'responsive-embeds' );
  add_theme_support( 'align-wide' );
  add_editor_style();

  register_nav_menus( [
    'primary' => __( 'Menú principal', 'psoe-participatiu' ),
    'footer_participa' => __( 'Footer: Participa', 'psoe-participatiu' ),
    'footer_partido'   => __( 'Footer: El Partido', 'psoe-participatiu' ),
    'footer_legal'     => __( 'Footer: Legal', 'psoe-participatiu' ),
  ] );

  add_image_size( 'hero', 1600, 700, true );
  add_image_size( 'area', 600, 420, true );
  add_image_size( 'card', 800, 500, true );
} );

// Tailwind via CDN (dev-ready) + custom CSS + JS.
// Para producción: npm run build y se cargará assets/css/theme.css si existe.
add_action( 'wp_enqueue_scripts', function () {
  // Inter font
  wp_enqueue_style( 'psoe-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap', [], null );

  // Tailwind Play CDN — permite usar clases sin compilar
  wp_enqueue_script( 'tailwind-cdn', 'https://cdn.tailwindcss.com', [], null, false );
  $tw_config = "tailwind.config = " . wp_json_encode( [
    'theme' => [
      'extend' => [
        'colors' => [
          'brand' => [ 'DEFAULT' => '#E30613', 'dark' => '#B0050F', 'darker' => '#7A030B', 'light' => '#FF4D5A', 'soft' => '#FDECEC', 'muted' => '#F9F1F0' ],
          'ink'   => [ 'DEFAULT' => '#1C1214', 'soft' => '#5B4B4E', 'muted' => '#8A7A7D' ],
          'cream' => '#FAF7F5',
        ],
        'fontFamily' => [ 'sans' => [ 'Inter', 'system-ui', 'sans-serif' ] ],
        'maxWidth' => [ 'shell' => '1120px' ],
        'boxShadow' => [ 'card' => '0 1px 2px rgba(0,0,0,.04), 0 8px 24px -12px rgba(0,0,0,.12)' ],
        'borderRadius' => [ 'xl2' => '14px' ],
      ],
    ],
  ] );
  wp_add_inline_script( 'tailwind-cdn', $tw_config, 'after' );

  // Estilos del theme (con filemtime para evitar caché tras cada cambio de CSS/JS).
  wp_enqueue_style( 'psoe-style', get_stylesheet_uri(), [], PSOE_VER );
  wp_enqueue_style( 'psoe-custom', PSOE_URI . '/assets/css/custom.css', [ 'psoe-style' ], filemtime( PSOE_DIR . '/assets/css/custom.css' ) );

  // Si existe compilado de producción, cargarlo también
  if ( file_exists( PSOE_DIR . '/assets/css/theme.css' ) ) {
    wp_enqueue_style( 'psoe-theme', PSOE_URI . '/assets/css/theme.css', [ 'psoe-custom' ], filemtime( PSOE_DIR . '/assets/css/theme.css' ) );
  }

  wp_enqueue_script( 'psoe-main', PSOE_URI . '/js/main.js', [], filemtime( PSOE_DIR . '/js/main.js' ), true );
  wp_localize_script( 'psoe-main', 'PSOE', [
    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    'nonce'   => wp_create_nonce( 'psoe_vote' ),
    'i18n'    => [ 'voted' => __( '¡Gracias por tu apoyo!', 'psoe-participatiu' ) ],
  ] );

  if ( is_singular() && comments_open() ) wp_enqueue_script( 'comment-reply' );
} );

// Search: limitar placeholder y redirigir búsqueda de propuestas
add_filter( 'get_search_query', function ( $q ) { return $q; } );

// Excerpt
add_filter( 'excerpt_length', fn() => 22 );
add_filter( 'excerpt_more', fn() => '…' );

add_filter( 'nav_menu_link_attributes', function ( $atts ) {
  $atts['class'] = 'text-[15px] font-medium text-[#222] hover:text-[#E30613] transition-colors';
  return $atts;
} );

// Admin: mensaje de ayuda
add_action( 'admin_notices', function () {
  if ( get_current_screen()->id !== 'themes' ) return;
  echo '<div class="notice notice-info"><p><strong>PSOE Participatiu:</strong> edita Portada desde Apariencia → Personalizar (stats, hero, newsletter) y crea Propuestas desde el CPT “Propuestas”. Asigna una página con plantilla Portada como portada estática.</p></div>';
} );
