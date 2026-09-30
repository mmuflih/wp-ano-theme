<?php get_header(); if(have_posts()):while(have_posts()):the_post(); ?>
<main class="single-wrap"><h1 class="single-title"><?php the_title(); ?></h1><div class="single-content"><?php the_content(); ?></div></main>
<?php endwhile;endif; get_footer(); ?>
