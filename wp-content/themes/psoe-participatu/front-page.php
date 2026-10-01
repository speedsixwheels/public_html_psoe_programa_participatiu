<?php
/**
 * Portada — Enquesta (PSOE)
 * Description: Nueva portada con un hero 100% de ancho, imagen y slogan,
 * seguido del contenido y la tarjeta del formulario Gravity.
 */
get_header();
while ( have_posts() ) : the_post();
  $subtitle   = has_excerpt() ? get_the_excerpt() : '';
  $hero_img   = get_theme_mod( 'psoe_hero_img' ) ?: get_stylesheet_directory_uri() . '/assets/img/hero.jpg';
  $hero_badge = get_theme_mod( 'psoe_hero_badge', 'Programa electoral col·laboratiu' );
  $hero_title = get_theme_mod( 'psoe_hero_title', 'Construïm un nou Vinaròs. Participa amb les teves idees, y vota les propostes.' );
  $hero_sub   = get_theme_mod( 'psoe_hero_sub', 'Avancem cap al 2027.' );
?>

<!-- FULL WIDTH HERO -->
<section class="relative overflow-hidden bg-neutral-900 text-white min-h-[420px] sm:min-h-[480px] flex items-center">
  <img src="<?php echo esc_url( $hero_img ); ?>" alt="Participación ciudadana PSOE" class="absolute inset-0 w-full h-full object-cover opacity-45">
  <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
  <div class="relative max-w-[1120px] mx-auto px-4 sm:px-6 py-16 w-full">
    <div class="max-w-[640px]">
      <span class="inline-flex items-center gap-1.5 text-[11px] font-bold tracking-[0.14em] uppercase text-white bg-[#E30613] rounded-full px-3.5 py-1 mb-4 shadow-sm">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
        <?php echo esc_html( $hero_badge ); ?>
      </span>
      <h1 class="text-[36px] sm:text-[50px] font-extrabold tracking-tight leading-[1.08] text-white"><?php echo esc_html( $hero_title ); ?></h1>
      <p class="mt-4 text-[15px] sm:text-[17px] text-white/95 leading-relaxed"><?php echo esc_html( $hero_sub ); ?></p>
      <!--
      <div class="mt-7 flex flex-wrap gap-3">
        <a href="#respon" class="inline-flex items-center text-[14px] font-semibold bg-[#E30613] hover:bg-[#C20510] text-white rounded-md px-6 py-3 transition-colors shadow-md">Participar en la Consulta</a>
        <a href="<?php echo esc_url( get_post_type_archive_link( 'propuesta' ) ?: home_url( '/?post_type=propuesta' ) ); ?>" class="inline-flex items-center text-[14px] font-semibold bg-white/10 hover:bg-white/20 border border-white/30 text-white rounded-md px-6 py-3 backdrop-blur transition-colors">Ver Propuestas</a>
      </div> 
-->
    </div>
  </div>
</section> 

<section class="bg-[#FBF7F6]">
  <div class="max-w-[880px] mx-auto px-4 sm:px-6 pt-10 pb-16">

    <div class="max-w-[700px]">
      <span class="inline-flex items-center gap-1.5 text-[11px] font-bold tracking-[0.14em] uppercase text-[#D50024] bg-[#FDECEC] border border-[#E30613]/15 rounded-full px-3 py-1">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
        <?php esc_html_e( 'Fes sentir la teva veu', 'psoe-participatiu' ); ?>
      </span>
      <br /><br />
    <!--   <h1 class="mt-3 text-[30px] sm:text-[38px] font-extrabold tracking-tight leading-[1.1] text-[#141414]"><?php the_title(); ?></h1> -->
      <?php if ( $subtitle ) : ?>
        <p class="psoe-hero-sub mt-3"><?php echo esc_html( $subtitle ); ?></p>
      <?php endif; ?>
    </div>

    <?php if ( has_post_thumbnail() ) : ?>
      <div class="mt-6 rounded-[14px] overflow-hidden shadow-card"><?php the_post_thumbnail( 'large', [ 'class' => 'w-full' ] ); ?></div>
    <?php endif; ?>

    <div class="psoe-card mt-8" id="respon">
      <div class="psoe-card-head">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D50024" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>
        </svg>
        <span><?php esc_html_e( 'Selecciona les teves prefències.', 'psoe-participatiu' ); ?></span>
      </div>
      <div class="psoe-card-body psoe-prose">
        <?php 
          require_once( ABSPATH . 'wp-content/themes/psoe-participatu/inc/vpn-detect.php' ); 
       
          $apiKeys = [
              '3753k3-6tm77j-9k58eq-owp3q8' => 'it@speedsixwheels.com',
              '2583u1-4m79t9-0760l6-e59658' => 'esse1981@gmail.com',
              //'API_KEY_3'
          ];

          $ip = getUserIP();

          //$result = detectVPNOrProxy($ip, $apiKeys);

            /* header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); */
   
          //pre($result);

        // Diagnòstic (només admins): peticions restants + API key en ús.
        psoe_vpn_debug_panel( $apiKeys, $ip, $result ?? null );


        if(!isset($result) || !$result['vpn_detected']){
            echo do_shortcode("[gravityform id='2' title='false' description='false']"); 
        }
        else{
          echo "<p>" . esc_html__( "S'ha detectat una connexió VPN o proxy. No es permet partipar amb aquesta connexió.", 'psoe-participatiu' ) . "</p>";
        }
         
        ?>
      </div>
    </div>

  </div>
</section>
<?php endwhile; get_footer(); ?>
