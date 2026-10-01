<?php
/**
 * Template Name: Resultats (PSOE)
 * Description: Plantilla dedicada solo para la página de resultados de la enquesta:
 * hero propio, contador de respuestas y tarjeta "Resultats en directe" con el
 * shortcode [resultados_encuesta_chart]. El formulario se configura en el lateral
 * del editor ("Enquesta (plantilla)").
 */
get_header();
while ( have_posts() ) : the_post();
  $post_id          = get_the_ID();
  $results_form_id  = (int) get_post_meta( $post_id, '_psoe_enquesta_form_id', true );
  $results_form_id  = $results_form_id > 0 ? $results_form_id : 2;
  $results_tipo     = get_post_meta( $post_id, '_psoe_enquesta_tipo', true ) ?: 'doughnut';
  $results_columnas = (int) get_post_meta( $post_id, '_psoe_enquesta_columnas', true );
  $results_columnas = $results_columnas >= 1 ? min( $results_columnas, 6 ) : 1;
  $subtitle         = has_excerpt() ? get_the_excerpt() : '';

  $total_respostes = null;
  if ( class_exists( 'GFAPI' ) ) {
    $count = GFAPI::count_entries( $results_form_id, array( 'status' => 'active' ) );
    if ( ! is_wp_error( $count ) ) {
      $total_respostes = (int) $count;
    }
  }
?>
<section class="bg-[#FBF7F6]">
  <div class="max-w-[1200px] mx-auto px-4 sm:px-6 pt-10 pb-16">

    <div class="max-w-[700px]">
      <span class="inline-flex items-center gap-1.5 text-[11px] font-bold tracking-[0.14em] uppercase text-[#D50024] bg-[#FDECEC] border border-[#E30613]/15 rounded-full px-3 py-1">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
          <path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>
        </svg>
        <?php esc_html_e( 'Participa · Resultats', 'psoe-participatiu' ); ?>
      </span>
      <h1 class="mt-3 text-[30px] sm:text-[38px] font-extrabold tracking-tight leading-[1.1] text-[#141414]"><?php the_title(); ?></h1>
      <?php if ( $subtitle ) : ?>
        <p class="psoe-hero-sub mt-3"><?php echo esc_html( $subtitle ); ?></p>
      <?php endif; ?>
      <?php if ( $total_respostes !== null ) : ?>
        <p class="mt-3 inline-flex items-center gap-1.5 text-[13px] font-semibold text-[#7A030B] bg-white border border-[#F0E4E4] rounded-full px-3 py-1 shadow-sm">
          <span class="inline-block w-2 h-2 rounded-full bg-[#E30613] animate-pulse" aria-hidden="true"></span>
          <?php
          printf(
            /* translators: %s: número de respuestas */
            esc_html( _n( '%s resposta', '%s respostes', $total_respostes, 'psoe-participatiu' ) ),
            esc_html( number_format_i18n( $total_respostes ) )
          );
          ?>
        </p>
      <?php endif; ?>
    </div>

    <?php if ( has_post_thumbnail() ) : ?>
      <div class="mt-6 rounded-[14px] overflow-hidden shadow-card"><?php the_post_thumbnail( 'large', [ 'class' => 'w-full' ] ); ?></div>
    <?php endif; ?>

    <div class="psoe-card mt-8" id="resultats">
      <div class="psoe-card-head">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D50024" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
          <path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>
        </svg>
        <span><?php esc_html_e( 'Resultats en directe', 'psoe-participatiu' ); ?></span>
      </div>
      <div class="psoe-card-body">
        <?php
        echo do_shortcode( sprintf(
          '[resultados_encuesta_chart form_id="%d" tipo="%s" columnas="%d"]',
          $results_form_id,
          esc_attr( $results_tipo ),
          $results_columnas
        ) );
        ?>
      </div>
    </div>

  </div>
</section>
<?php endwhile; get_footer(); ?>
