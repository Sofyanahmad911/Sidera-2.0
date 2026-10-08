<?php
// ajax_get_penduduk.php
require_once 'config/koneksi.php';

// Pastikan balasan berupa JSON agar mudah dibaca Javascript
header('Content-Type: application/json');

// Gunakan parameter GET 'nik' sebagai keyword pencarian universal (bisa NIK atau KK)
if (!isset($_GET['nik']) || empty(trim($_GET['nik']))) {
    echo json_encode(['error' => 'Kunci pencarian (NIK/No KK) tidak diberikan.']);
    exit;
}

$keyword = $koneksi->real_escape_string(trim($_GET['nik']));

// SINKRONISASI: Menambahkan kolom nama_dusun, umur, kecamatan, kabupaten, dan provinsi ke dalam SELECT
$query = "SELECT id, no_kk, nik, nama_lengkap, tempat_lahir, tgl_lahir, umur, pekerjaan, rt, rw, nama_dusun, kel_desa, kecamatan, kabupaten, provinsi 
          FROM penduduk 
          WHERE nik = ? OR no_kk = ? LIMIT 1";

$stmt = $koneksi->prepare($query);

// Bind parameter dua kali ("ss") karena menggunakan dua placeholder (?)
$stmt->bind_param("ss", $keyword, $keyword);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Data penduduk dengan NIK / No KK tersebut tidak terdaftar di sistem.']);
}

$stmt->close();
?>