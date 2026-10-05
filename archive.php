<?php get_header(); ?>
<main class="container page-header">
<?php if (!is_post_type_archive(array('ano_business', 'ano_initiative'))) : ?>
<h1 class="page-title"><?php the_archive_title(); ?></h1>
<?php endif; ?>
<p class="section-sub"><?php the_archive_description(); ?></p>
</main>
<div class="container section"><div class="archive-grid"><?php if(have_posts()):while(have_posts()):the_post(); ?>
<article class="post-card"><div class="post-thumb"><?php $ano_t=ano_thumb_url(get_the_ID(),'medium_large'); if($ano_t): ?><img src="<?php echo esc_url($ano_t); ?>" alt="<?php echo esc_attr(get_the_title()); ?>"><?php endif; ?></div><div class="post-body"><h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p class="post-excerpt"><?php echo esc_html(ano_excerpt(get_the_excerpt(),150)); ?></p><a class="text-link" href="<?php the_permalink(); ?>">Lihat Detail →</a></div></article>
<?php endwhile;else: ?><p>Belum ada konten.</p><?php endif; ?></div>
<?php the_posts_pagination(array('mid_size'=>1,'prev_text'=>'←','next_text'=>'→')); ?></div>
<?php get_footer(); ?>
