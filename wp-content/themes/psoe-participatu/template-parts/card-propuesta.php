<?php
// Card propuesta reutilizable
$votos = function_exists( 'psoe_get_votos' ) ? psoe_get_votos( get_the_ID() ) : 0;
$votado = function_exists( 'psoe_has_voted' ) ? psoe_has_voted( get_the_ID() ) : false;
?>
<article class="propuesta-card bg-white rounded-[12px] shadow-card border border-black/5 p-5 flex flex-col">
  <div class="flex items-center justify-between gap-2">
    <?php psoe_categoria_chip(); ?>
    <span class="inline-flex items-center gap-1 text-[11px] text-neutral-400 shrink-0">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
      <?php echo esc_html( psoe_time_ago() ); ?>
    </span>
  </div>
  <h3 class="mt-2.5 text-[14px] font-bold leading-snug">
    <a href="<?php the_permalink(); ?>" class="hover:text-[#E30613]"><?php the_title(); ?></a>
  </h3>
  <p class="mt-2 text-[12.5px] leading-relaxed text-neutral-600 clamp-3"><?php echo esc_html( get_the_excerpt() ); ?></p>
  <div class="mt-4 pt-3 border-t border-black/5 flex items-center justify-between mt-auto">
    <span class="inline-flex items-center gap-2 text-[12px] font-medium text-neutral-600">
      <?php psoe_avatar_or_initial( get_the_author() ); ?>
      <?php the_author(); ?>
    </span>
    <button class="vote-btn <?php echo $votado ? 'voted' : ''; ?> inline-flex items-center gap-1 text-[12px] font-bold text-[#E30613] bg-[#FDECEC] border border-transparent rounded px-2 py-1 hover:bg-[#E30613] hover:text-white transition-colors"
      data-vote="<?php the_ID(); ?>" <?php disabled( $votado ); ?>>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 11l3 3 7-7 M12 3a9 9 0 100 18 9 9 0 000-18"/></svg>
      <span class="vote-count"><?php echo esc_html( psoe_format_votos( $votos ) ); ?></span>
    </button>
  </div>
</article>
