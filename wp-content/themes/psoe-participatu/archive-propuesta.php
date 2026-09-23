<?php get_header(); ?>
<div class="max-w-[1120px] mx-auto px-4 sm:px-6 py-8">
  <div class="flex flex-wrap items-end justify-between gap-3">
    <div>
      <p class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#E30613]">Participación</p>
      <h1 class="text-[26px] font-extrabold tracking-tight">Propuestas ciudadanas</h1>
      <p class="text-[13px] text-neutral-500">Explora, filtra por categoría y apoya tus favoritas.</p>
    </div>
    <form method="get" class="flex gap-2">
      <input type="hidden" name="post_type" value="propuesta">
      <input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Buscar…" class="text-sm bg-white border border-black/10 rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-[#E30613]/30">
      <button class="text-sm font-semibold bg-[#E30613] text-white rounded-md px-4 py-2">Buscar</button>
    </form>
  </div>

  <?php $cats = get_terms( [ 'taxonomy' => 'categoria_propuesta', 'hide_empty' => false ] ); ?>
  <?php if ( $cats && ! is_wp_error( $cats ) ) : ?>
    <div class="mt-4 flex flex-wrap gap-2">
      <a href="<?php echo esc_url( get_post_type_archive_link( 'propuesta' ) ); ?>" class="text-[12px] font-semibold px-3 py-1.5 rounded-full bg-neutral-900 text-white">Todas</a>
      <?php foreach ( $cats as $c ) : ?>
        <a href="<?php echo esc_url( get_term_link( $c ) ); ?>" class="text-[12px] font-semibold px-3 py-1.5 rounded-full bg-white border border-black/10 hover:border-[#E30613] hover:text-[#E30613]"><?php echo esc_html( $c->name ); ?></a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="mt-5 grid md:grid-cols-3 gap-4">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
      get_template_part( 'template-parts/card', 'propuesta' );
    endwhile; else : ?>
      <p class="text-sm text-neutral-500">Aún no hay propuestas. ¡Sé la primera persona en participar!</p>
    <?php endif; ?>
  </div>
  <div class="mt-6"><?php the_posts_pagination(); ?></div>
</div>
<?php get_footer(); ?>
