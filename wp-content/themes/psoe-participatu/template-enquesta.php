<?php
/**
 * Template Name: Enquesta (PSOE)
 * Description: Plantilla dedicada solo para la página de la enquesta: hero propio
 * y tarjeta del formulario Gravity. Los resultados van en la plantilla
 * "Resultats (PSOE)".
 */
get_header();
while ( have_posts() ) : the_post();
  $subtitle = has_excerpt() ? get_the_excerpt() : '';
?>
<section class="bg-[#FBF7F6]">
  <div class="max-w-[880px] mx-auto px-4 sm:px-6 pt-10 pb-16">

    <div class="max-w-[700px]">
      <span class="inline-flex items-center gap-1.5 text-[11px] font-bold tracking-[0.14em] uppercase text-[#D50024] bg-[#FDECEC] border border-[#E30613]/15 rounded-full px-3 py-1">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
        <?php esc_html_e( 'Participa · Enquesta', 'psoe-participatiu' ); ?>
      </span>
      <h1 class="mt-3 text-[30px] sm:text-[38px] font-extrabold tracking-tight leading-[1.1] text-[#141414]"><?php the_title(); ?></h1>
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
        <span><?php esc_html_e( 'Respon l’enquesta', 'psoe-participatiu' ); ?></span>
      </div>
      <div class="psoe-card-body psoe-prose">
        <?php the_content(); ?>
      </div>
    </div>

  </div>
</section>
<?php endwhile; get_footer(); ?>
