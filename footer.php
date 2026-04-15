<footer style="background-color: var(--bg-dark-1);">
 
  <!-- Main Footer -->
  <div class="max-w-7xl mx-auto px-6 py-16">
    <div class="grid md:grid-cols-3 gap-12 mb-12">
 
      <!-- Quick Links (footer-links menu) -->
      <div>
        <h3 class="mb-6 text-lg"
            style="font-family: 'Outfit', sans-serif; font-weight: 600; color: var(--color-primary);">
          <?php esc_html_e( 'Quick Links', 'luxe' ); ?>
        </h3>
        <ul class="space-y-3">
          <?php
            wp_nav_menu([
              'theme_location' => 'footer-links',
              'container'      => false,
              'items_wrap'     => '%3$s',
              'walker'         => new Luxe_Footer_Walker(),
              'fallback_cb'    => 'luxe_footer_links_fallback',
            ]);
          ?>
        </ul>
      </div>
 
      <!-- Help & Support (footer-help menu) -->
      <div>
        <h3 class="mb-6 text-lg"
            style="font-family: 'Outfit', sans-serif; font-weight: 600; color: var(--color-primary);">
          <?php esc_html_e( 'Help & Support', 'luxe' ); ?>
        </h3>
        <ul class="space-y-3">
          <?php
            wp_nav_menu([
              'theme_location' => 'footer-help',
              'container'      => false,
              'items_wrap'     => '%3$s',
              'walker'         => new Luxe_Footer_Walker(),
              'fallback_cb'    => 'luxe_footer_help_fallback',
            ]);
          ?>
        </ul>
      </div>
 
      <!-- Legal (footer-legal menu) -->
      <div>
        <h3 class="mb-6 text-lg"
            style="font-family: 'Outfit', sans-serif; font-weight: 600; color: var(--color-primary);">
          <?php esc_html_e( 'Legal', 'luxe' ); ?>
        </h3>
        <ul class="space-y-3">
          <?php
            wp_nav_menu([
              'theme_location' => 'footer-legal',
              'container'      => false,
              'items_wrap'     => '%3$s',
              'walker'         => new Luxe_Footer_Walker(),
              'fallback_cb'    => 'luxe_footer_legal_fallback',
            ]);
          ?>
        </ul>
      </div>
 
    </div>
  </div><!-- /main footer -->
 
  <!-- Bottom Bar -->
  <div class="border-t" style="border-color: var(--border); background-color: var(--bg-dark-2);">
    <div class="max-w-7xl mx-auto px-6 py-6">
      <div class="flex flex-col md:flex-row justify-between items-center gap-6">
 
        <!-- Copyright -->
        <div class="text-white/50 text-sm">
          &copy; <?php echo esc_html( date('Y') ); ?>
          <?php bloginfo('name'); ?>.
          <?php esc_html_e( 'All rights reserved.', 'luxe' ); ?>
        </div>
 
        <!-- Social Icons -->
        <div class="flex items-center gap-4">
 
          <?php
            $social_links = [
              'twitter'   => [ 'label' => 'Twitter',   'icon' => '&#120143;' ], // 𝕏
              'linkedin'  => [ 'label' => 'LinkedIn',  'icon' => 'in' ],
              'github'    => [ 'label' => 'GitHub',    'icon' => 'GH' ],
              'instagram' => [ 'label' => 'Instagram', 'icon' => 'IG' ],
            ];
 
            foreach ( $social_links as $key => $social ) :
              $url = get_theme_mod( 'luxe_social_' . $key, '#' );
              if ( ! $url ) continue;
          ?>
            <a href="<?php echo esc_url( $url ); ?>"
               class="w-10 h-10 rounded-lg flex items-center justify-center transition-all hover:scale-110 luxe-social-icon"
               title="<?php echo esc_attr( $social['label'] ); ?>"
               target="_blank"
               rel="noopener noreferrer"
               style="background-color: var(--bg-dark-4); border: 1px solid var(--border); color: white;">
              <span class="text-sm font-bold"><?php echo $social['icon']; ?></span>
            </a>
          <?php endforeach; ?>
 
        </div><!-- /social -->
 
      </div>
    </div>
  </div><!-- /bottom bar -->
 
</footer>
 
<?php wp_footer(); ?>
</body>
</html>