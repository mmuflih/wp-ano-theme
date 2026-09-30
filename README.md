# ANO WordPress Theme

Custom WordPress theme inspired by the supplied ANO reference design.

## Instalasi

1. WordPress Admin → Appearance → Themes → Add New → Upload Theme.
2. Upload `ano-wordpress-theme-logging.zip`.
3. Activate the theme.
4. Settings → Permalinks → Save Changes.
5. Appearance → Demo ANO → Buat Konten Demo.

## Logging / Troubleshooting

Theme ini memiliki logging internal untuk membantu diagnosis ketika terjadi masalah.

### Dashboard

Setelah theme aktif, buka:

**Appearance → Log ANO**

Halaman tersebut menampilkan maksimal 200 baris log terakhir dan menyediakan tombol **Kosongkan Log**.

### Jenis error yang dicatat

- Aktivasi/deaktivasi theme.
- Fatal PHP error yang terjadi setelah `functions.php` berhasil dimuat.
- Exception dari proses Demo ANO.
- Error database WordPress yang tersedia melalui `$wpdb->last_error`.
- Pesan diagnostik dari proses internal theme.

### WordPress Debug Log

Untuk diagnosis lebih lengkap di server development/staging, tambahkan konfigurasi berikut ke `wp-config.php`, sebelum baris `/* That's all, stop editing! */`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Dengan konfigurasi tersebut, error WordPress/PHP akan masuk ke `wp-content/debug.log` dan tidak ditampilkan kepada pengunjung.

**Catatan keamanan:** jangan mengaktifkan `WP_DEBUG_DISPLAY` di production karena pesan error dapat terlihat oleh pengunjung.

## Catatan fatal parse error

Fatal parse error pada file theme yang membuat PHP tidak dapat memuat `functions.php` tidak dapat dicatat oleh fungsi logging di dalam file yang sama. Karena itu paket theme ini sudah diperiksa dengan PHP syntax check sebelum dibuat menjadi ZIP. Untuk error jenis tersebut, lihat PHP error log milik hosting/server.


## Hero Slider
Homepage menampilkan maksimal 3 artikel terbaru (`post`) secara otomatis, diurutkan berdasarkan tanggal publikasi terbaru. Background hero menggunakan `assets/images/hero.jpg` tanpa teks yang tertanam di gambar. Slider memiliki autoplay 6 detik, dot navigation, dan pause saat hover/focus.

## Hero image

`assets/images/hero.jpg` has been replaced with the supplied Flores landscape image. The image contains no embedded header or hero text; all slider text is rendered dynamically by WordPress from the 3 latest posts.
