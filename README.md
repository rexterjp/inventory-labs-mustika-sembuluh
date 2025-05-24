
# Backend API untuk Sistem Inventori MS Labs

## Struktur Folder
```
backend/
├── config/
│   ├── database.php      # Konfigurasi database
│   └── cors.php          # Konfigurasi CORS
├── api/
│   ├── auth/
│   │   └── login.php     # Endpoint login
│   ├── jenis-barang/
│   │   └── index.php     # CRUD jenis barang
│   ├── satuan/
│   │   └── index.php     # CRUD satuan
│   ├── barang/
│   │   └── index.php     # CRUD data barang
│   ├── dashboard/
│   │   └── index.php     # Dashboard statistics
│   └── .htaccess         # Apache rewrite rules
└── README.md
```

## Konfigurasi Database
1. Buat database dengan nama: `mustikas_sembuluh_labs`
2. Import file `database_updated.sql`
3. Update kredential database di `config/database.php` jika diperlukan

## Deployment ke Web Hosting
1. Upload semua file di folder `backend/` ke folder `public_html/api/` di hosting
2. Pastikan PHP 7.4+ dan MySQL aktif
3. Pastikan mod_rewrite Apache aktif untuk .htaccess
4. Test endpoint: `https://yourdomain.com/api/dashboard/`

## Endpoint API
- `POST /api/auth/login` - Login user
- `GET /api/dashboard/` - Dashboard statistics
- `GET/POST/PUT/DELETE /api/jenis-barang/` - CRUD jenis barang
- `GET/POST/PUT/DELETE /api/satuan/` - CRUD satuan
- `GET/POST/PUT/DELETE /api/barang/` - CRUD data barang

## Default Login
- Username: `admin`
- Password: `admin123`

## CORS
Backend sudah dikonfigurasi untuk menerima request dari domain manapun. Untuk production, sesuaikan CORS di `config/cors.php`.
