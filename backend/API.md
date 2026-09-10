# Namastra Travel API

Dokumentasi REST API Laravel untuk frontend React dan Admin CMS.

## Konvensi

Base URL lokal:

```text
http://localhost:8000/api
```

Base URL production:

```text
https://api.namastratravel.com/api
```

Semua request JSON menggunakan header:

```http
Accept: application/json
Content-Type: application/json
```

Response validasi gagal menggunakan HTTP `422` dengan bentuk umum:

```json
{
  "message": "Validasi gagal.",
  "errors": {
    "field": ["Pesan validasi"]
  }
}
```

## Alur frontend

```text
GET tours
-> GET tours/{slug}
-> Customer mengisi checkout
-> POST payments/cashup/create
-> Redirect ke payment_url CashUP
-> GET payments/cashup/status-by-order/{orderId}
```

## Endpoint publik

Endpoint berikut tidak membutuhkan login.

### Tour

```http
GET /tours
GET /tours/{slug}
```

Query opsional pada daftar tour:

| Parameter | Tipe | Keterangan |
|---|---|---|
| `category` | string | `domestik` atau `mancanegara` |
| `q` | string | Pencarian judul/konten sesuai implementasi controller |

### Destinasi

```http
GET /destinations
GET /destinations/{slug}
```

### Blog

```http
GET /posts
GET /posts/{slug}
```

Query opsional daftar post:

| Parameter | Tipe | Keterangan |
|---|---|---|
| `category` | string | Filter kategori |
| `q` | string | Pencarian artikel |

### Konten situs

```http
GET /testimonials
GET /about
GET /settings
```

### Booking inquiry tanpa payment

```http
POST /bookings
```

Request:

```json
{
  "customer_name": "Nama Customer",
  "tour": "Paket Tour Bali",
  "whatsapp": "08123456789",
  "email": "customer@example.com",
  "destination": "Tour Bali 5 hari 4 malam",
  "pax": "2 orang",
  "date": "2026-12-24"
}
```

Field wajib:

- `whatsapp`: string, maksimal 20 karakter
- `email`: email valid
- `destination`: string, maksimal 2.000 karakter

Response `201`:

```json
{
  "message": "Terima kasih! Tim kami akan menghubungi Anda segera.",
  "booking": {
    "id": 12,
    "customer_name": "Nama Customer",
    "tour_name": "Paket Tour Bali",
    "whatsapp": "08123456789",
    "email": "customer@example.com",
    "destination": "Tour Bali 5 hari 4 malam",
    "pax": "2 orang",
    "planned_date": "2026-12-24",
    "status": "baru"
  }
}
```

Rate limit: `5 request/menit`.

### Contact

```http
POST /contacts
```

Gunakan field contact yang tersedia pada form frontend. Endpoint memiliki rate limit `5 request/menit`; response validasi mengikuti format Laravel di atas.

## CashUP payment

### Membuat payment link

```http
POST /payments/cashup/create
```

Request:

```json
{
  "customer_name": "Nama Customer",
  "tour_slug": "bromo-ijen-sunrise-adventure",
  "schedule_start_date": "24 Dec 2026",
  "whatsapp": "08123456789",
  "email": "customer@example.com",
  "destination": "Bromo dan Ijen 3 hari 2 malam",
  "pax": "2 orang",
  "date": "24 Dec 2026 - 28 Dec 2026"
}
```

Field wajib:

- `customer_name`: string, maksimal 255 karakter
- `tour_slug`: slug tour published
- `whatsapp`: string, maksimal 20 karakter
- `email`: email valid
- `destination`: string, maksimal 2.000 karakter

Field opsional:

- `schedule_start_date`: tanggal mulai yang cocok dengan jadwal tour
- `pax`: jumlah peserta
- `date`: tanggal atau rentang tanggal

Nominal tidak dikirim dari frontend. Laravel mengambil harga dari `price_start` tour atau harga jadwal yang cocok. Nominal harus lebih besar dari Rp1.000.

Response `201`:

```json
{
  "message": "Payment link berhasil dibuat.",
  "booking_id": 12,
  "payment_url": "https://link.cashup.id/payment/....",
  "order_id": "cashup-order-id",
  "amount": 4500000
}
```

Response penting:

| HTTP | Kondisi |
|---|---|
| `201` | Payment link berhasil dibuat |
| `404` | Tour tidak ditemukan/tidak published |
| `422` | Data invalid atau harga terlalu kecil |
| `502` | CashUP gagal membuat payment link |

Rate limit: `5 request/menit`.

### Callback CashUP

```http
GET /payments/cashup/callback?orderId={order_id}
```

CashUP mengirim `orderId` atau `order_id`. Laravel mencari booking berdasarkan `payment_order_id`, lalu memanggil Check Status ke CashUP. Parameter redirect dari browser tidak digunakan sebagai bukti final pembayaran.

### Status berdasarkan booking

```http
GET /payments/cashup/status/{booking_id}
```

Contoh response:

```json
{
  "booking_id": 12,
  "order_id": "cashup-order-id",
  "payment_status": "paid",
  "amount": 4500000,
  "message": "PAYMENT ALREADY PAID.",
  "cashup": {}
}
```

### Status berdasarkan order CashUP

```http
GET /payments/cashup/status-by-order/{orderId}
```

Endpoint ini digunakan halaman frontend `/payment/result`.

Status internal aplikasi:

```text
pending
paid
failed
expired
cancelled
```

Status menjadi `paid` hanya jika CashUP mengembalikan status pembayaran yang sesuai, seperti `PAID`, `SUCCESS`, atau `COMPLETED`.

Detail setup credential, tunnel callback, Mailtrap, dan CashUP tersedia di [CASHUP-INTEGRATION.md](../CASHUP-INTEGRATION.md).

## Autentikasi Admin API

### Login

```http
POST /auth/login
```

Request:

```json
{
  "email": "admin@namastratravel.com",
  "password": "password-admin"
}
```

Response:

```json
{
  "token": "1|plain-text-sanctum-token",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@namastratravel.com"
  }
}
```

Gunakan token pada request admin:

```http
Authorization: Bearer 1|plain-text-sanctum-token
```

Login memiliki rate limit `10 request/menit`.

### Session admin

```http
GET /admin/auth/me
POST /admin/auth/logout
```

Semua endpoint berikut membutuhkan middleware `auth:sanctum`.

## Endpoint Admin

### Dashboard dan upload

```http
GET /admin/dashboard
POST /admin/upload
```

Upload menggunakan `multipart/form-data` dengan field `image`.

### Tour

```http
GET    /admin/tours
POST   /admin/tours
GET    /admin/tours/{id}
PUT    /admin/tours/{id}
DELETE /admin/tours/{id}
```

### Destinasi

```http
GET    /admin/destinations
POST   /admin/destinations
GET    /admin/destinations/{id}
PUT    /admin/destinations/{id}
DELETE /admin/destinations/{id}
```

### Posts

```http
GET    /admin/posts
POST   /admin/posts
GET    /admin/posts/{id}
PUT    /admin/posts/{id}
DELETE /admin/posts/{id}
```

### Testimoni

```http
GET    /admin/testimonials
POST   /admin/testimonials
PUT    /admin/testimonials/{id}
DELETE /admin/testimonials/{id}
```

### Booking

```http
GET    /admin/bookings
GET    /admin/bookings/export
GET    /admin/bookings/{id}
PUT    /admin/bookings/{id}
DELETE /admin/bookings/{id}
```

Query daftar booking:

| Parameter | Tipe | Keterangan |
|---|---|---|
| `status` | string | `baru`, `dihubungi`, `selesai` |
| `q` | string | Cari WhatsApp, email, atau detail tujuan |
| `per_page` | integer | Jumlah data per halaman, default 20 |

Update booking hanya menerima:

```json
{
  "status": "dihubungi"
}
```

### Contact, about, dan settings

```http
GET    /admin/contacts
PUT    /admin/contacts/{id}
DELETE /admin/contacts/{id}
GET    /admin/about
PUT    /admin/about
GET    /admin/settings
PUT    /admin/settings
```

## Error dan troubleshooting

| Status | Arti umum |
|---|---|
| `401` | Token admin tidak ada atau tidak valid |
| `404` | Resource atau slug tidak ditemukan |
| `422` | Request tidak lolos validasi |
| `429` | Rate limit tercapai |
| `502` | Provider CashUP gagal diakses atau mengembalikan error |

Setelah mengubah `.env` Laravel:

```powershell
php artisan config:clear
php artisan cache:clear
```

Untuk gambar upload lokal:

```powershell
php artisan storage:link
```

Jangan commit `backend/.env` karena berisi credential CashUP, Mailtrap, database, dan application key.
