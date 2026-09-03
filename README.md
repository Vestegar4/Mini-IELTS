# Mini IELTS Speaking Evaluation

Sebuah aplikasi web sederhana (Full-stack) untuk latihan IELTS Speaking. Aplikasi ini memungkinkan pengguna untuk memilih topik pertanyaan, mengirimkan jawaban teks, dan mendapatkan evaluasi instan (Band Score, Strengths, dan Improvements) menggunakan integrasi dari **Google Gemini AI**.

## Fitur Utama
- **Backend API:** RESTful API menggunakan Laravel untuk mengelola data soal dan histori evaluasi.
- **AI Integration:** Terintegrasi dengan Google Gemini AI (versi fallback/stabil) untuk mengevaluasi jawaban bahasa Inggris.
- **Graceful Fallback:** Sistem akan mengembalikan *mock response* secara otomatis jika server AI sedang *overloaded* (500).
- **Frontend Dashboard:** Antarmuka interaktif menggunakan Vue.js dan Tailwind CSS (Vite).
- **Automated Testing:** Feature test menggunakan HTTP Mocking untuk menguji logika evaluasi tanpa memerlukan koneksi internet.

## Tech Stack
- **Backend:** Laravel 12, PHP 8.5
- **Frontend:** Vue.js 3 (Composition API), Tailwind CSS
- **HTTP Client:** Guzzle / Laravel HTTP Client
- **AI Model:** Google Gemini (gemini-3.5-flash)
- **Database:** SQLite / MySQL

---

## 🗄️ Skema Database (Relasional)

Aplikasi ini menggunakan dua tabel utama dengan relasi **One-to-Many**:

### 1. Tabel `questions`
Menyimpan daftar master pertanyaan IELTS Speaking.
- `id` (Primary Key, BigInt)
- `part` (Integer) - *Bagian tes IELTS (mis. Part 1, 2, atau 3)*
- `topic` (String) - *Topik pembahasan (mis. Work or Studies)*
- `prompt` (Text) - *Pertanyaan yang diajukan*
- `created_at`, `updated_at` (Timestamps)

### 2. Tabel `attempts`
Menyimpan riwayat jawaban dari pengguna beserta hasil evaluasi dari AI.
- `id` (Primary Key, BigInt)
- `question_id` (Foreign Key -> `questions.id`)
- `answer` (Text) - *Jawaban yang dikirim pengguna*
- `band_score` (Float) - *Estimasi skor IELTS dari AI*
- `strengths` (Text) - *Poin kekuatan dari jawaban pengguna*
- `improvements` (Text) - *Saran perbaikan dari AI*
- `created_at`, `updated_at` (Timestamps)

---

## Cara Instalasi & Menjalankan Aplikasi

Ikuti langkah-langkah berikut untuk menjalankan project ini di komputer lokal:

1. **Clone Repository**
   ```bash
   git clone <url-repository>
   cd mini-ielts

2. **Install Dependensi Backend & Frontend**
    ```bash
    composer install
    npm install

3. **Konfigurasi Environment**
    ```bash
    cp .env.example .env
    php artisan key:generate
    *Bukan file .env lalu tambahkan API Gemini milikmu dibagian bawah:
    GEMINI_API= masukkan API key mu disini tanpa "".

4. **Migrasi dan Seeding Database**
    ```bash
    php artisan migrate --seed

5. **Jalankan Aplikasi**
    membutuhkan 2 terminal tambahan
    ```bash
    npm run dev
    php artisan serve

6. Pengujian (Automated Testing)

Project ini dilengkapi dengan Feature Testing untuk memastikan titik API dan logika database berjalan normal. Panggilan ke API Gemini di-mock menggunakan Http::fake() sehingga pengujian berjalan dalam hitungan milidetik tanpa memakan kuota internet.

Jalankan perintah ini di terminal:
Bash

php artisan test

7. Dokumentasi API (Postman)

Dokumentasi lengkap mengenai endpoint API, struktur request, dan contoh response telah disediakan dalam format Postman Collection.

    Buka aplikasi Postman.

    Klik tombol Import.

    Pilih file postman_collection.json yang terdapat di root direktori project ini.
