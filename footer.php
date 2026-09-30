<footer class="site-footer" id="kontak">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="brand-mark">ANO.</div>
                <div class="footer-copy">Aroma · Science · History · Field · Writing<br><br>Menulis, belajar, bekerja, dan berkontribusi untuk hal-hal yang lebih baik.</div>
            </div>
            <div class="footer-nav"><?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'fallback_cb' => 'ano_menu_fallback')); ?></div>
            <div class="footer-quote">“Pengetahuan, seperti aroma, hanya bermakna jika dibagikan.”<br><br>— Ano</div>
        </div>
        <div class="footer-bottom"><span>© <?php echo esc_html(date('Y')); ?> anoweb.id &nbsp; &nbsp; &nbsp;</span><span>Kebijakan Privasi &nbsp;&nbsp; Syarat Penggunaan</span></div>
    </div>
</footer><?php wp_footer(); ?></body>

</html>