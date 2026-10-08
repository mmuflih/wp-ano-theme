<?php get_header(); ?>
<main class="container page-header">
    <?php if (get_post_type() === 'ano_book') : ?>
        <div>
            <h2 class="section-title">Bibliografi Buku</h2>
            <p class="section-sub">Kumpulan novel saya tentang sejarah, manusia, dan dunia yang kita hidupi.</p>
        </div>
    <?php elseif (get_post_type() === 'ano_initiative') : ?>
        <div>
            <h2 class="section-title">Inisiatif</h2>
            <p class="section-sub">Kumpulan inisiatif saya tentang sejarah, manusia, dan dunia yang kita hidupi.</p>
        </div>
    <?php elseif (get_post_type() === 'ano_business') : ?>
        <div>
            <h2 class="section-title">Bisnis</h2>
            <p class="section-sub">Kumpulan bisnis saya tentang sejarah, manusia, dan dunia yang kita hidupi.</p>
        </div>
    <?php endif; ?>
    <p class="section-sub"><?php the_archive_description(); ?></p>
</main>
<div class="container section">
    <div class="archive-grid"><?php if (have_posts()): while (have_posts()): the_post(); ?>
                <article class="post-card">
                    <div class="<?php echo get_post_type() === 'ano_book' ? ' post-thumb-book' : 'post-thumb'; ?>"><?php $ano_t = ano_thumb_url(get_the_ID(), 'medium_large');
                                                                                                                    if ($ano_t): ?><img src="<?php echo esc_url($ano_t); ?>" alt="<?php echo esc_attr(get_the_title()); ?>"><?php endif; ?></div>
                    <div class="post-body">
                        <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p class="post-excerpt"><?php echo esc_html(ano_excerpt(get_the_excerpt(), 150)); ?></p><a class="text-link" href="<?php the_permalink(); ?>">Lihat Detail →</a>
                    </div>
                </article>
            <?php endwhile;
                                else: ?><p>Belum ada konten.</p><?php endif; ?>
    </div>
    <?php the_posts_pagination(array('mid_size' => 1, 'prev_text' => '←', 'next_text' => '→')); ?>
</div>
<?php get_footer(); ?>