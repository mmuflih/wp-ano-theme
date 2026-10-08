<?php get_header(); ?>
<?php
$hero_query = new WP_Query(array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 3,
    'orderby'             => 'date',
    'order'               => 'DESC',
    'ignore_sticky_posts' => true,
));
$hero_posts = $hero_query->posts;
$hero_fallback = get_template_directory_uri() . '/assets/images/hero.jpg';
?>

<section class="hero-slider" aria-label="Artikel terbaru">
    <div class="hero-slides">
        <?php if ($hero_posts) : ?>
            <?php foreach ($hero_posts as $index => $hero_post) :
                $categories = get_the_category($hero_post->ID);
                $category = !empty($categories) ? $categories[0]->name : 'Artikel Terbaru';
                $content = get_post_field('post_content', $hero_post->ID);
                $word_count = str_word_count(wp_strip_all_tags($content));
                $reading_time = max(1, (int) ceil($word_count / 200));
                $background = $hero_fallback;
            ?>
                <article class="hero-slide<?php echo $index === 0 ? ' is-active' : ''; ?>" style="--hero-image:url('<?php echo esc_url($background); ?>');" aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>">
                    <div class="hero-overlay"></div>
                    <div class="container hero-inner">
                        <div class="hero-content">
                            <div class="eyebrow"><?php echo esc_html($category); ?></div>
                            <h1><?php echo esc_html(get_the_title($hero_post)); ?></h1>
                            <p class="hero-excerpt"><?php echo esc_html(ano_excerpt(get_the_excerpt($hero_post), 190)); ?></p>
                            <div class="hero-meta">
                                <span aria-label="Tanggal artikel">▣ <?php echo esc_html(get_the_date('j F Y', $hero_post)); ?></span>
                                <span aria-label="Kategori artikel">▱ <?php echo esc_html($category); ?></span>
                                <span aria-label="Waktu baca">◷ <?php echo esc_html($reading_time); ?> menit baca</span>
                            </div>
                            <a class="btn" href="<?php echo esc_url(get_permalink($hero_post)); ?>">Baca Selengkapnya →</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else : ?>
            <article class="hero-slide is-active" style="--hero-image:url('<?php echo esc_url($hero_fallback); ?>');" aria-hidden="false">
                <div class="hero-overlay"></div>
                <div class="container hero-inner">
                    <div class="hero-content">
                        <div class="eyebrow">Artikel Terbaru</div>
                        <h1>Belum ada artikel</h1>
                        <p class="hero-excerpt">Buat minimal satu artikel WordPress. Hero slider akan otomatis menampilkan tiga artikel terbaru berdasarkan tanggal publikasi.</p>
                        <a class="btn" href="<?php echo esc_url(admin_url('post-new.php')); ?>">Tulis Artikel →</a>
                    </div>
                </div>
            </article>
        <?php endif; ?>
    </div>

    <?php if (count($hero_posts) > 1) : ?>
        <div class="hero-dots" aria-label="Navigasi artikel slider">
            <?php foreach ($hero_posts as $index => $hero_post) : ?>
                <button class="hero-dot<?php echo $index === 0 ? ' is-active' : ''; ?>" type="button" data-slide="<?php echo esc_attr($index); ?>" aria-label="Tampilkan artikel <?php echo esc_attr($index + 1); ?>"></button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="section article-section" id="artikel">
    <div class="container">
        <div class="section-head">
            <div>
                <h2 class="section-title">Artikel</h2>
                <p class="section-sub">Tulisan, gagasan, dan catatan terbaru.</p>
            </div><a class="section-link" href="<?php echo esc_url(ano_posts_archive_url()); ?>">Lihat Semua Artikel</a>
        </div>
        <div class="article-grid"><?php
                                    $articles = get_posts(array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 4, 'orderby' => 'date', 'order' => 'DESC', 'ignore_sticky_posts' => true));
                                    foreach ($articles as $p): $img = ano_thumb_url($p, 'medium_large');
                                        $categories = get_the_category($p->ID);
                                        $category = !empty($categories) ? $categories[0]->name : 'Artikel';
                                    ?>
                <article class="article-card">
                    <a class="article-thumb" href="<?php echo esc_url(get_permalink($p)); ?>"><?php if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title($p)); ?>"><?php else: ?><span aria-hidden="true"></span><?php endif; ?></a>
                    <div class="article-body">
                        <div class="article-meta"><?php echo esc_html($category); ?> · <?php echo esc_html(get_the_date('j F Y', $p)); ?></div>
                        <h3 class="article-title"><a href="<?php echo esc_url(get_permalink($p)); ?>"><?php echo esc_html(get_the_title($p)); ?></a></h3>
                        <p class="article-excerpt"><?php echo esc_html(ano_excerpt(get_the_excerpt($p), 120)); ?></p><a class="text-link" href="<?php echo esc_url(get_permalink($p)); ?>">Baca Artikel →</a>
                    </div>
                </article>
            <?php endforeach;
                                    if (!$articles): ?><p>Belum ada artikel. Tambahkan tulisan melalui menu Pos di WordPress.</p><?php endif; ?>
        </div>
    </div>
</section>

<section class="section" id="tulisan">
    <div class="container">
        <div class="section-head">
            <div>
                <h2 class="section-title">Bibliografi Buku</h2>
                <p class="section-sub">Kumpulan novel saya tentang sejarah, manusia, dan dunia yang kita hidupi.</p>
            </div><a class="section-link" href="<?php echo esc_url(get_post_type_archive_link('ano_book')); ?>">Lihat Semua Buku</a>
        </div>
        <div class="content-card-grid"><?php
                                        $books = get_posts(array('post_type' => 'ano_book', 'posts_per_page' => 4, 'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC')));
                                        if (!$books) {
                                            $books = array();
                                        }
                                        foreach ($books as $p): $img = ano_thumb_url($p, 'medium_large'); ?>
                <article class="content-card content-card-book">
                    <a class="content-card-book-thumb" href="<?php echo esc_url(get_permalink($p)); ?>"><?php if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title($p)); ?>"><?php else:
                                                                                                                                                                                                                    $book_title = get_the_title($p);
                                                                                                                                                                                                                    $book_file = (strpos($book_title, 'Jalur Wangi') !== false) ? 'waling-jalur-wangi.svg' : ((strpos($book_title, 'Altar') !== false) ? 'waling-altar.svg' : ((strpos($book_title, 'ARGYRE') !== false) ? 'argyre.svg' : 'waling-jalur-angin.svg'));
                                                                                                                                                                                                                    ?><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/' . $book_file); ?>" alt=""><?php endif; ?></a>
                    <div class="content-card-body">
                        <div class="article-meta">Buku</div>
                        <h3 class="content-card-title"><a href="<?php echo esc_url(get_permalink($p)); ?>"><?php echo esc_html(get_the_title($p)); ?></a></h3>
                        <p class="content-card-excerpt"><?php echo esc_html(ano_excerpt(get_the_excerpt($p), 95)); ?></p><a class="text-link" href="<?php echo esc_url(get_permalink($p)); ?>">Lihat Detail →</a>
                    </div>
                </article>
            <?php endforeach;
                                        if (!$books): ?><p>Belum ada buku. Tambahkan konten melalui Konten ANO → Bibliografi Buku.</p><?php endif; ?>
        </div>
    </div>
</section>

<section class="section" id="usaha">
    <div class="container">
        <div class="section-head">
            <div>
                <h2 class="section-title">Usaha</h2>
                <p class="section-sub">Lini usaha dan layanan yang saya jalankan.</p>
            </div><a class="section-link" href="<?php echo esc_url(get_post_type_archive_link('ano_business')); ?>">Lihat Semua Usaha</a>
        </div>
        <div class="content-card-grid"><?php $items = get_posts(array('post_type' => 'ano_business', 'posts_per_page' => 4, 'orderby' => array('menu_order' => 'ASC', 'date' => 'ASC')));
                                        foreach ($items as $i => $p): $img = ano_thumb_url($p, 'medium_large'); ?>
                <article class="content-card content-card-business">
                    <a class="content-card-thumb content-card-thumb-contain" href="<?php echo esc_url(get_post_meta($p->ID, 'ano_business_url', true) ?: get_permalink($p)); ?>" target="_blank" rel="noopener noreferrer"><?php if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title($p)); ?>"><?php else: ?><span aria-hidden="true"></span><?php endif; ?></a>
                    <div class="content-card-body">
                        <div class="article-meta">Usaha</div>
                        <p class="content-card-excerpt"><?php echo esc_html(ano_excerpt(get_the_excerpt($p), 90)); ?></p><a class="text-link" href="<?php echo esc_url(get_post_meta($p->ID, 'ano_business_url', true) ?: get_permalink($p)); ?>" target="_blank" rel="noopener noreferrer">Kunjungi Website →</a>
                    </div>
                </article><?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" id="inisiatif">
    <div class="container">
        <div class="section-head">
            <div>
                <h2 class="section-title">Inisiatif</h2>
                <p class="section-sub">Pendidikan, sosial, dan gerakan kolaboratif untuk dampak yang lebih luas.</p>
            </div><a class="section-link" href="<?php echo esc_url(get_post_type_archive_link('ano_initiative')); ?>">Lihat Semua Inisiatif</a>
        </div>
        <div class="content-card-grid"><?php $items = get_posts(array('post_type' => 'ano_initiative', 'posts_per_page' => 4, 'orderby' => array('menu_order' => 'ASC', 'date' => 'ASC')));
                                        foreach ($items as $p): $img = ano_thumb_url($p, 'medium_large'); ?>
                <article class="content-card content-card-initiative">
                    <a class="content-card-thumb content-card-thumb-contain" href="<?php echo esc_url(get_post_meta($p->ID, 'ano_initiative_url', true) ?: get_permalink($p)); ?>" target="_blank" rel="noopener noreferrer"><?php if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title($p)); ?>"><?php else: ?><span aria-hidden="true"></span><?php endif; ?></a>
                    <div class="content-card-body">
                        <div class="article-meta">Inisiatif</div>
                        <p class="content-card-excerpt"><?php echo esc_html(ano_excerpt(get_the_excerpt($p), 85)); ?></p><a class="text-link" href="<?php echo esc_url(get_post_meta($p->ID, 'ano_initiative_url', true) ?: get_permalink($p)); ?>" target="_blank" rel="noopener noreferrer">Lihat Detail →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php get_footer(); ?>