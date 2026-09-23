<?php
/**
 * Template Name: Enviar propuesta (PSOE)
 * Description: Portada de envío con hero centrado + tarjeta "Nueva Propuesta" (Image 1).
 * Asigna esta plantilla a la página que contenga el formulario Gravity de propuestas.
 */
get_header();
while ( have_posts() ) : the_post();
  $subtitle = has_excerpt()
    ? get_the_excerpt()
    : 'Envíanos tus propuestas y construyamos juntos el futuro de todos. Tu opinión es la base de nuestra política.';
?>
<section class="bg-[#FBF7F6]">
  <div class="max-w-[880px] mx-auto px-4 sm:px-6 pt-10 pb-16">

    <div class="text-center max-w-[700px] mx-auto">
      <h1 class="psoe-hero-title">Tu voz en el <span class="red">programa 2027</span></h1>
      <p class="psoe-hero-sub mt-3"><?php echo esc_html( $subtitle ); ?></p>
    </div>

    <div class="psoe-card mt-8">
      <div class="psoe-card-head">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D50024" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
          <path d="M4 6h12M4 10h9M4 14h7"/><path d="M15 16l4 4m0-4l-4 4" stroke-linecap="round"/>
        </svg>
        <span><?php esc_html_e( 'Nueva Propuesta', 'psoe-participatiu' ); ?></span>
      </div>
      <div class="psoe-card-body psoe-prose">
        <?php the_content(); ?>
      </div>
    </div>

    <p class="mt-4 text-center text-[12px] text-[#A98A90]"><?php esc_html_e( 'Esta propuesta será revisada por el equipo de coordinación del programa electoral.', 'psoe-participatiu' ); ?></p>

  </div>
</section>
<?php endwhile; get_footer(); ?>
