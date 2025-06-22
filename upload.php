<?php
// Setel zona waktu untuk fungsi tanggal, jika diperlukan
date_default_timezone_set('Asia/Jakarta');

// HTML untuk memberikan umpan balik kepada pengguna
echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Status Unggahan</title><style>body{font-family:sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}.message{padding:2rem;border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,0.1);text-align:center;}.success{background-color:#d4edda;color:#155724;}.error{background-color:#f8d7da;color:#721c24;}a{color:#0056b3;text-decoration:none;margin-top:1rem;display:inline-block;}</style></head><body>';

$pesan = '';
$statusClass = 'error'; // kelas CSS default adalah error

// 1. Periksa apakah form sudah di-submit dengan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Periksa apakah ada file yang diunggah dan tidak ada error saat proses unggah
    if (isset($_FILES["berkas"]) && $_FILES["berkas"]["error"] == UPLOAD_ERR_OK) {
        
        // 3. Tentukan direktori tujuan menggunakan path absolut
        // $_SERVER['DOCUMENT_ROOT'] akan mengarah ke 'C:/xampp/htdocs'
        $direktoriTujuan = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';

        // 4. [SOLUSI ERROR] Periksa apakah direktori tujuan sudah ada. Jika tidak, buat direktorinya.
        // Fungsi is_dir() memeriksa apakah sebuah path adalah direktori.
        if (!is_dir($direktoriTujuan)) {
            // mkdir() membuat direktori baru.
            // Parameter 0777 memberikan izin akses penuh (umum untuk development).
            // Parameter 'true' memungkinkan pembuatan direktori bersarang (contoh: /uploads/images/).
            if (!mkdir($direktoriTujuan, 0777, true)) {
                $pesan = 'Gagal membuat direktori unggahan.';
            }
        }
        
        // Lanjutkan hanya jika direktori berhasil dibuat atau sudah ada
        if (is_dir($direktoriTujuan)) {
            // 5. Amankan nama file untuk mencegah serangan path traversal
            // basename() hanya akan mengambil bagian nama file dari sebuah path
            $namaFileAsli = basename($_FILES["berkas"]["name"]);
            $fileTujuan = $direktoriTujuan . $namaFileAsli;
            
            // Opsional: Tambahkan pengecekan untuk menghindari penimpaan file
            if (file_exists($fileTujuan)) {
                $pesan = "Maaf, file dengan nama <strong>" . htmlspecialchars($namaFileAsli) . "</strong> sudah ada. Silakan ganti nama file Anda.";
            } else {
                // 6. Pindahkan file dari lokasi sementara ke direktori tujuan
                if (move_uploaded_file($_FILES["berkas"]["tmp_name"], $fileTujuan)) {
                    $pesan = "File <strong>" . htmlspecialchars($namaFileAsli) . "</strong> berhasil diunggah.";
                    $statusClass = 'success';
                } else {
                    $pesan = "Maaf, terjadi kesalahan saat memindahkan file Anda.";
                }
            }
        }

    } else {
        // Handle berbagai kemungkinan error unggahan
        switch ($_FILES["berkas"]["error"]) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $pesan = "Ukuran file melebihi batas yang diizinkan.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $pesan = "Tidak ada file yang dipilih untuk diunggah.";
                break;
            default:
                $pesan = "Terjadi kesalahan yang tidak diketahui saat mengunggah.";
                break;
        }
    }
} else {
    // Jika halaman diakses langsung tanpa melalui form POST
    $pesan = "Akses tidak diizinkan. Silakan unggah melalui form.";
}

// Tampilkan pesan status akhir kepada pengguna
echo "<div class='message " . $statusClass . "'>" . $pesan . "<br><a href='index.html'>Kembali ke Form</a></div>";
echo "</body></html>";
?>
