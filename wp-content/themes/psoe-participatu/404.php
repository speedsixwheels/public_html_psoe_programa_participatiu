<?php get_header(); ?>
<div class="max-w-[560px] mx-auto px-4 py-20 text-center">
  <p class="eyebrow text-[#E30613] text-[11px] font-bold tracking-widest uppercase">Error 404</p>
  <h1 class="mt-2 text-3xl font-extrabold">Página no encontrada</h1>
  <p class="mt-2 text-sm text-neutral-500">La página que buscas no existe o ha sido movida.</p>
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mt-5 inline-flex text-sm font-semibold bg-[#E30613] text-white rounded-md px-5 py-2.5">Volver al inicio</a>
</div>
<?php get_footer(); ?>
