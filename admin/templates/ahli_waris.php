<?php
/**
 * File Template: ahli_waris.php
 * Catatan: Ini adalah template standar. Kop dan TTD Kepala Desa disuntikkan secara otomatis.
 */

// =========================================================================
// 1. SINKRONISASI SUMBER DATA & FUNGSI TITIK-TITIK
// =========================================================================
$input_data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    $input_data = $_POST;
} else {
    $sumber_db = $data_dinamis ?? ($data_surat ?? '');
    $input_data = is_string($sumber_db) ? (json_decode($sumber_db, true) ?: []) : (is_array($sumber_db) ? $sumber_db : []);
}

function cek_kosong($data, $fallback) {
    return (!isset($data) || trim($data) === '') ? $fallback : htmlspecialchars(trim($data));
}

// =========================================================================
// 2. EKSTRAKSI VARIABEL FORM DINAMIS (DENGAN FALLBACK TITIK-TITIK)
// =========================================================================
// Biodata Pemohon (Saksi Utama Keluarga)
$nama_pemohon = cek_kosong($warga['nama_lengkap'] ?? ($input_data['nama_lengkap'] ?? ''), '.......................................................');
$nik_pemohon = cek_kosong($warga['nik'] ?? '', '.......................................................');
$tempat_lahir_pemohon = cek_kosong($warga['tempat_lahir'] ?? '', '.........................');
$tgl_lahir_pemohon = (!empty($warga['tgl_lahir'])) ? format_tanggal_indo($warga['tgl_lahir']) : '.........................';
$pekerjaan_pemohon = cek_kosong($warga['pekerjaan'] ?? '', '.......................................................');
$alamat_pemohon = cek_kosong($warga['nama_dusun'] ?? '', '.......................................................');

// Data Almarhum
$nama_alm = cek_kosong($input_data['dyn_nama_alm'] ?? '', '.......................................................');
$tgl_meninggal_alm = (!empty($input_data['dyn_tgl_meninggal_alm'])) ? format_tanggal_indo($input_data['dyn_tgl_meninggal_alm']) : '.......................................';
$tempat_meninggal_alm = cek_kosong($input_data['dyn_tempat_meninggal_alm'] ?? '', '.......................................................');

$keperluan = cek_kosong($input_data['keperluan'] ?? '', '................................................................................');
$jumlah_ahli_waris = !empty($input_data['jumlah_ahli_waris']) ? (int)$input_data['jumlah_ahli_waris'] : 1;
?>

<style>
    /* STYLING DIRAPATKAN */
    .judul-surat-std { text-align: center; margin-bottom: 20px; line-height: 1.2; }
    .judul-surat-std .judul { text-decoration: underline; font-weight: bold; font-size: 13pt; margin-bottom: 2px; }
    .judul-surat-std .nomor { font-weight: normal; font-size: 11pt; }
    
    .konten-std { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #000; }
    .konten-std p { text-align: justify; text-indent: 40px; margin-top: 0; margin-bottom: 8px; line-height: 1.15; }
    
    .tabel-identitas { width: 95%; margin-left: 40px; margin-bottom: 8px; border-collapse: collapse; line-height: 1.15; }
    .tabel-identitas td { vertical-align: top; padding: 2px 0; }
    .tabel-identitas td:first-child { width: 28%; }
    .tabel-identitas td:nth-child(2) { width: 3%; text-align: center; }

    /* TABEL DAFTAR AHLI WARIS BERSYARAT */
    .tabel-waris { width: 95%; margin: 10px auto 15px auto; border-collapse: collapse; text-align: center; line-height: 1.15; }
    .tabel-waris th, .tabel-waris td { border: 1px solid #000; padding: 6px; }
    .tabel-waris th { font-weight: bold; background: #f8fafc; }
</style>

<div class="judul-surat-std">
    <div class="judul">SURAT KETERANGAN AHLI WARIS</div>
    <div class="nomor">Nomor : <?= htmlspecialchars($nomor_surat_lengkap ?? '.......................................................', ENT_QUOTES, 'UTF-8') ?></div>
</div>

<div class="konten-std">
    <p>Yang bertanda tangan di bawah ini Kepala Desa Serage, Kecamatan Praya Barat Daya, Kabupaten Lombok Tengah, menerangkan dengan sebenarnya bahwa:</p>
    
    <table class="tabel-identitas">
        <tr><td>Nama Lengkap</td><td>:</td><td><strong><?= $nama_pemohon ?></strong></td></tr>
        <tr><td>NIK</td><td>:</td><td><?= $nik_pemohon ?></td></tr>
        <tr><td>Tempat, Tgl Lahir</td><td>:</td><td><?= $tempat_lahir_pemohon ?>, <?= $tgl_lahir_pemohon ?></td></tr>
        <tr><td>Pekerjaan</td><td>:</td><td><?= $pekerjaan_pemohon ?></td></tr>
        <tr><td>Alamat</td><td>:</td><td>Dusun <?= $alamat_pemohon ?>, Desa Serage</td></tr>
    </table>

    <p>Berdasarkan keterangan yang bersangkutan dan saksi-saksi, bahwa nama tersebut di atas adalah benar merupakan salah satu ahli waris yang sah dari Almarhum/ah:</p>

    <table class="tabel-identitas">
        <tr><td>Nama Almarhum/ah</td><td>:</td><td><strong><?= $nama_alm ?></strong></td></tr>
        <tr><td>Tanggal Meninggal</td><td>:</td><td><?= $tgl_meninggal_alm ?></td></tr>
        <tr><td>Tempat Meninggal</td><td>:</td><td><?= $tempat_meninggal_alm ?></td></tr>
    </table>

    <p>Adapun almarhum/ah tersebut semasa hidupnya telah menikah dan meninggalkan ahli waris yang sah sebanyak <strong><?= $jumlah_ahli_waris ?></strong> orang, dengan rincian sebagai berikut:</p>

    <table class="tabel-waris">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 44%;">Nama Lengkap</th>
                <th style="width: 20%;">Umur</th>
                <th style="width: 30%;">Status Hubungan</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i <= $jumlah_ahli_waris; $i++): 
                // Jika data form tabel waris kosong, akan dicetak titik-titik
                $n_aw = cek_kosong($input_data['dyn_aw_nama_'.$i] ?? '', '........................................');
                $u_aw = cek_kosong($input_data['dyn_aw_umur_'.$i] ?? '', '..............');
                $h_aw = cek_kosong($input_data['dyn_aw_hubungan_'.$i] ?? '', '.........................');
            ?>
            <tr>
                <td><?= $i ?></td>
                <td style="text-align: left; padding-left:10px;"><?= $n_aw ?></td>
                <td><?= $u_aw ?> Thn</td>
                <td><?= $h_aw ?></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <p>Surat Keterangan Ahli Waris ini dibuat dan diberikan untuk dipergunakan dalam keperluan: <strong><?= $keperluan ?></strong>.</p>
    
    <p style="margin-bottom: 25px;">Demikian surat keterangan ini kami buat dengan sebenarnya, untuk dapat dipergunakan sebagaimana mestinya dan agar pihak-pihak yang berkepentingan menjadi maklum.</p>
</div>