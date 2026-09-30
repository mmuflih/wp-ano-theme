<?php get_header(); ?>
<main class="container page-header"><h1 class="page-title"><?php the_archive_title(); ?></h1><p class="section-sub"><?php the_archive_description(); ?></p></main>
<div class="container section"><div class="archive-grid"><?php if(have_posts()):while(have_posts()):the_post(); ?>
<article class="post-card"><div class="post-thumb"><?php if(has_post_thumbnail()) the_post_thumbnail('medium_large'); ?></div><div class="post-body"><h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p class="post-excerpt"><?php echo esc_html(ano_excerpt(get_the_excerpt(),150)); ?></p><a class="text-link" href="<?php the_permalink(); ?>">Lihat Detail →</a></div></article>
<?php endwhile;else: ?><p>Belum ada konten.</p><?php endif; ?></div></div>
<?php get_footer(); ?>
