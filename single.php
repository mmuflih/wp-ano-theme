<?php get_header(); if(have_posts()):while(have_posts()):the_post();
$pt = get_post_type();
$is_ano = in_array($pt, array('ano_book','ano_business','ano_initiative'), true);
$has_content = trim(wp_strip_all_tags(get_the_content())) !== '';
$excerpt = has_excerpt() ? get_the_excerpt() : '';
$subtitle = $pt === 'ano_book' ? get_post_meta(get_the_ID(),'ano_book_subtitle',true) : '';
$author = $pt === 'ano_book' ? get_post_meta(get_the_ID(),'ano_book_author',true) : '';
$ext_url = '';
if ($pt === 'ano_business') $ext_url = get_post_meta(get_the_ID(),'ano_business_url',true);
if ($pt === 'ano_initiative') $ext_url = get_post_meta(get_the_ID(),'ano_initiative_url',true);
?>
<main class="single-wrap"><div class="eyebrow"><?php echo esc_html(get_post_type_object($pt)->labels->singular_name); ?></div>
<h1 class="single-title"><?php the_title(); ?></h1>
<?php if($subtitle || $author): ?><p class="section-sub"><?php echo esc_html(trim($subtitle . ($subtitle && $author ? ' · ' : '') . $author)); ?></p><?php endif; ?>
<?php if(!$is_ano): ?><p class="section-sub"><?php echo esc_html(get_the_date('j F Y')); ?></p><?php endif; ?>
<?php
if (has_post_thumbnail()) {
    the_post_thumbnail('full', array('class' => 'single-image'));
} elseif ($is_ano || $pt === 'post') {
    $fallback_thumb = ano_thumb_url(get_the_ID(), 'full');
    if ($fallback_thumb) echo '<img class="single-image" src="' . esc_url($fallback_thumb) . '" alt="' . esc_attr(get_the_title()) . '">';
}
?>
<div class="single-content">
<?php
if ($has_content) {
    the_content();
} elseif ($is_ano && $excerpt !== '') {
    echo wpautop(esc_html($excerpt));
}
?>
</div>
<?php if($ext_url): ?><p><a class="btn" href="<?php echo esc_url($ext_url); ?>" target="_blank" rel="noopener noreferrer">Kunjungi Website →</a></p><?php endif; ?>
</main>
<?php endwhile;endif; get_footer(); ?>
