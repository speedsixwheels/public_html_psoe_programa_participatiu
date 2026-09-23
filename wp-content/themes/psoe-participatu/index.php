<?php get_header(); ?>
<div class="max-w-[1120px] mx-auto px-4 sm:px-6 py-8">
  <h1 class="text-2xl font-extrabold tracking-tight"><?php
    if ( is_search() ) printf( 'Resultados para “%s”', get_search_query() );
    elseif ( is_archive() ) the_archive_title();
    else wp_title( '' );
  ?></h1>
  <div class="mt-5 grid md:grid-cols-3 gap-4">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
      if ( get_post_type() === 'propuesta' ) get_template_part( 'template-parts/card', 'propuesta' );
      else { ?>
        <article class="bg-white rounded-[12px] shadow-card border border-black/5 p-5">
          <h2 class="font-bold"><a href="<?php the_permalink(); ?>" class="hover:text-[#E30613]"><?php the_title(); ?></a></h2>
          <p class="mt-2 text-[13px] text-neutral-600"><?php echo esc_html( get_the_excerpt() ); ?></p>
        </article>
      <?php }
    endwhile; else : ?>
      <p class="text-sm text-neutral-500">Sin resultados. Prueba con otra búsqueda.</p>
    <?php endif; ?>
  </div>
  <div class="mt-6"><?php the_posts_pagination( [ 'mid_size' => 2 ] ); ?></div>
</div>
<?php get_footer(); ?>
