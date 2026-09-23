<?php
/**
 * Plantilla genérica de página — estilo Encuesta (Image 2 PSOE-ificado).
 * Título a la izquierda + tarjeta blanca con el contenido (formulario Gravity).
 */
get_header();
while ( have_posts() ) : the_post();
?>
<section class="bg-[#FBF7F6]">
  <div class="max-w-[800px] mx-auto px-4 sm:px-6 pt-10 pb-16">
    <h1 class="text-[28px] sm:text-[32px] font-extrabold tracking-tight text-[#141414]"><?php the_title(); ?></h1>

    <?php if ( has_excerpt() ) : ?>
      <p class="mt-3 text-[16px] leading-relaxed text-[#6E565B]"><?php echo esc_html( get_the_excerpt() ); ?></p>
    <?php endif; ?>

    <?php if ( has_post_thumbnail() ) : ?>
      <div class="mt-5 rounded-[14px] overflow-hidden shadow-card"><?php the_post_thumbnail( 'large', [ 'class' => 'w-full' ] ); ?></div>
    <?php endif; ?>

    <div class="psoe-card mt-6">
      <div class="psoe-card-body psoe-prose">
        <?php the_content(); ?>
      </div>
    </div>
  </div>
</section>
<?php endwhile; get_footer(); ?>
