<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<div class="max-w-[760px] mx-auto px-4 sm:px-6 py-8">
  <a href="<?php echo esc_url( get_post_type_archive_link( 'propuesta' ) ); ?>" class="text-[12px] font-semibold text-[#E30613]">← Volver a propuestas</a>
  <div class="mt-3 flex items-center gap-2"><?php psoe_categoria_chip(); ?><span class="text-[12px] text-neutral-400"><?php echo esc_html( psoe_time_ago() ); ?></span></div>
  <h1 class="mt-2 text-[28px] font-extrabold tracking-tight leading-tight"><?php the_title(); ?></h1>
  <div class="mt-3 flex items-center justify-between bg-white border border-black/5 rounded-[12px] p-4">
    <span class="inline-flex items-center gap-2 text-sm"><?php psoe_avatar_or_initial( get_the_author() ); ?> <?php the_author(); ?></span>
    <?php $v = psoe_get_votos( get_the_ID() ); $done = psoe_has_voted( get_the_ID() ); ?>
    <button data-vote="<?php the_ID(); ?>" <?php disabled( $done ); ?> class="vote-btn <?php echo $done ? 'voted' : ''; ?> inline-flex items-center gap-2 text-sm font-bold text-[#E30613] bg-[#FDECEC] rounded-md px-4 py-2 hover:bg-[#E30613] hover:text-white">
      ♥ Apoyar · <span class="vote-count"><?php echo esc_html( psoe_format_votos( $v ) ); ?></span>
    </button>
  </div>
  <?php if ( has_post_thumbnail() ) : ?>
    <div class="mt-4 overflow-hidden rounded-[12px]"><?php the_post_thumbnail( 'large', [ 'class' => 'w-full' ] ); ?></div>
  <?php endif; ?>
  <div class="mt-5 prose prose-neutral max-w-none text-[15px] leading-relaxed"><?php the_content(); ?></div>
  <?php comments_template(); ?>
</div>
<?php endwhile; ?>
<?php get_footer(); ?>
