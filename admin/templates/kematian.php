<?php
/**
 * File Template: kematian.php
 * Catatan: File ini adalah template STANDAR, sehingga Kop Surat dan Tanda Tangan 
 * otomatis disuntikkan oleh sistem utama (cetak_surat.php / lihat_surat.php).
 */

// =========================================================================
// 1. SINKRONISASI SUMBER DATA
// =========================================================================
$input_data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    $input_data = $_POST;
} else {
    $sumber_db = $data_dinamis ?? ($data_surat ?? '');
    if (is_string($sumber_db)) {
        $input_data = json_decode($sumber_db, true) ?: [];
    } elseif (is_array($sumber_db)) {
        $input_data = $sumber_db;
    }
}

// Fungsi Pengecekan Kosong: Jika input kosong (""), beri titik-titik
function cek_kosong($data, $fallback) {
    return (!isset($data) || trim($data) === '') ? $fallback : htmlspecialchars(trim($data));
}

// =========================================================================
// 2. EKSTRAKSI DATA ALMARHUM & KEMATIAN
// =========================================================================
// Data Jenazah (Mengambil dari Identitas Warga Utama)
$nama_alm = cek_kosong($warga['nama_lengkap'] ?? '', '.......................................................');
$nik_alm = cek_kosong($warga['nik'] ?? '', '.......................................................');
$jk_alm = cek_kosong($warga['jenis_kelamin'] ?? '', '..................................');
$tempat_lahir = cek_kosong($warga['tempat_lahir'] ?? '', '...................');
$tgl_lahir = (!empty($warga['tgl_lahir'])) ? format_tanggal_indo($warga['tgl_lahir']) : '...................';
$agama_alm = cek_kosong($warga['agama'] ?? '', '..................................');
$pekerjaan_alm = cek_kosong($warga['pekerjaan'] ?? '', '.......................................................');
$dusun_alm = cek_kosong($warga['nama_dusun'] ?? '', '..................................');

// Data Detail Kematian (Dari form inputan dinamis)
$tgl_meninggal = (!empty($input_data['dyn_tgl_meninggal'])) ? format_tanggal_indo($input_data['dyn_tgl_meninggal']) : '.......................................................';
$tempat_meninggal = cek_kosong($input_data['dyn_tempat_meninggal'] ?? '', '.......................................................');
$penyebab = cek_kosong($input_data['dyn_penyebab'] ?? '', '.......................................................');

// Nomor surat bisa berasal dari controller / cetak_surat.php atau kosong saat preview template saja.
$nomor_surat_lengkap = isset($nomor_surat_lengkap) && trim((string)$nomor_surat_lengkap) !== ''
    ? htmlspecialchars(trim((string)$nomor_surat_lengkap))
    : '.......................................................';
?>

<style>
    /* Styling khusus konten tengah (standar) */
    .judul-surat-std { text-align: center; margin-bottom: 25px; line-height: 1.3; }
    .judul-surat-std .judul { text-decoration: underline; font-weight: bold; font-size: 14pt; margin-bottom: 2px; }
    .judul-surat-std .nomor { font-weight: normal; font-size: 11pt; }
    
    .konten-std { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #000; text-align: justify; line-height: 1.5; }
    .konten-std p { text-indent: 40px; margin-top: 0; margin-bottom: 15px; }
    
    .tabel-identitas { width: 95%; margin-left: 40px; margin-bottom: 20px; border-collapse: collapse; line-height: 1.5; }
    .tabel-identitas td { vertical-align: top; padding: 4px 0; }
    .tabel-identitas td:first-child { width: 30%; }
    .tabel-identitas td:nth-child(2) { width: 3%; text-align: center; }
</style>

<!-- BAGIAN JUDUL SURAT -->
<div class="judul-surat-std">
    <div class="judul">SURAT KETERANGAN KEMATIAN</div>
    <div class="nomor">Nomor : <?= $nomor_surat_lengkap ?></div>
</div>

<!-- BAGIAN ISI SURAT -->
<div class="konten-std">
    <p>
        Yang bertanda tangan di bawah ini Kepala Desa Serage, Kecamatan Praya Barat Daya, Kabupaten Lombok Tengah, menerangkan dengan sebenarnya bahwa:
    </p>

    <!-- BIODATA ALMARHUM -->
    <table class="tabel-identitas">
        <tr>
            <td>Nama Lengkap</td><td>:</td>
            <td><strong><?= $nama_alm ?></strong></td>
        </tr>
        <tr>
            <td>NIK</td><td>:</td>
            <td><?= $nik_alm ?></td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td><td>:</td>
            <td><?= $jk_alm ?></td>
        </tr>
        <tr>
            <td>Tempat, Tgl. Lahir</td><td>:</td>
            <td><?= $tempat_lahir ?>, <?= $tgl_lahir ?></td>
        </tr>
        <tr>
            <td>Agama</td><td>:</td>
            <td><?= $agama_alm ?></td>
        </tr>
        <tr>
            <td>Pekerjaan</td><td>:</td>
            <td><?= $pekerjaan_alm ?></td>
        </tr>
        <tr>
            <td>Alamat Terakhir</td><td>:</td>
            <td>Dusun <?= $dusun_alm ?>, Desa Serage</td>
        </tr>
    </table>

    <p>
        Orang tersebut di atas adalah benar-benar warga kami yang telah <strong>Meninggal Dunia</strong> pada:
    </p>

    <!-- DETAIL KEMATIAN -->
    <table class="tabel-identitas">
        <tr>
            <td>Hari / Tanggal</td><td>:</td>
            <td><strong><?= $tgl_meninggal ?></strong></td>
        </tr>
        <tr>
            <td>Tempat Meninggal</td><td>:</td>
            <td><?= $tempat_meninggal ?></td>
        </tr>
        <tr>
            <td>Penyebab Kematian</td><td>:</td>
            <td><?= $penyebab ?></td>
        </tr>
    </table>

    <p style="margin-bottom: 30px;">
        Demikian Surat Keterangan Kematian ini kami buat dengan sebenarnya dan berdasarkan permohonan ahli waris, untuk dapat dipergunakan sebagaimana mestinya.
    </p>
</div>