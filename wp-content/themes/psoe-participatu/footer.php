</main><!-- /#content -->

<?php if ( is_page() ) : ?>
  <!-- Footer claro para páginas (estilo Image 1) -->
  <footer class="bg-[#FAFAFA] border-t border-[#EFE5E5] mt-16">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8 text-center">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center gap-2" aria-label="PSOE inicio">
        <svg width="22" height="22" viewBox="0 0 32 32" fill="#E30613" aria-hidden="true">
          <path d="M16 28C16 28 3 19.5 3 11.5C3 7.4 6.1 4.5 9.5 4.5C12.2 4.5 14.6 6.1 16 8.4C17.4 6.1 19.8 4.5 22.5 4.5C25.9 4.5 29 7.4 29 11.5C29 19.5 16 28 16 28Z"/>
        </svg>
        <span class="font-extrabold tracking-tight text-[18px] text-[#5B3A40]">VINARÒS</span>
      </a>
      <nav class="mt-3 flex items-center justify-center gap-6 text-[14px] text-[#A0767E]" aria-label="Legal">
        <a class="hover:text-[#E30613]" href="#">Aviso Legal</a>
        <a class="hover:text-[#E30613]" href="#">Política de Cookies</a>
        <a class="hover:text-[#E30613]" href="#">Contacto</a>
      </nav>
      <p class="mt-3 text-[12px] text-[#C9A9B0]">© <?php echo date('Y'); ?> PSPV-Vinaròs. Tots els drets reservats.</p>
    </div>
  </footer>
<?php else : ?>

<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
