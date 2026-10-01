<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-[#FBF7F6]' ); ?>>
<?php wp_body_open(); ?>

<header class="sticky top-0 z-50 bg-white border-b border-[#F1E4E4]">
  <div class="max-w-[1200px] mx-auto px-4 sm:px-6">
    <div class="flex items-center gap-4 h-[64px]">
      <!-- Logo PSOE -->
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-2 shrink-0" aria-label="PSOE inicio">
        <svg width="26" height="26" viewBox="0 0 32 32" fill="#E30613" aria-hidden="true">
          <path d="M16 28.5C15.5 28.1 3.5 20.5 3.5 11.8C3.5 7.9 6.6 5 10.4 5C13.1 5 15.3 6.2 16 8.2C16.7 6.2 18.9 5 21.6 5C25.4 5 28.5 7.9 28.5 11.8C28.5 20.5 16.5 28.1 16 28.5Z"/>
        </svg>
        <span class="font-extrabold tracking-tight text-[22px] text-[#141414]">VINARÒS</span>
      </a>

      <div class="flex-1"></div>

      <!-- Nav desktop -->
      <nav class="hidden md:flex items-center gap-7" aria-label="Principal">
        <?php
        $fallback = [
          'Programa 2027' => home_url( '/' ),
          'Noticias'      => home_url( '/#noticias' ),
          'Participa'     => get_post_type_archive_link( 'propuesta' ) ?: home_url( '/#propuestas' ),
        ];
        if ( has_nav_menu( 'primary' ) ) {
          wp_nav_menu( [ 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'flex items-center gap-7', 'items_wrap' => '%3$s' ] );
        } else {
          foreach ( $fallback as $label => $url ) {
            printf( '<a class="text-[15px] font-medium text-[#222] hover:text-[#E30613] transition-colors" href="%s">%s</a>', esc_url( $url ), esc_html( $label ) );
          }
        }
        ?>
      </nav>

     <!--  <a href="<?php echo esc_url( wp_login_url() ); ?>"
         class="hidden sm:inline-flex items-center text-[15px] font-semibold bg-[#D50024] hover:bg-[#B0050F] text-white rounded-[10px] px-5 py-2.5 shadow-md transition-colors ml-2">
        <?php esc_html_e( 'Iniciar Sessió', 'psoe-participatiu' ); ?>
      </a>
 -->
      <!-- Hamburguesa móvil -->
      <button id="menuBtn" class="md:hidden inline-flex items-center justify-center w-9 h-9 rounded-md hover:bg-neutral-100" aria-label="Abrir menú" aria-expanded="false">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
      </button>
    </div>
  </div>
  <!-- Menú móvil: misma fuente que el desktop (menú WP "primary" o fallback) -->
  <div id="mobileMenu" class="md:hidden hidden border-t border-[#F1E4E4] bg-white px-4 py-3 space-y-1">
    <?php
    if ( has_nav_menu( 'primary' ) ) {
      $locs = get_nav_menu_locations();
      $mobj = ! empty( $locs['primary'] ) ? wp_get_nav_menu_object( $locs['primary'] ) : false;
      $mitems = $mobj ? wp_get_nav_menu_items( $mobj->term_id ) : false;
      if ( $mitems ) {
        foreach ( $mitems as $item ) {
          printf(
            '<a class="block py-2 text-[15px] font-medium" href="%s">%s</a>',
            esc_url( $item->url ),
            esc_html( $item->title )
          );
        }
      }
    } else {
    ?>
    <a class="block py-2 text-[15px] font-medium" href="<?php echo esc_url( home_url( '/' ) ); ?>">Programa 2027</a>
    <a class="block py-2 text-[15px] font-medium" href="<?php echo esc_url( home_url( '/#noticias' ) ); ?>">Noticias</a>
    <a class="block py-2 text-[15px] font-medium" href="<?php echo esc_url( get_post_type_archive_link( 'propuesta' ) ?: home_url( '/#propuestas' ) ); ?>">Participa</a>
    <?php } ?>
    <a class="block mt-2 text-center text-[15px] font-semibold bg-[#D50024] text-white rounded-[10px] px-5 py-2.5" href="<?php echo esc_url( wp_login_url() ); ?>">Iniciar Sesión</a>
  </div>
</header>

<main id="content">
