<?php
/**
 * Portada — réplica captura
 */
get_header();

$hero_img = get_theme_mod( 'psoe_hero_img' ) ?: 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=1600&auto=format&fit=crop';
$hero_badge = get_theme_mod( 'psoe_hero_badge', 'Programa electoral colaborativo' );
$hero_title = get_theme_mod( 'psoe_hero_title', 'Tu voz, nuestro compromiso. El futuro de España.' );
$hero_sub   = get_theme_mod( 'psoe_hero_sub', 'Construyamos juntos el Programa Electoral 2027. Participa con tus ideas, debate con otros ciudadanos y vota las propuestas para una España mejor.' );
?>

<!-- HERO -->
<div class="max-w-[1120px] mx-auto px-4 sm:px-6 mt-4">
  <section class="relative overflow-hidden rounded-[12px] shadow-card min-h-[380px] sm:min-h-[420px] flex items-end sm:items-center">
    <img src="<?php echo esc_url( $hero_img ); ?>" alt="Ciudadanos debatiendo" class="absolute inset-0 w-full h-full object-cover">
    <div class="absolute inset-0 hero-overlay"></div>
    <div class="relative p-6 sm:p-10 max-w-[560px] text-white">
      <span class="inline-flex items-center text-[10px] font-bold tracking-[0.12em] uppercase border border-white/40 bg-white/10 backdrop-blur rounded-full px-3 py-1"><?php echo esc_html( $hero_badge ); ?></span>
      <h1 class="mt-3 text-[32px] sm:text-[44px] leading-[1.05] font-extrabold tracking-tight"><?php echo esc_html( $hero_title ); ?></h1>
      <p class="mt-3 text-[13px] sm:text-[14px] leading-relaxed text-white/85"><?php echo esc_html( $hero_sub ); ?></p>
      <div class="mt-5 flex flex-wrap gap-2">
        <a href="<?php echo esc_url( get_post_type_archive_link( 'propuesta' ) ?: '#propuestas' ); ?>" class="inline-flex items-center text-[13px] font-semibold bg-white text-[#E30613] rounded-md px-5 py-2.5 hover:bg-neutral-100 transition-colors">Participar Ahora</a>
        <a href="#valores" class="inline-flex items-center text-[13px] font-semibold border border-white/60 text-white rounded-md px-5 py-2.5 hover:bg-white/10 transition-colors">Leer Manifiesto</a>
      </div>
    </div>
  </section>
</div>

<!-- STATS -->
<section class="bg-white border-b border-black/5 mt-0">
  <div class="max-w-[1120px] mx-auto px-4 sm:px-6 py-6 grid grid-cols-2 md:grid-cols-4 gap-6">
    <?php
    $stats = [
      [ get_theme_mod( 'psoe_stat1_n', '1.250' ), get_theme_mod( 'psoe_stat1_l', 'Ideas recibidas' ), 'Propuestas', 'M4 5h16v14H4z M8 3v4 M16 3v4' ],
      [ get_theme_mod( 'psoe_stat2_n', '15.4K' ), get_theme_mod( 'psoe_stat2_l', 'Apoyos ciudadanos' ), 'Votos', 'M7 11l3 3 7-7 M12 3a9 9 0 100 18 9 9 0 000-18' ],
      [ get_theme_mod( 'psoe_stat3_n', '320' ), get_theme_mod( 'psoe_stat3_l', 'Conversaciones activas' ), 'Debates', 'M4 5h16v11H8l-4 4z' ],
      [ get_theme_mod( 'psoe_stat4_n', '12' ), get_theme_mod( 'psoe_stat4_l', 'Áreas temáticas' ), 'Categorías', 'M12 3l2 5h5l-4 3 1.5 5-4.5-3-4.5 3L9 11 5 8h5z' ],
    ];
    foreach ( $stats as $s ) : ?>
      <div>
        <p class="text-[11px] font-bold tracking-wider uppercase text-[#E30613] flex items-center gap-1">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="<?php echo esc_attr( $s[2] === 'Propuestas' ? 'M6 2h9l5 5v15H6z M14 2v6h6' : ( $s[2] === 'Votos' ? 'M7 11l3 3 7-7 M12 3a9 9 0 100 18 9 9 0 000-18' : ( $s[2] === 'Debates' ? 'M4 5h16v11H8l-4 4z' : 'M12 3v18 M5 8h14 M7 8l-3 6a3.5 3.5 0 007 0L8 8 M17 8l-3 6a3.5 3.5 0 007 0l-3-6' ) ) ); ?>"/></svg>
          <?php echo esc_html( $s[2] ); ?>
        </p>
        <p class="text-2xl font-extrabold tracking-tight mt-1"><?php echo esc_html( $s[0] ); ?></p>
        <p class="text-xs text-neutral-500"><?php echo esc_html( $s[1] ); ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- VALORES -->
<section id="valores" class="py-10">
  <div class="max-w-[1120px] mx-auto px-4 sm:px-6 text-center">
    <p class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#E30613]">Nuestros pilares</p>
    <h2 class="mt-1 text-[24px] font-extrabold tracking-tight">Valores que nos unen</h2>
    <p class="mt-2 text-[13px] text-neutral-500 max-w-[560px] mx-auto">Cada propuesta se evalúa bajo la lupa de nuestros principios fundamentales, asegurando que avanzamos sin dejar a nadie atrás.</p>

    <div class="mt-6 grid md:grid-cols-3 gap-4 text-left">
      <?php
      $valores = [
        [ 'Justicia Social', 'Defendemos la igualdad de oportunidades y la protección de los más vulnerables como base de nuestra democracia.', 'M12 3v18 M5 7h14 M7 7l-3 7a3.5 3.5 0 007 0L8 7 M17 7l-3 7a3.5 3.5 0 007 0l-3-7 M8 21h8' ],
        [ 'Feminismo e Igualdad', 'Trabajamos por una sociedad libre de discriminación, donde la igualdad entre hombres y mujeres sea real y efectiva.', 'M8 11a4 4 0 118 0 4 4 0 01-8 0z M12 15v6 M9 18h6 M4 6a8 8 0 0112 0 M6.5 6.5A8 8 0 0112 4' ],
        [ 'Sostenibilidad', 'Apostamos por una transición ecológica justa que impulse la economía verde y proteja nuestro patrimonio natural.', 'M12 3c-5 3-7 7-7 11a7 7 0 0014 0c0-4-2-8-7-11z M12 21v-8' ],
      ];
      foreach ( $valores as $v ) : ?>
        <article class="bg-white rounded-[14px] shadow-card border border-black/5 p-6">
          <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-[#FDECEC] text-[#E30613]">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="<?php echo esc_attr( $v[2] ); ?>"/></svg>
          </span>
          <h3 class="mt-4 text-[14px] font-bold"><?php echo esc_html( $v[0] ); ?></h3>
          <p class="mt-2 text-[12.5px] leading-relaxed text-neutral-600"><?php echo esc_html( $v[1] ); ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ÁREAS -->
<section class="py-8 bg-white border-y border-black/5">
  <div class="max-w-[1120px] mx-auto px-4 sm:px-6">
    <div class="flex items-end justify-between gap-4">
      <div>
        <h2 class="text-[20px] font-extrabold tracking-tight">Áreas Temáticas Principales</h2>
        <p class="text-[12.5px] text-neutral-500 mt-1">Explora y propon en los sectores clave para el desarrollo.</p>
      </div>
      <a href="<?php echo esc_url( get_post_type_archive_link( 'propuesta' ) ?: '#' ); ?>" class="text-[12px] font-semibold text-[#E30613] hover:underline shrink-0">Ver todas →</a>
    </div>

    <div class="mt-5 grid grid-cols-2 lg:grid-cols-4 gap-3">
      <?php
      $areas_static = [
        [ 'Sanidad Pública', 'Prioridad', 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?q=80&w=600&auto=format&fit=crop' ],
        [ 'Educación de Calidad', 'Futuro', 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=600&auto=format&fit=crop' ],
        [ 'Transición Ecológica', 'Verde', 'https://images.unsplash.com/photo-1466611653911-95081537e5b7?q=80&w=600&auto=format&fit=crop' ],
        [ 'Empleo Digno', 'Dignidad', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=600&auto=format&fit=crop' ],
      ];
      // Si hay términos con imagen, úsalos; si no, fallback estático
      $terms = get_terms( [ 'taxonomy' => 'area_tematica', 'hide_empty' => false, 'number' => 4 ] );
      $i = 0;
      foreach ( $areas_static as $a ) :
        $link = ( ! empty( $terms ) && ! is_wp_error( $terms ) && isset( $terms[ $i ] ) ) ? get_term_link( $terms[ $i ] ) : '#';
        if ( is_wp_error( $link ) ) $link = '#';
      ?>
        <a href="<?php echo esc_url( $link ); ?>" class="area-card group relative overflow-hidden rounded-[10px] min-h-[190px] sm:min-h-[220px] flex items-end">
          <img src="<?php echo esc_url( $a[2] ); ?>" alt="<?php echo esc_attr( $a[0] ); ?>" class="absolute inset-0 w-full h-full object-cover" loading="lazy">
          <span class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></span>
          <span class="relative p-3 text-white">
            <span class="inline-flex items-center gap-1 text-[10px] font-bold tracking-wider uppercase text-white/80">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
              <?php echo esc_html( $a[1] ); ?>
            </span>
            <span class="block text-[14px] font-bold leading-tight mt-0.5"><?php echo esc_html( $a[0] ); ?></span>
          </span>
        </a>
      <?php $i++; endforeach; ?>
    </div>
  </div>
</section>

<!-- PROPUESTAS -->
<section id="propuestas" class="py-8">
  <div class="max-w-[1120px] mx-auto px-4 sm:px-6">
    <h2 class="text-[20px] font-extrabold tracking-tight">Propuestas Destacadas</h2>

    <div class="mt-4 grid md:grid-cols-3 gap-4">
      <?php
      $q = new WP_Query( [
        'post_type' => 'propuesta',
        'posts_per_page' => 3,
        'meta_key' => '_psoe_votos',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
      ] );
      if ( $q->have_posts() ) :
        while ( $q->have_posts() ) : $q->the_post();
          get_template_part( 'template-parts/card', 'propuesta' );
        endwhile; wp_reset_postdata();
      else :
        // Fallback idéntico a la captura cuando no hay contenido
        $demo = [
          [ 'Digitalización', '#DBEAFE', '#1E40AF', 'Hace 2h', 'Acceso universal a internet de alta velocidad en zonas rurales', 'Garantizar por ley que todos los municipios de menos de 5.000 habitantes tengan acceso a fibra óptica subvencionada para combatir la…', 'Mario G.', '428' ],
          [ 'Ecología', '#DCFCE7', '#166534', 'Hace 5h', 'Bono de transporte gratuito para menores de 30 años', 'Incentivar el uso del transporte público entre los jóvenes para reducir la huella de carbono y facilitar la movilidad estudiantil y laboral.', 'Carlos R.', '1.2k' ],
          [ 'Vivienda', '#EDE9FE', '#5B21B6', 'Hace 1d', 'Parque público de alquiler del 20% en nuevas construcciones', 'Obligatoriedad de destinar un porcentaje significativo de toda nueva promoción inmobiliaria a alquiler social gestionado por el estado.', 'Laura M.', '856' ],
        ];
        foreach ( $demo as $d ) : ?>
          <article class="propuesta-card bg-white rounded-[12px] shadow-card border border-black/5 p-5 flex flex-col">
            <div class="flex items-center justify-between">
              <span class="chip" style="background:<?php echo esc_attr( $d[1] ); ?>;color:<?php echo esc_attr( $d[2] ); ?>"><?php echo esc_html( $d[0] ); ?></span>
              <span class="inline-flex items-center gap-1 text-[11px] text-neutral-400">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                <?php echo esc_html( $d[3] ); ?>
              </span>
            </div>
            <h3 class="mt-2.5 text-[14px] font-bold leading-snug"><?php echo esc_html( $d[4] ); ?></h3>
            <p class="mt-2 text-[12.5px] leading-relaxed text-neutral-600 clamp-3"><?php echo esc_html( $d[5] ); ?></p>
            <div class="mt-4 pt-3 border-t border-black/5 flex items-center justify-between mt-auto">
              <span class="inline-flex items-center gap-2 text-[12px] font-medium text-neutral-600">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-neutral-200 font-bold text-neutral-600"><?php echo esc_html( mb_substr( $d[6], 0, 1 ) ); ?></span>
                <?php echo esc_html( $d[6] ); ?>
              </span>
              <span class="inline-flex items-center gap-1 text-[12px] font-bold text-[#E30613] bg-[#FDECEC] rounded px-2 py-1">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 11l3 3 7-7 M12 3a9 9 0 100 18 9 9 0 000-18"/></svg>
                <?php echo esc_html( $d[7] ); ?>
              </span>
            </div>
          </article>
        <?php endforeach;
      endif; ?>
    </div>

    <div class="mt-6 text-center">
      <a href="<?php echo esc_url( get_post_type_archive_link( 'propuesta' ) ?: home_url( '/?post_type=propuesta' ) ); ?>" class="inline-flex items-center text-[13px] font-semibold text-[#E30613] border border-[#E30613]/40 rounded-md px-6 py-2.5 hover:bg-[#E30613] hover:text-white transition-colors">Ver todas las propuestas</a>
    </div>
  </div>
</section>

<?php get_footer(); ?>
