# 🏗️ TradeRitual Architecture Overview

Codebase ini menggunakan **Laravel** (PHP) dengan arsitektur **MVC (Model-View-Controller)**. Tampilannya (Frontend) menggunakan **Blade Templates**, **Tailwind CSS**, dan **Alpine.js**. Sistem autentikasinya menggunakan **Supabase**.

Berikut adalah penjelasan dan *link* langsung (bisa di-klik) ke file dan baris kodenya:

## 1. 🎨 Layout & Komponen Utama (Views)

Komponen UI utama dan *wrapper* (pembungkus) dari semua halaman ada di file layout master.

*   **[Bottom Navbar (Navigasi Bawah)](file:///d:/Trade_Journal/resources/views/layouts/app.blade.php#L120-L153)**
    Ini adalah kode untuk navbar yang muncul di bagian bawah layar (Home, Analytics, Calendar, AI Coach, Profile).
*   **[Master Layout / Template Utama](file:///d:/Trade_Journal/resources/views/layouts/app.blade.php#L1-L173)**
    Semua halaman di-*extend* dari file ini. File ini mengatur *setup* HTML, Tailwind CSS, Alpine.js, dan PWA.

## 2. 🗺️ Routing (Pengatur Jalan)

Semua URL atau halaman web diatur di dalam file *routing*.

*   **[Web Routes (web.php)](file:///d:/Trade_Journal/routes/web.php#L30-L98)**
    Di sini kamu bisa melihat URL mana yang akan memanggil Controller apa. Misalnya URL `/dashboard` memanggil `DashboardController`.

## 3. 🧠 Controllers (Logika Aplikasi)

Controller adalah penghubung antara Database (Model) dan Tampilan (View).

*   **[Dashboard Controller](file:///d:/Trade_Journal/app/Http/Controllers/DashboardController.php)**
    Mengatur data apa saja yang ditampilkan di halaman beranda/Home.
*   **[Trade Controller](file:///d:/Trade_Journal/app/Http/Controllers/TradeController.php)**
    Logika untuk menambah, mengedit, atau melihat jurnal trading. *(Hanya bisa diakses jika user sudah mengisi ritual harian)*.
*   **[Ritual Controller](file:///d:/Trade_Journal/app/Http/Controllers/RitualController.php)**
    Logika untuk fitur *Pre-Market* dan *Post-Market Check-in*.
*   **[AI Coach Controller](file:///d:/Trade_Journal/app/Http/Controllers/AiCoachController.php)**
    Mengatur fitur obrolan atau *insight* dari AI Coach.
*   **[Analytics Controller](file:///d:/Trade_Journal/app/Http/Controllers/AnalyticsController.php)** & **[Performance Controller](file:///d:/Trade_Journal/app/Http/Controllers/PerformanceController.php)**
    Mengatur statistik, performa trading, dan tampilan kalender.
*   **[Account Transaction Controller](file:///d:/Trade_Journal/app/Http/Controllers/AccountTransactionController.php)**
    Mengatur logika saat user menambah transaksi (Deposit/Withdrawal) pada akun trading.

## 4. 🗄️ Models (Database)

Model merepresentasikan tabel di dalam database dan relasi antar datanya.

*   **[Trade Model](file:///d:/Trade_Journal/app/Models/Trade.php)**: Data setiap jurnal trading.
*   **[TradingAccount Model](file:///d:/Trade_Journal/app/Models/TradingAccount.php)**: Data akun trading milik user.
*   **[AccountTransaction Model](file:///d:/Trade_Journal/app/Models/AccountTransaction.php)**: Riwayat deposit/withdrawal.
*   **[DailyRitual Model](file:///d:/Trade_Journal/app/Models/DailyRitual.php)**: Data checklist emosi dan kesiapan sebelum/sesudah trading.
*   **[AiInsight Model](file:///d:/Trade_Journal/app/Models/AiInsight.php)**: Menyimpan riwayat *insight* dari AI.

## 5. 📱 Halaman / Pages Utama (Views)

Ini adalah file-file tampilan spesifik (Blade templates) untuk tiap fitur di aplikasi ini secara lengkap:

### 🏠 Main Pages
*   **[Landing Page / Welcome](file:///d:/Trade_Journal/resources/views/welcome.blade.php)**: Halaman awal / *landing page* sebelum login.
*   **[Dashboard](file:///d:/Trade_Journal/resources/views/dashboard.blade.php)**: Halaman beranda utama setelah user login.

### 🔐 Authentication (Login & Register)
*   **[Login Page](file:///d:/Trade_Journal/resources/views/auth/login.blade.php)**: Form untuk masuk ke akun.
*   **[Register Page](file:///d:/Trade_Journal/resources/views/auth/register.blade.php)**: Form pendaftaran akun baru.

### 🧘‍♂️ Rituals (Pre-Market & Post-Market)
*   **[Pre-Market Ritual](file:///d:/Trade_Journal/resources/views/ritual/create.blade.php)**: Form checklist emosi dan kesiapan sebelum mulai trading.
*   **[Post-Market Ritual](file:///d:/Trade_Journal/resources/views/ritual/post-market.blade.php)**: Form evaluasi performa setelah selesai trading harian.

### 📈 Trades (Jurnal Trading)
*   **[Create Trade](file:///d:/Trade_Journal/resources/views/trades/create.blade.php)**: Form untuk mencatat *entry*, *exit*, dan alasan trading baru.
*   **[Edit Trade](file:///d:/Trade_Journal/resources/views/trades/edit.blade.php)**: Form untuk mengubah data jurnal trading yang sudah ada.
*   **[Show Trade (Detail)](file:///d:/Trade_Journal/resources/views/trades/show.blade.php)**: Halaman untuk melihat detail lengkap dari satu transaksi trading.

### 🤖 Fitur AI & Analitik
*   **[AI Coach](file:///d:/Trade_Journal/resources/views/ai/index.blade.php)**: Halaman obrolan/interaksi dengan *AI Assistant* untuk mendapat *insight* psikologis.
*   **[Analytics Dashboard](file:///d:/Trade_Journal/resources/views/analytics/index.blade.php)**: Halaman grafik dan statistik rasio menang/kalah (win rate).
*   **[Performance Calendar](file:///d:/Trade_Journal/resources/views/performance/index.blade.php)**: Halaman kalender yang memetakan hari-hari profit (hijau) dan loss (merah).

### 👤 Profile & Manajemen Akun
*   **[Profile Settings](file:///d:/Trade_Journal/resources/views/profile/index.blade.php)**: Halaman pengaturan profil user dan manajemen *Trading Account* (tempat nambah modal atau *withdraw* saldo).

---
💡 **Cara pakai:** Kamu bisa klik *link* berwarna biru di atas untuk langsung membuka file dan menuju baris kode yang bersangkutan di dalam edito kamu!
