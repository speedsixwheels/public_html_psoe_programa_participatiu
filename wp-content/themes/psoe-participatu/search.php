<?php get_header(); ?>
<div class="max-w-[1120px] mx-auto px-4 py-10">
  <h1 class="text-2xl font-extrabold">Resultados para “<?php echo esc_html( get_search_query() ); ?>”</h1>
  <div class="mt-5 grid md:grid-cols-3 gap-4">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
      get_template_part( 'template-parts/card', 'propuesta' );
    endwhile; else : ?><p class="text-sm text-neutral-500">Sin resultados.</p><?php endif; ?>
  </div>
</div>
<?php get_footer(); ?>
