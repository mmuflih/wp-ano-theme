<?php
get_header();
if (have_posts()) :
?>
<main class="section"><div class="container">
<?php while (have_posts()) : the_post(); ?>
<article class="post-card" style="margin-bottom:20px">
<?php if (has_post_thumbnail()) : ?><div class="post-thumb"><?php the_post_thumbnail('large'); ?></div><?php endif; ?>
<div class="post-body"><h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><div class="post-excerpt"><?php the_excerpt(); ?></div></div>
</article>
<?php endwhile; ?>
</div></main>
<?php else : ?>
<main class="section"><div class="container"><h1 class="page-title"><?php esc_html_e('Belum ada konten.', 'ano'); ?></h1></div></main>
<?php endif;
get_footer();
