<?php if (!defined('ABSPATH')) exit; ?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?>
</head>

<body <?php body_class(); ?>><?php wp_body_open(); ?>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><span class="brand-mark">ANO.</span><span class="brand-copy"><small>Aroma · Science · History · Field · Writing</small></span></a>
            <button class="mobile-toggle" aria-label="Buka menu">☰</button>
            <nav class="main-nav"><?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'fallback_cb' => 'ano_menu_fallback')); ?></nav>
            <button class="search-link" type="button" aria-label="Buka pencarian" aria-expanded="false" aria-controls="ano-search-panel">⌕</button>
        </div>
    </header>
    <div class="search-panel" id="ano-search-panel" aria-hidden="true">
        <div class="search-panel-inner container">
            <form class="search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <label class="screen-reader-text" for="ano-search-input">Cari di website</label>
                <input id="ano-search-input" name="s" type="search" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="Cari artikel, buku, usaha, inisiatif..." autocomplete="off">
                <button type="submit" aria-label="Cari">Cari</button>
                <button class="search-close" type="button" aria-label="Tutup pencarian">×</button>
            </form>
        </div>
    </div>
    <?php function ano_menu_fallback()
    {
        echo '<ul><li><a href="' . esc_url(home_url('/')) . '">Beranda</a></li><li><a href="#tentang">Tentang</a></li><li><a href="#tulisan">Tulisan</a></li><li><a href="#riset">Riset</a></li><li><a href="#usaha">Usaha</a></li><li><a href="#inisiatif">Inisiatif</a></li><li><a href="#kontak">Kontak</a></li></ul>';
    } ?>