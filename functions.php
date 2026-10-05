<?php
if (!defined('ABSPATH')) exit;

define('ANO_VERSION', '1.4.20');

/**
 * ANO Theme diagnostics / logging.
 * Logs are passed to PHP's error_log(), which WordPress can route to
 * wp-content/debug.log when WP_DEBUG_LOG is enabled. A small theme-specific
 * log is also maintained for the dashboard diagnostics screen.
 */
function ano_log($message, $context = array(), $level = 'INFO')
{
    if (!is_string($message)) {
        $message = wp_json_encode($message);
    }

    $line = '[' . gmdate('Y-m-d H:i:s') . ' UTC] [ANO] [' . strtoupper($level) . '] ' . $message;
    if (!empty($context)) {
        $encoded = wp_json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($encoded !== false) {
            $line .= ' | ' . $encoded;
        }
    }

    // WordPress/PHP logging. This is the preferred location when WP_DEBUG_LOG is enabled.
    error_log($line);

    // Theme-specific log for easier diagnosis from the WordPress dashboard.
    $log_file = trailingslashit(WP_CONTENT_DIR) . 'ano-debug.php';
    if (!file_exists($log_file)) {
        // The PHP wrapper prevents the raw log from being downloaded/executed as text
        // when wp-content is directly browsable.
        @file_put_contents($log_file, "<?php exit; /* ANO DEBUG LOG */ ?>\n", LOCK_EX);
    }
    $result = @file_put_contents($log_file, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    if ($result === false && defined('WP_DEBUG') && WP_DEBUG) {
        // Do not throw from a logger; report the failure to the normal PHP log only.
        error_log('[ANO] Unable to write theme log file: ' . $log_file);
    }
}

function ano_log_error($message, $context = array())
{
    ano_log($message, $context, 'ERROR');
}

function ano_log_warning($message, $context = array())
{
    ano_log($message, $context, 'WARNING');
}

function ano_log_exception($exception, $context = array())
{
    if ($exception instanceof Throwable) {
        $context['file'] = $exception->getFile();
        $context['line'] = $exception->getLine();
        $context['trace'] = $exception->getTraceAsString();
        ano_log_error($exception->getMessage(), $context);
    } else {
        ano_log_error((string) $exception, $context);
    }
}

function ano_log_shutdown_error()
{
    $error = error_get_last();
    if (!$error) {
        return;
    }

    $fatal_types = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
    if (in_array($error['type'], $fatal_types, true)) {
        ano_log_error('Fatal PHP error detected.', array(
            'type' => $error['type'],
            'message' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line'],
            'request_uri' => isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '',
        ));
    }
}
register_shutdown_function('ano_log_shutdown_error');

function ano_log_wpdb_error()
{
    global $wpdb;
    if (!empty($wpdb->last_error)) {
        ano_log_error('WordPress database error.', array(
            'error' => $wpdb->last_error,
            'query' => $wpdb->last_query,
        ));
    }
}
add_action('shutdown', 'ano_log_wpdb_error', 999);

function ano_log_theme_activation()
{
    ano_log('Theme activated.', array(
        'wordpress' => get_bloginfo('version'),
        'php' => PHP_VERSION,
        'theme' => wp_get_theme()->get('Version'),
    ));
}
add_action('after_switch_theme', 'ano_log_theme_activation');

function ano_log_theme_deactivation()
{
    ano_log('Theme deactivated.');
}
add_action('switch_theme', 'ano_log_theme_deactivation');

function ano_debug_admin_menu()
{
    add_theme_page('Log ANO', 'Log ANO', 'manage_options', 'ano-log', 'ano_debug_admin_page');
}
add_action('admin_menu', 'ano_debug_admin_menu');

function ano_debug_admin_page()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Anda tidak memiliki izin untuk melihat log.', 'ano'));
    }

    $log_file = trailingslashit(WP_CONTENT_DIR) . 'ano-debug.php';

    if (isset($_POST['ano_clear_log']) && check_admin_referer('ano_clear_log_action')) {
        @file_put_contents($log_file, "<?php exit; /* ANO DEBUG LOG */ ?>\n", LOCK_EX);
        ano_log('Log cleared from ANO dashboard.');
        echo '<div class="notice notice-success"><p>Log berhasil dikosongkan.</p></div>';
    }

    $lines = array();
    if (is_readable($log_file)) {
        $content = @file($log_file, FILE_IGNORE_NEW_LINES);
        if (is_array($content)) {
            $lines = array_slice($content, -200);
            if (!empty($lines) && strpos($lines[0], '<?php exit;') !== false) {
                array_shift($lines);
            }
        }
    }
?>
    <div class="wrap">
        <h1>Log ANO</h1>
        <p>Log ini membantu menemukan error theme, fatal PHP, dan database error. Maksimal 200 baris terakhir ditampilkan.</p>
        <p><code><?php echo esc_html($log_file); ?></code></p>
        <form method="post" style="margin-bottom:16px;">
            <?php wp_nonce_field('ano_clear_log_action'); ?>
            <input type="hidden" name="ano_clear_log" value="1">
            <button type="submit" class="button" onclick="return confirm('Kosongkan log ANO?');">Kosongkan Log</button>
        </form>
        <textarea readonly style="width:100%;min-height:520px;font-family:monospace;white-space:pre;"><?php echo esc_textarea(implode("\n", $lines)); ?></textarea>
    </div>
<?php
}


function ano_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
    add_theme_support('custom-logo', array('height' => 80, 'width' => 260, 'flex-height' => true, 'flex-width' => true));
    register_nav_menus(array('primary' => 'Menu Utama'));
}
add_action('after_setup_theme', 'ano_setup');

function ano_assets()
{
    wp_enqueue_style('ano-style', get_stylesheet_uri(), array(), ANO_VERSION);
    wp_enqueue_script('ano-script', get_template_directory_uri() . '/assets/js/theme.js', array(), ANO_VERSION, true);
}
add_action('wp_enqueue_scripts', 'ano_assets');

function ano_register_cpts()
{
    register_post_type('ano_book', array(
        'labels' => array('name' => 'Buku', 'singular_name' => 'Buku', 'add_new_item' => 'Tambah Buku'),
        'public' => true,
        'show_in_menu' => false,
        'menu_icon' => 'dashicons-book-alt',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'),
        'show_in_rest' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'buku')
    ));
    register_post_type('ano_business', array(
        'labels' => array('name' => 'Usaha', 'singular_name' => 'Usaha', 'add_new_item' => 'Tambah Usaha'),
        'public' => true,
        'show_in_menu' => false,
        'menu_icon' => 'dashicons-store',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'),
        'show_in_rest' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'usaha')
    ));
    register_post_type('ano_initiative', array(
        'labels' => array('name' => 'Inisiatif', 'singular_name' => 'Inisiatif', 'add_new_item' => 'Tambah Inisiatif'),
        'public' => true,
        'show_in_menu' => false,
        'menu_icon' => 'dashicons-groups',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'),
        'show_in_rest' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'inisiatif')
    ));
}
add_action('init', 'ano_register_cpts');

function ano_meta_boxes()
{
    add_meta_box('ano_book_meta', 'Detail Buku', 'ano_book_meta_cb', 'ano_book');
    add_meta_box('ano_business_meta', 'Detail Usaha', 'ano_business_meta_cb', 'ano_business');
    add_meta_box('ano_initiative_meta', 'Detail Inisiatif', 'ano_initiative_meta_cb', 'ano_initiative');
}
add_action('add_meta_boxes', 'ano_meta_boxes');

/**
 * Thumbnail kartu (tampil di halaman utama) terpisah dari gambar asli
 * (featured image, tampil di halaman detail).
 */
function ano_thumb_post_types()
{
    return array('post', 'ano_book', 'ano_business', 'ano_initiative');
}
function ano_thumb_url($post, $size = 'medium_large')
{
    $post = get_post($post);
    if (!$post) return '';
    $tid = (int) get_post_meta($post->ID, 'ano_thumbnail_id', true);
    if ($tid) {
        $url = wp_get_attachment_image_url($tid, $size);
        if ($url) return $url;
    }
    $url = get_the_post_thumbnail_url($post, $size);
    return $url ? $url : '';
}
function ano_media_field($name, $id, $select_label = 'Pilih Gambar', $remove_label = 'Hapus Gambar')
{
    $id = (int) $id;
    $url = $id ? wp_get_attachment_image_url($id, 'thumbnail') : '';
?>
    <div class="ano-media-field">
        <input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo $id ? esc_attr($id) : ''; ?>">
        <div class="ano-media-preview" style="margin-bottom:8px;"><?php if ($url) : ?><img src="<?php echo esc_url($url); ?>" style="max-width:140px;height:auto;display:block;" alt=""><?php endif; ?></div>
        <button type="button" class="button ano-media-select"><?php echo esc_html($select_label); ?></button>
        <button type="button" class="button ano-media-remove" <?php echo $id ? '' : 'style="display:none"'; ?>><?php echo esc_html($remove_label); ?></button>
    </div>
<?php
}
function ano_thumb_meta_box()
{
    foreach (ano_thumb_post_types() as $pt) {
        add_meta_box('ano_thumb_meta', 'Thumbnail Kartu', 'ano_thumb_meta_cb', $pt, 'side', 'default');
    }
}
add_action('add_meta_boxes', 'ano_thumb_meta_box');
function ano_thumb_meta_cb($post)
{
    wp_nonce_field('ano_thumb', 'ano_thumb_nonce');
    ano_media_field('ano_thumbnail_id', get_post_meta($post->ID, 'ano_thumbnail_id', true), 'Pilih Thumbnail', 'Hapus Thumbnail');
    echo '<p class="description">Tampil di halaman utama dan daftar. Jika kosong, dipakai gambar unggulan (gambar asli). Gambar asli tampil di halaman detail.</p>';
}
function ano_save_thumb_meta($post_id)
{
    if (!isset($_POST['ano_thumb_nonce']) || !wp_verify_nonce($_POST['ano_thumb_nonce'], 'ano_thumb')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;
    if (!current_user_can('edit_post', $post_id)) return;
    $tid = isset($_POST['ano_thumbnail_id']) ? absint($_POST['ano_thumbnail_id']) : 0;
    if ($tid && get_post_type($tid) === 'attachment') update_post_meta($post_id, 'ano_thumbnail_id', $tid);
    else delete_post_meta($post_id, 'ano_thumbnail_id');
}
add_action('save_post', 'ano_save_thumb_meta');
function ano_thumb_admin_assets($hook)
{
    if (!in_array($hook, array('post.php', 'post-new.php'), true)) return;
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->post_type, ano_thumb_post_types(), true)) return;
    wp_enqueue_media();
    add_action('admin_footer', 'ano_content_media_script');
}
add_action('admin_enqueue_scripts', 'ano_thumb_admin_assets');

function ano_field($label, $key, $value = '', $type = 'text')
{
    printf(
        '<p><label><strong>%s</strong><br><input type="%s" name="%s" value="%s" style="width:100%%"></label></p>',
        esc_html($label),
        esc_attr($type),
        esc_attr($key),
        esc_attr($value)
    );
}
function ano_book_meta_cb($post)
{
    wp_nonce_field('ano_meta', 'ano_meta_nonce');
    ano_field('Subjudul / jenis', 'ano_book_subtitle', get_post_meta($post->ID, 'ano_book_subtitle', true));
    ano_field('Penulis', 'ano_book_author', get_post_meta($post->ID, 'ano_book_author', true));
}
function ano_business_meta_cb($post)
{
    wp_nonce_field('ano_meta', 'ano_meta_nonce');
    ano_field('Label tombol / URL', 'ano_business_url', get_post_meta($post->ID, 'ano_business_url', true), 'url');
}
function ano_initiative_meta_cb($post)
{
    wp_nonce_field('ano_meta', 'ano_meta_nonce');
    ano_field('Label tombol / URL', 'ano_initiative_url', get_post_meta($post->ID, 'ano_initiative_url', true), 'url');
}
function ano_save_meta($post_id)
{
    if (!isset($_POST['ano_meta_nonce']) || !wp_verify_nonce($_POST['ano_meta_nonce'], 'ano_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    foreach (array('ano_book_subtitle', 'ano_book_author', 'ano_business_url', 'ano_initiative_url') as $key) {
        if (isset($_POST[$key])) update_post_meta($post_id, $key, sanitize_text_field(wp_unslash($_POST[$key])));
    }
}
add_action('save_post', 'ano_save_meta');


/**
 * Dedicated content management screens for the homepage sections.
 * These screens provide a simple form while still using WordPress CPTs,
 * featured images and the normal editor underneath.
 */
function ano_content_admin_menu()
{
    add_menu_page(
        'Konten ANO',
        'Konten ANO',
        'edit_posts',
        'ano-content',
        'ano_content_dashboard_page',
        'dashicons-layout',
        25
    );
    add_submenu_page('ano-content', 'Bibliografi Buku', 'Bibliografi Buku', 'edit_posts', 'ano-books', 'ano_content_books_page');
    add_submenu_page('ano-content', 'Usaha', 'Usaha', 'edit_posts', 'ano-business', 'ano_content_business_page');
    add_submenu_page('ano-content', 'Inisiatif', 'Inisiatif', 'edit_posts', 'ano-initiative', 'ano_content_initiative_page');
}
add_action('admin_menu', 'ano_content_admin_menu');

function ano_content_types()
{
    return array(
        'book' => array('post_type' => 'ano_book', 'label' => 'Buku', 'menu_slug' => 'ano-books'),
        'business' => array('post_type' => 'ano_business', 'label' => 'Usaha', 'menu_slug' => 'ano-business'),
        'initiative' => array('post_type' => 'ano_initiative', 'label' => 'Inisiatif', 'menu_slug' => 'ano-initiative'),
    );
}

function ano_content_dashboard_page()
{
    if (!current_user_can('edit_posts')) wp_die('Anda tidak memiliki izin.');
    $types = ano_content_types();
?>
    <div class="wrap">
        <h1>Konten ANO</h1>
        <p>Kelola konten yang tampil di halaman depan. Semua data tetap tersimpan sebagai post type WordPress sehingga bisa diedit kembali dari dashboard.</p>
        <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;max-width:1100px;margin-top:24px;">
            <?php foreach ($types as $key => $type) :
                $count = wp_count_posts($type['post_type']);
                $published = isset($count->publish) ? (int) $count->publish : 0;
            ?>
                <div style="background:#fff;border:1px solid #dcdcde;border-radius:6px;padding:22px;">
                    <h2 style="margin-top:0"><?php echo esc_html($type['label']); ?></h2>
                    <p><?php echo esc_html($published); ?> konten aktif.</p>
                    <a class="button button-primary" href="<?php echo esc_url(admin_url('admin.php?page=' . $type['menu_slug'])); ?>">Kelola <?php echo esc_html($type['label']); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="margin-top:28px;background:#f6f7f7;padding:18px;max-width:1100px;">
            <strong>Tips:</strong> <em>Gambar Asli</em> tampil penuh di halaman detail, sedangkan <em>Thumbnail</em> tampil di halaman utama (jika kosong, dipakai Gambar Asli). Field <em>Urutan</em> menentukan posisi item di halaman depan.
        </div>
    </div>
<?php
}

function ano_content_admin_page($type_key)
{
    if (!current_user_can('edit_posts')) wp_die('Anda tidak memiliki izin.');
    $types = ano_content_types();
    if (!isset($types[$type_key])) wp_die('Tipe konten tidak ditemukan.');
    $type = $types[$type_key];
    $post_type = $type['post_type'];
    $message = '';
    $editing_id = isset($_GET['edit']) ? absint($_GET['edit']) : 0;

    if (isset($_POST['ano_content_action'])) {
        check_admin_referer('ano_content_manage_' . $type_key);
        $action = sanitize_key(wp_unslash($_POST['ano_content_action']));
        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        $title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';
        $description = isset($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : '';
        $url = isset($_POST['url']) ? esc_url_raw(wp_unslash($_POST['url'])) : '';
        $order = isset($_POST['menu_order']) ? intval($_POST['menu_order']) : 0;
        $subtitle = isset($_POST['subtitle']) ? sanitize_text_field(wp_unslash($_POST['subtitle'])) : '';
        $author = isset($_POST['author']) ? sanitize_text_field(wp_unslash($_POST['author'])) : '';
        $image_id = isset($_POST['image_id']) ? absint($_POST['image_id']) : 0;
        $thumbnail_id = isset($_POST['thumbnail_id']) ? absint($_POST['thumbnail_id']) : 0;

        if ($action === 'delete' && $post_id) {
            if (get_post_type($post_id) === $post_type && current_user_can('delete_post', $post_id)) {
                wp_delete_post($post_id, true);
                ano_log('Content deleted from ANO manager.', array('type' => $type_key, 'post_id' => $post_id));
                $message = 'Konten berhasil dihapus.';
                $editing_id = 0;
            }
        } elseif (in_array($action, array('create', 'update'), true)) {
            if ($title === '') {
                $message = 'Judul wajib diisi.';
            } else {
                $postarr = array(
                    'post_type' => $post_type,
                    'post_status' => 'publish',
                    'post_title' => $title,
                    'post_excerpt' => $description,
                    'menu_order' => $order,
                );
                if ($action === 'update' && $post_id) {
                    $postarr['ID'] = $post_id;
                    $saved_id = wp_update_post($postarr, true);
                } else {
                    $saved_id = wp_insert_post($postarr, true);
                }
                if (is_wp_error($saved_id)) {
                    ano_log_error('Failed saving ANO content.', array('type' => $type_key, 'error' => $saved_id->get_error_message()));
                    $message = 'Gagal menyimpan: ' . $saved_id->get_error_message();
                } else {
                    if ($type_key === 'book') {
                        update_post_meta($saved_id, 'ano_book_subtitle', $subtitle);
                        update_post_meta($saved_id, 'ano_book_author', $author);
                    } else {
                        update_post_meta($saved_id, 'ano_' . $type_key . '_url', $url);
                    }
                    if ($image_id) set_post_thumbnail($saved_id, $image_id);
                    else delete_post_thumbnail($saved_id);
                    if ($thumbnail_id && get_post_type($thumbnail_id) === 'attachment') update_post_meta($saved_id, 'ano_thumbnail_id', $thumbnail_id);
                    else delete_post_meta($saved_id, 'ano_thumbnail_id');
                    ano_log('ANO content saved.', array('type' => $type_key, 'post_id' => $saved_id, 'action' => $action));
                    $message = 'Konten berhasil disimpan.';
                    $editing_id = $saved_id;
                }
            }
        }
    }

    $editing = $editing_id ? get_post($editing_id) : null;
    if ($editing && $editing->post_type !== $post_type) $editing = null;
    $field = function ($key, $default = '') use ($editing) {
        if (!$editing) return $default;
        return get_post_meta($editing->ID, $key, true);
    };
    $image_id = $editing ? get_post_thumbnail_id($editing->ID) : 0;
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
    $thumbnail_id = $editing ? (int) get_post_meta($editing->ID, 'ano_thumbnail_id', true) : 0;
    $items = get_posts(array('post_type' => $post_type, 'post_status' => array('publish', 'draft', 'pending'), 'posts_per_page' => 100, 'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC')));
?>
    <div class="wrap ano-content-admin">
        <h1><?php echo esc_html($type['label']); ?></h1>
        <?php if ($message) : ?><div class="notice notice-info is-dismissible">
                <p><?php echo esc_html($message); ?></p>
            </div><?php endif; ?>
        <div style="display:grid;grid-template-columns:minmax(360px,520px) 1fr;gap:24px;align-items:start;">
            <div style="background:#fff;border:1px solid #dcdcde;padding:20px;">
                <h2 style="margin-top:0"><?php echo $editing ? 'Edit ' . esc_html($type['label']) : 'Tambah ' . esc_html($type['label']); ?></h2>
                <form method="post">
                    <?php wp_nonce_field('ano_content_manage_' . $type_key); ?>
                    <input type="hidden" name="ano_content_action" value="<?php echo $editing ? 'update' : 'create'; ?>">
                    <input type="hidden" name="post_id" value="<?php echo $editing ? esc_attr($editing->ID) : '0'; ?>">
                    <table class="form-table" role="presentation">
                        <tr>
                            <th><label for="ano-title">Judul</label></th>
                            <td><input id="ano-title" class="regular-text" type="text" name="title" required value="<?php echo $editing ? esc_attr($editing->post_title) : ''; ?>"></td>
                        </tr>
                        <tr>
                            <th><label for="ano-description">Deskripsi</label></th>
                            <td><textarea id="ano-description" name="description" rows="5" class="large-text"><?php echo $editing ? esc_textarea($editing->post_excerpt) : ''; ?></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="ano-order">Urutan</label></th>
                            <td><input id="ano-order" type="number" name="menu_order" value="<?php echo $editing ? esc_attr($editing->menu_order) : '0'; ?>" min="0">
                                <p class="description">Angka kecil tampil lebih dahulu.</p>
                            </td>
                        </tr>
                        <?php if ($type_key === 'book') : ?>
                            <tr>
                                <th><label>Subjudul / jenis</label></th>
                                <td><input class="regular-text" type="text" name="subtitle" value="<?php echo esc_attr($field('ano_book_subtitle')); ?>"></td>
                            </tr>
                            <tr>
                                <th><label>Penulis</label></th>
                                <td><input class="regular-text" type="text" name="author" value="<?php echo esc_attr($field('ano_book_author')); ?>"></td>
                            </tr>
                        <?php else : ?>
                            <tr>
                                <th><label>URL</label></th>
                                <td><input class="regular-text" type="url" name="url" value="<?php echo esc_attr($field('ano_' . $type_key . '_url')); ?>">
                                    <p class="description">Jika kosong, link akan menuju halaman detail konten.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Gambar Asli</th>
                            <td>
                                <?php ano_media_field('image_id', $image_id, 'Pilih Gambar', 'Hapus Gambar'); ?>
                                <p class="description">Tampil penuh di halaman detail.</p>
                            </td>
                        </tr>
                        <tr>
                            <th>Thumbnail</th>
                            <td>
                                <?php ano_media_field('thumbnail_id', $thumbnail_id, 'Pilih Thumbnail', 'Hapus Thumbnail'); ?>
                                <p class="description">Tampil di halaman utama. Jika kosong, dipakai Gambar Asli.</p>
                            </td>
                        </tr>
                    </table>
                    <p><button class="button button-primary" type="submit">Simpan <?php echo esc_html($type['label']); ?></button>
                        <?php if ($editing) : ?><a class="button" href="<?php echo esc_url(admin_url('admin.php?page=' . $type['menu_slug'])); ?>">Batal / Tambah Baru</a><?php endif; ?></p>
                </form>
                <?php if ($editing) : ?>
                    <form method="post" style="margin-top:8px" onsubmit="return confirm('Hapus konten ini?');">
                        <?php wp_nonce_field('ano_content_manage_' . $type_key); ?>
                        <input type="hidden" name="ano_content_action" value="delete"><input type="hidden" name="post_id" value="<?php echo esc_attr($editing->ID); ?>">
                        <button class="button-link-delete" type="submit">Hapus <?php echo esc_html($type['label']); ?></button>
                    </form>
                <?php endif; ?>
            </div>
            <div>
                <h2 style="margin-top:0">Daftar <?php echo esc_html($type['label']); ?></h2>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th>Urutan</th>
                            <th>Judul</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($items) : foreach ($items as $item) : ?>
                                <tr>
                                    <td><?php echo esc_html($item->menu_order); ?></td>
                                    <td><strong><?php echo esc_html($item->post_title); ?></strong></td>
                                    <td><?php echo esc_html($item->post_status); ?></td>
                                    <td><a href="<?php echo esc_url(admin_url('admin.php?page=' . $type['menu_slug'] . '&edit=' . $item->ID)); ?>">Edit</a> · <a href="<?php echo esc_url(get_permalink($item)); ?>" target="_blank" rel="noopener">Lihat</a></td>
                                </tr>
                            <?php endforeach;
                        else : ?>
                            <tr>
                                <td colspan="4">Belum ada konten.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php
    wp_enqueue_media();
    add_action('admin_footer', 'ano_content_media_script');
}

function ano_content_media_script()
{
    static $printed = false;
    if ($printed) return;
    $printed = true;
?>
    <script>
        jQuery(function($) {
            $(document).on('click', '.ano-media-select', function(e) {
                e.preventDefault();
                const $f = $(this).closest('.ano-media-field');
                let frame = $f.data('frame');
                if (!frame) {
                    frame = wp.media({
                        title: 'Pilih gambar',
                        button: {
                            text: 'Gunakan gambar'
                        },
                        multiple: false
                    });
                    frame.on('select', function() {
                        const a = frame.state().get('selection').first().toJSON();
                        const src = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
                        $f.find('input[type=hidden]').val(a.id);
                        $f.find('.ano-media-preview').empty().append($('<img>').attr('src', src).css({
                            maxWidth: '140px',
                            height: 'auto',
                            display: 'block'
                        }));
                        $f.find('.ano-media-remove').show();
                    });
                    $f.data('frame', frame);
                }
                frame.open();
            });
            $(document).on('click', '.ano-media-remove', function(e) {
                e.preventDefault();
                const $f = $(this).closest('.ano-media-field');
                $f.find('input[type=hidden]').val('');
                $f.find('.ano-media-preview').empty();
                $(this).hide();
            });
        });
    </script>
<?php
}

function ano_content_books_page()
{
    ano_content_admin_page('book');
}
function ano_content_business_page()
{
    ano_content_admin_page('business');
}
function ano_content_initiative_page()
{
    ano_content_admin_page('initiative');
}

function ano_excerpt($text, $length = 115)
{
    $text = wp_strip_all_tags($text);
    return wp_html_excerpt($text, $length, '…');
}



/**
 * Halaman "Semua Artikel" (/artikel/) — daftar semua post.
 * Jika Pengaturan → Membaca → "Halaman pos" sudah diatur, halaman itu yang dipakai.
 */
function ano_posts_archive_url()
{
    $page_id = (int) get_option('page_for_posts');
    if ($page_id) {
        $link = get_permalink($page_id);
        if ($link) return $link;
    }
    if (get_option('permalink_structure')) return home_url('/artikel/');
    return add_query_arg('ano_posts', '1', home_url('/'));
}
function ano_posts_rewrite()
{
    add_rewrite_rule('^artikel/page/([0-9]{1,})/?$', 'index.php?ano_posts=1&paged=$matches[1]', 'top');
    add_rewrite_rule('^artikel/?$', 'index.php?ano_posts=1', 'top');
}
add_action('init', 'ano_posts_rewrite');
add_filter('query_vars', function ($vars) {
    $vars[] = 'ano_posts';
    return $vars;
});
add_action('init', function () {
    if (get_option('ano_rewrite_version') !== ANO_VERSION) {
        flush_rewrite_rules(false);
        update_option('ano_rewrite_version', ANO_VERSION);
    }
}, 99);
add_action('pre_get_posts', function ($q) {
    // Pencarian publik mencakup artikel, Buku, Usaha, dan Inisiatif.
    if (!is_admin() && $q->is_main_query() && $q->is_search()) {
        $q->set('post_type', array('post', 'ano_book', 'ano_business', 'ano_initiative'));
        $q->set('post_status', 'publish');
    }
    if (is_admin() || !$q->is_main_query() || !$q->get('ano_posts')) return;
    $q->set('post_type', 'post');
    $q->set('post_status', 'publish');
    $q->set('ignore_sticky_posts', true);
    $q->set('page_id', '');
    $q->set('p', '');
    $q->is_page = false;
    $q->is_singular = false;
    $q->is_home = false;
    $q->is_404 = false;
    $q->is_archive = true;
});
add_filter('template_include', function ($template) {
    if (get_query_var('ano_posts')) {
        $archive = locate_template('archive.php');
        if ($archive) return $archive;
    }
    return $template;
});
add_filter('get_the_archive_title', function ($title) {
    return get_query_var('ano_posts') ? 'Semua Artikel' : $title;
});
add_filter('get_the_archive_description', function ($desc) {
    return get_query_var('ano_posts') ? 'Tulisan, gagasan, dan catatan terbaru.' : $desc;
});
add_filter('pre_get_document_title', function ($title) {
    return get_query_var('ano_posts') ? 'Semua Artikel — ' . get_bloginfo('name') : $title;
});
