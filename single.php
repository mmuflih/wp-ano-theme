<?php get_header(); if(have_posts()):while(have_posts()):the_post(); ?>
<main class="single-wrap"><div class="eyebrow"><?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name); ?></div>
<h1 class="single-title"><?php the_title(); ?></h1><p class="section-sub"><?php echo esc_html(get_the_date('j F Y')); ?></p>
<?php if(has_post_thumbnail()) the_post_thumbnail('large'); ?><div class="single-content"><?php the_content(); ?></div></main>
<?php endwhile;endif; get_footer(); ?>
