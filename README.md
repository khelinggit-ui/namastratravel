# Namastra Travel — Backend (Laravel)

Backend REST API + Admin CMS untuk **Namastra Travel**. Dekat dengan frontend React yang sudah dibuat.

- **Stack:** Laravel 12, MySQL/MariaDB, Sanctum (auth token), storage lokal
- **Lokasi:** `backend/` (terpisah dari frontend React di root)

---

## 1. Setup Lokal (Laragon)

```bash
cd backend

# 1) Install dependencies
composer install

# 2) Konfigurasi .env (sudah diarahkan ke MySQL)
cp .env.example .env            # Windows: copy .env.example .env
# isi: DB_CONNECTION=mysql, DB_DATABASE=namastra_travel, DB_USERNAME=root, DB_PASSWORD=

# 3) Generate key + buat database (via phpMyAdmin / Laragon)
php artisan key:generate

# 4) Migrate + seed (users, tours, destinations, posts, dsb + user admin)
php artisan migrate --seed

# 5) Symlink storage agar file upload bisa diakses publik
php artisan storage:link
```

## 2. Menjalankan

```bash
php artisan serve --port 8000
```

- **API :** `http://localhost:8000/api/...`
- **Admin CMS (Blade):** `http://localhost:8000/admin`

### Akun Admin (hasil seeder)
| Field | Nilai |
|---|---|
| Email | `admin@namastratravel.com` |
| Password | `namastra2026` |

> ⚠️ Ubah password & buat user admin tambahan sebelum produksi.

---

## 3. Menghubungkan Frontend React ke API

Edit `../src/api/config.js` pada project frontend:

```js
export const API_BASE_URL = 'http://localhost:8000/api';
export const USE_MOCK = false;   // matikan mode mock
```

---

## 4. Endpoint API

Dokumentasi lengkap request, response, autentikasi, CashUP payment, error code, dan contoh penggunaan tersedia di [API.md](API.md).

### Publik (tanpa auth)
| Method | Path | Keterangan |
|---|---|---|
| GET | `/api/tours?category=&q=` | Daftar tour (filter lokal) |
| GET | `/api/tours/{slug}` | Detail tour |
| GET | `/api/destinations` | Daftar destinasi |
| GET | `/api/destinations/{slug}` | Detail destinasi + tour terkait |
| GET | `/api/posts?category=&q=` | Daftar artikel |
| GET | `/api/posts/{slug}` | Detail artikel |
| GET | `/api/testimonials` | Testimoni |
| GET | `/api/about` | Konten "Tentang Kami" |
| GET | `/api/settings` | Pengaturan situs |
| POST | `/api/bookings` | Submit booking (rate limit 5/menit) |
| POST | `/api/contacts` | Submit form kontak (rate limit 5/menit) |
| POST | `/api/payments/cashup/create` | Buat booking pending dan hosted payment link CashUP |
| GET | `/api/payments/cashup/callback` | Callback server dari CashUP |
| GET | `/api/payments/cashup/status/{booking}` | Verifikasi status payment berdasarkan booking |
| GET | `/api/payments/cashup/status-by-order/{orderId}` | Verifikasi status berdasarkan order CashUP |

### Admin (auth: `Authorization: Bearer <token>`)
| Method | Path | Keterangan |
|---|---|---|
| POST | `/api/auth/login` | Login → dapat token |
| GET | `/api/auth/me` | Data user login |
| POST | `/api/auth/logout` | Logout (hapus token) |
| GET | `/api/admin/dashboard` | Ringkasan dashboard |
| POST | `/api/admin/upload` | Upload gambar (field `image`) |
| GET/POST | `/api/admin/tours` | List / buat tour |
| GET/PUT/DELETE | `/api/admin/tours/{id}` | Baca/ubah/hapus tour |
| GET/POST | `/api/admin/destinations` | List / buat destinasi |
| GET/PUT/DELETE | `/api/admin/destinations/{id}` | CRUD destinasi |
| GET/POST | `/api/admin/posts` | List / buat artikel |
| GET/PUT/DELETE | `/api/admin/posts/{id}` | CRUD artikel |
| GET/POST/PUT/DELETE | `/api/admin/testimonials` | CRUD testimoni |
| GET | `/api/admin/bookings` | Daftar booking (filter status/q) |
| GET | `/api/admin/bookings/export` | Export CSV |
| GET | `/api/admin/bookings/{id}` | Detail booking |
| PUT | `/api/admin/bookings/{id}` | Ubah status (baru/dihubungi/selesai) |
| DELETE | `/api/admin/bookings/{id}` | Hapus booking |
| GET | `/api/admin/contacts` | Daftar kontak masuk |
| PUT/DELETE | `/api/admin/contacts/{id}` | Ubah status/hapus |
| GET/PUT | `/api/admin/about` | Baca / ubah konten tentang |
| GET/PUT | `/api/admin/settings` | Baca / ubah pengaturan |

### Contoh login (token)
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@namastratravel.com","password":"namastra2026"}'
# → {"token":"<plain-text-token>", ...}

curl http://localhost:8000/api/admin/dashboard \
  -H "Authorization: Bearer <token>"
```

---

## 5. Keamanan
- Validasi & sanitasi input di setiap controller (`Request::validate`).
- Rate limiting pada `/api/bookings`, `/api/contacts`, `/api/auth/login` (`throttle`).
- Autentikasi admin: web (Blade) memakai **session**; API admin memakai **Sanctum token**.
- Upload gambar divalidasi (image, mimes, max 5MB).
- Notifikasi email dikirim ke admin saat ada booking baru (`NewBookingNotification`), di-log saat `MAIL_MAILER=log`.
- Credential CashUP disimpan di `.env` dan seluruh pemanggilan CashUP dilakukan dari Laravel.
- Detail setup, callback localhost, dan pengujian CashUP tersedia di `../CASHUP-INTEGRATION.md`.

---

## 6. Struktur Utama
- `routes/api.php` — endpoint REST publik + admin.
- `routes/web.php` — halaman admin Blade di `/admin`.
- `app/Http/Controllers/Api/*` — controller API.
- `app/Http/Controllers/Admin/*` — controller admin panel.
- `app/Models/*` — model Eloquent.
- `database/migrations/*` — skema DB.
- `database/seeders/DatabaseSeeder.php` — data awal + admin user.
- `resources/views/admin/*` — tampilan admin Blade.
- `app/Support/Media.php` — helper URL gambar.
