<?php
session_start();
require_once '../config/koneksi.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_logged_in'])) { die("Akses ditolak."); }

$id_surat = intval($_GET['id'] ?? 0);

// Ambil data transaksi surat beserta data penduduk (menggunakan LEFT JOIN untuk mengakomodasi surat manual)
$stmt = $koneksi->prepare("
    SELECT t.*, p.nik, p.nama_lengkap, p.tempat_lahir, p.tgl_lahir, p.jenis_kelamin, p.pekerjaan, p.nama_dusun, p.rt, p.rw, p.agama, p.status_perkawinan 
    FROM transaksi_surat t 
    LEFT JOIN penduduk p ON t.penduduk_id = p.id 
    WHERE t.id = ? LIMIT 1
");
$stmt->bind_param("i", $id_surat);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif; color:red;'><h3>Arsip surat tidak ditemukan di database.</h3></div>");
}

$surat = $result->fetch_assoc();
$stmt->close();

// Parse data dinamis JSON
$data_dinamis = json_decode($surat['data_dinamis'], true);
$jenis_surat  = $data_dinamis['jenis'] ?? 'SKTM';
$keperluan    = $data_dinamis['keperluan'] ?? '-';
$is_manual    = $data_dinamis['is_manual'] ?? false;

// Tentukan data warga (jika manual ambil dari json, jika tidak ambil dari hasil join database)
$warga = [];
if ($is_manual && isset($data_dinamis['data_warga'])) {
    $warga = $data_dinamis['data_warga'];
} else {
    $warga = $surat;
}

$nomor_surat_lengkap = $surat['nomor_surat'];

// Data Kepala Desa
$q_kades = $koneksi->query("SELECT nama_pejabat FROM struktur_organisasi WHERE jabatan = 'kepala desa' LIMIT 1");
$data_kades = $q_kades->fetch_assoc();
$nama_kades = $data_kades ? strtoupper($data_kades['nama_pejabat']) : "HERMAN YADI, S. Adm";
$nik_kades = "5202041234560001"; 
$jabatan_kades = "KEPALA DESA";

// =========================================================================================
// RENDER TEMPLATE BERDASARKAN JENIS SURAT
// =========================================================================================
$nama_file_template = str_replace(' ', '_', strtolower($jenis_surat)) . '.php';
$template_file = "templates/" . $nama_file_template;

if (!file_exists($template_file)) {
    die("<div style='padding:20px; background:#fff; font-family:sans-serif; color:#ef4444;'><b>Error:</b> File template <b>$nama_file_template</b> tidak ditemukan.</div>");
}

ob_start();
include $template_file;
$html_isi_surat = ob_get_clean();

// =========================================================================================
// LOGIKA PENGECUALIAN KOP & TANDA TANGAN GANDA
// =========================================================================================
$path_logo = BASE_URL . '/assets/img/logo.png';

$kop_surat = '
<div style="border-bottom: 3px solid #000; padding-bottom: 2px; margin-bottom: 20px;">
    <div style="border-bottom: 1px solid #000; padding-bottom: 10px; display: flex; align-items: center;">
        <div style="width: 80px; margin-right: 5px;">
            <img src="'.$path_logo.'" alt="Logo Kabupaten" style="width: 100%; height: auto;" onerror="this.style.display=\'none\'">
        </div>
        <div style="flex: 1; text-align: center; padding-right: 100px;">
            <h3 style="margin: 0; font-size: 13pt; font-weight: normal;">PEMERINTAH KABUPATEN LOMBOK TENGAH</h3>
            <h3 style="margin: 0; font-size: 13pt; font-weight: normal;">KECAMATAN PRAYA BARAT DAYA</h3>
            <h1 style="margin: 0; font-size: 14pt; font-weight: bold;">DESA SERAGE</h1>
        </div>
    </div>
</div>';

$ttd_surat = '
<div style="width: 250px; float: right; text-align: center; margin-top: 40px;">
    <div>Serage, '.format_tanggal_indo($surat['tgl_terbit']).'</div>
    <div>Kepala Desa Serage</div>
    <div style="margin-top: 90px; font-weight: bold;">(<span style="text-decoration: underline;">'.$nama_kades.'</span>)</div>
</div>
<div style="clear: both;"></div>';

// Jika surat adalah jenis kustom (punya kop & ttd sendiri), jangan suntikkan lagi dari luar
$template_custom = ['warisan.php', 'hibah.php', 'jual_beli.php'];
$dokumen_final = '';

if (in_array($nama_file_template, $template_custom)) {
    // Inject nama Kades ke dalam placeholder template (jika belum tergantikan di template aslinya)
    $html_isi_surat = str_replace('[NAMA_KADES]', $nama_kades, $html_isi_surat);
    $dokumen_final = $html_isi_surat;
} else {
    $dokumen_final = $kop_surat . $html_isi_surat . $ttd_surat;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Arsip Surat - <?= htmlspecialchars($nomor_surat_lengkap) ?></title>
    <style>
        /* KONTROL MARGIN KERTAS SECARA MANUAL DI SINI */
        @page { 
            size: F4 portrait; /* Menggunakan ukuran F4 agar sama dengan cetak_surat */
            margin-top: 1.5cm; /* Mengatur margin atas */
            margin-bottom: 2cm;
            margin-left: 2.5cm;
            margin-right: 2.5cm;
        }
        
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 11pt; 
            color: #000; 
            line-height: 1.5; 
            background: #525659; 
            margin: 0; 
            padding: 20px; 
            display: flex; 
            justify-content: center; 
        }
        
        .kertas { 
            background: #fff; 
            width: 21.5cm; /* Lebar standar F4/Folio */
            min-height: 33cm; 
            padding: 2cm; 
            box-sizing: border-box; 
            box-shadow: 0 0 10px rgba(0,0,0,0.5); 
            margin: 0 auto; 
        }

        /* Styling elemen standar yang digunakan template lama */
        .judul-surat { text-align: center; margin-bottom: 20px; line-height: 1.3; }
        .judul-surat .judul { text-decoration: underline; font-weight: bold; font-size: 14pt; margin-bottom: 2px; }
        .judul-surat .nomor { font-weight: normal; font-size: 11pt; }
        .tabel-identitas { width: 100%; margin-left: 0; margin-bottom: 15px; border-collapse: collapse; }
        .tabel-identitas td { vertical-align: top; padding: 2px 0; }
        .paragraf-indent { text-indent: 40px; text-align: justify; margin-bottom: 15px; line-height: 1.5; }
        
        .btn-print { position: fixed; bottom: 30px; right: 30px; padding: 15px 30px; background: #1a6f76; color: white; font-weight: bold; border-radius: 8px; cursor: pointer; border: none; font-size: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); z-index: 100;}
        .btn-print:hover { background: #13555b; }
        
        @media print { 
            body { background: none; display: block; padding: 0; } 
            .kertas { box-shadow: none; margin: 0; padding: 0; width: 100%; min-height: auto; } 
            .no-print { display: none; } 
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="no-print btn-print">
        <i class="fa-solid fa-print"></i> Cetak Ulang Dokumen
    </button>

    <div class="kertas">
        <?= $dokumen_final ?>
    </div>
</body>
</html>