<?php if (!defined('ABSPATH')) exit; ?>
<!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<header class="site-header"><div class="container header-inner">
<a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><span class="brand-mark">ANO.</span><span class="brand-copy">Marsiano Dirgantara<small>Aroma · Science · History · Field · Writing</small></span></a>
<button class="mobile-toggle" aria-label="Buka menu">☰</button>
<nav class="main-nav"><?php wp_nav_menu(array('theme_location'=>'primary','container'=>false,'fallback_cb'=>'ano_menu_fallback')); ?></nav>
<a class="search-link" href="<?php echo esc_url(home_url('/?s=')); ?>" aria-label="Cari">⌕</a>
</div></header>
<?php function ano_menu_fallback(){echo '<ul><li><a href="'.esc_url(home_url('/')).'">Beranda</a></li><li><a href="#tentang">Tentang</a></li><li><a href="#tulisan">Tulisan</a></li><li><a href="#riset">Riset</a></li><li><a href="#usaha">Usaha</a></li><li><a href="#inisiatif">Inisiatif</a></li><li><a href="#kontak">Kontak</a></li></ul>';} ?>
