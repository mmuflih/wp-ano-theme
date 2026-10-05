### 1.4.19 — 2026-10-04
- Mengaktifkan pencarian website dari ikon search; pencarian mencakup Artikel, Buku, Usaha, dan Inisiatif.

### 1.4.17 — 2026-09-30
- Menu admin Buku, Usaha, dan Inisiatif disembunyikan dari sidebar dashboard; kelola lewat Konten ANO.

### 1.4.16 — 2026-09-30
- Detail artikel: gambar unggulan (`.single-image` / `.wp-post-image`) disembunyikan. Detail Buku, Usaha, dan Inisiatif tidak berubah.

### 1.4.15 — 2026-09-30
- Border card di halaman "Lihat Semua" (Artikel, Buku, Usaha, Inisiatif) dihilangkan.

### 1.4.14 — 2026-09-30
- "Lihat Semua Artikel" kini mengarah ke halaman daftar semua post (/artikel/), lengkap dengan pagination.

### 1.4.13 — 2026-09-30
- Border pada `.article-card` dan `.content-card` dihilangkan.

### 1.4.12 — 2026-09-30
- Background semua halaman (body, header, menu mobile) diubah menjadi putih.

### 1.4.11 — 2026-09-30
- Tambah input Thumbnail (terpisah dari Gambar Asli) untuk Artikel, Buku, Usaha, dan Inisiatif.
- Thumbnail tampil di halaman utama dan daftar arsip; halaman detail menampilkan gambar asli ukuran penuh.
- Jika thumbnail kosong, dipakai gambar unggulan/Gambar Asli.
- Tombol "Hapus Gambar" di Konten ANO kini benar-benar menghapus gambar saat disimpan.

### 1.4.10 — 2026-09-30
- Judul card: pindah baris di spasi, kata panjang tanpa spasi dipotong dengan "...".

### 1.4.9 — 2026-09-30
- Judul card (Artikel, Buku, Usaha, Inisiatif) boleh lebih dari satu baris (pindah baris di spasi); kata tunggal yang melebihi lebar kotak dipotong dengan "...".
- Halaman detail Buku, Usaha, dan Inisiatif kini menampilkan deskripsi, subjudul/penulis (Buku), dan tombol website (Usaha/Inisiatif).
- Pembaruan `front-page.php`, `footer.php`, dan `screenshot.png`.

### 1.4.4 — 2026-09-30
- Penyempurnaan tampilan responsif bagian Inisiatif pada mobile.

# ANO WordPress Theme

Custom WordPress theme inspired by the supplied ANO reference design.

## Instalasi

1. WordPress Admin → Appearance → Themes → Add New → Upload Theme.
2. Upload `ano-wordpress-theme-logging.zip`.
3. Activate the theme.
4. Settings → Permalinks → Save Changes.

## Logging / Troubleshooting

Theme ini memiliki logging internal untuk membantu diagnosis ketika terjadi masalah.

### Dashboard

Setelah theme aktif, buka:

**Appearance → Log ANO**

Halaman tersebut menampilkan maksimal 200 baris log terakhir dan menyediakan tombol **Kosongkan Log**.

### Jenis error yang dicatat

- Aktivasi/deaktivasi theme.
- Fatal PHP error yang terjadi setelah `functions.php` berhasil dimuat.
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


## Versioning

- **1.4.8** — 2026-09-30: Menyeragamkan grid konten homepage menjadi 4 kolom (25% per item) untuk Artikel, Bibliografi Buku, Usaha, dan Inisiatif; Artikel dan Usaha menampilkan maksimal 4 item di homepage.

- 1.4.6 — 2026-09-30: Mobile header: burger menu ditempatkan di sebelah kiri judul ANO.

- **1.4.3** — 2026-09-30: tautan **Lihat Detail** pada bagian Inisiatif dibuka di tab baru dengan `target="_blank"` dan `rel="noopener noreferrer"`.

- **1.4.2** — 2026-09-30: tautan **Kunjungi Website** pada bagian Usaha dibuka di tab baru dengan `target="_blank"` dan `rel="noopener noreferrer"`.

- **1.4.1** — 2026-09-30: memperbaiki tampilan logo Inisiatif agar mempertahankan rasio asli menggunakan area 1:1 dan `object-fit: contain`, sehingga logo tidak gepeng/terdistorsi.

### 1.4.0 — 2026-09-30
- Version theme diseragamkan ke `1.4.0`.
- `ANO_VERSION` digunakan sebagai versi asset CSS dan JavaScript untuk cache busting.
- Changelog mulai dicatat di README untuk memudahkan tracking perubahan theme.

### 1.3.0
- Inisiatif menggunakan Featured Image dalam format square dengan `object-fit: cover`.
- Menu Generate Demo dihapus.
- Author theme: Muflih Kholidin.

### 1.2.0
- Hero slider 3 artikel terbaru.
- Content Manager untuk Buku, Usaha, dan Inisiatif.
- Logging ANO.
