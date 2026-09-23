</main><!-- /#content -->

<?php if ( is_page() ) : ?>
  <!-- Footer claro para páginas (estilo Image 1) -->
  <footer class="bg-[#FAFAFA] border-t border-[#EFE5E5] mt-16">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8 text-center">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center gap-2" aria-label="PSOE inicio">
        <svg width="22" height="22" viewBox="0 0 32 32" fill="none" stroke="#E30613" stroke-width="4.5" stroke-linecap="round" aria-hidden="true">
          <path d="M16 4v24M4 16h24M7.5 7.5l17 17M24.5 7.5l-17 17"/>
        </svg>
        <span class="font-extrabold tracking-tight text-[18px] text-[#5B3A40]">PSOE</span>
      </a>
      <nav class="mt-3 flex items-center justify-center gap-6 text-[14px] text-[#A0767E]" aria-label="Legal">
        <a class="hover:text-[#E30613]" href="#">Aviso Legal</a>
        <a class="hover:text-[#E30613]" href="#">Política de Cookies</a>
        <a class="hover:text-[#E30613]" href="#">Contacto</a>
      </nav>
      <p class="mt-3 text-[12px] text-[#C9A9B0]">© 2024 Partido Socialista Obrero Español. Todos los derechos reservados.</p>
    </div>
  </footer>
<?php else : ?>
<!-- Newsletter -->
<section class="mt-10 bg-[#241114] text-white">
  <div class="max-w-[1120px] mx-auto px-4 sm:px-6 py-12 text-center">
    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-[#E30613]/15 text-[#FF6B75] mb-3">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
    </span>
    <h2 class="text-2xl font-extrabold tracking-tight"><?php echo esc_html( get_theme_mod( 'psoe_news_title', 'Mantente informado' ) ); ?></h2>
    <p class="mt-2 text-[13px] text-white/70 max-w-[520px] mx-auto"><?php echo esc_html( get_theme_mod( 'psoe_news_sub', 'Recibe actualizaciones sobre el proceso de elaboración del programa y las propuestas más votadas directamente en tu correo.' ) ); ?></p>
    <form id="newsForm" class="mt-5 flex flex-col sm:flex-row gap-2 max-w-[420px] mx-auto" onsubmit="return false;">
      <input type="email" required placeholder="<?php esc_attr_e( 'Tu correo electrónico', 'psoe-participatiu' ); ?>" class="flex-1 text-sm bg-white/10 border border-white/20 rounded-md px-4 py-2.5 text-white placeholder:text-white/50 outline-none focus:border-white/50">
      <button class="text-[13px] font-semibold bg-[#E30613] hover:bg-[#B0050F] rounded-md px-5 py-2.5 transition-colors"><?php esc_html_e( 'Suscribirse', 'psoe-participatiu' ); ?></button>
    </form>
    <p id="newsMsg" class="hidden mt-3 text-[13px] text-emerald-300">¡Gracias! Revisa tu bandeja para confirmar.</p>
    <p class="mt-3 text-[11px] text-white/40">Al suscribirte aceptas nuestra <a href="#" class="underline hover:text-white/70">Política de Privacidad</a>.</p>
  </div>

  <!-- Footer -->
  <footer class="bg-[#1B0C0E] border-t border-white/10">
    <div class="max-w-[1120px] mx-auto px-4 sm:px-6 py-10 grid grid-cols-2 md:grid-cols-4 gap-8 text-left">
      <div class="col-span-2 md:col-span-1">
        <div class="flex items-center gap-2">
          <span class="inline-flex items-center justify-center w-6 h-6 rounded bg-[#E30613] text-white">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 4.9 5.4.8-3.9 3.8.9 5.4-4.8-2.5-4.8 2.5.9-5.4L4.2 7.7l5.4-.8L12 2z"/></svg>
          </span>
          <span class="font-extrabold text-[14px]">PSOE 2027</span>
        </div>
        <p class="mt-3 text-[12px] leading-relaxed text-white/55">Plataforma oficial de participación ciudadana del Partido Socialista Obrero Español para la elaboración del Programa Electoral 2027.</p>
        <div class="mt-3 flex items-center gap-3 text-white/60">
          <a href="#" aria-label="Web" class="hover:text-white"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c3 3.5 3 14 0 18M12 3c-3 3.5-3 14 0 18"/></svg></a>
          <a href="#" aria-label="X" class="hover:text-white"><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h3l-6.5 7.5L22 22h-6l-4.7-6.1L5.8 22H2.8l7-8.1L2 2h6.2l4.2 5.6L18 2zm-1.1 18h1.7L7.4 3.9H5.6L16.9 20z"/></svg></a>
          <a href="#" aria-label="RSS" class="hover:text-white"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 11a9 9 0 019 9M4 4a16 16 0 0116 16"/><circle cx="5" cy="19" r="1.5" fill="currentColor"/></svg></a>
        </div>
      </div>
      <nav aria-label="Participa">
        <h3 class="text-[13px] font-bold mb-3">Participa</h3>
        <ul class="space-y-2 text-[12px] text-white/55">
          <li><a class="hover:text-white" href="#">Enviar Propuesta</a></li>
          <li><a class="hover:text-white" href="#">Debates Abiertos</a></li>
          <li><a class="hover:text-white" href="#">Votaciones Activas</a></li>
          <li><a class="hover:text-white" href="#">Resultados Previos</a></li>
        </ul>
      </nav>
      <nav aria-label="El Partido">
        <h3 class="text-[13px] font-bold mb-3">El Partido</h3>
        <ul class="space-y-2 text-[12px] text-white/55">
          <li><a class="hover:text-white" href="#">Sobre Nosotros</a></li>
          <li><a class="hover:text-white" href="#">Transparencia</a></li>
          <li><a class="hover:text-white" href="#">Sedes</a></li>
          <li><a class="hover:text-white" href="#">Afíliate</a></li>
        </ul>
      </nav>
      <nav aria-label="Legal">
        <h3 class="text-[13px] font-bold mb-3">Legal</h3>
        <ul class="space-y-2 text-[12px] text-white/55">
          <li><a class="hover:text-white" href="#">Aviso Legal</a></li>
          <li><a class="hover:text-white" href="#">Política de Privacidad</a></li>
          <li><a class="hover:text-white" href="#">Política de Cookies</a></li>
          <li><a class="hover:text-white" href="#">Contacto</a></li>
        </ul>
      </nav>
    </div>
    <div class="border-t border-white/10">
      <div class="max-w-[1120px] mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-white/40">
        <p>© 2024 PSOE. Todos los derechos reservados.</p>
        <p class="inline-flex items-center gap-1">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 018 0v3"/></svg>
          Sitio seguro y encriptado
        </p>
      </div>
    </div>
  </footer>
</section>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
