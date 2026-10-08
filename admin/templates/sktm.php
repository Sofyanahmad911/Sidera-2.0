<?php
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

// Data Warga / Pemohon
$nama = cek_kosong($warga['nama_lengkap'] ?? '', '.......................................................');
$nik = cek_kosong($warga['nik'] ?? '', '.......................................................');
$jk = cek_kosong($warga['jenis_kelamin'] ?? '', '..................................');
$tempat_lahir = cek_kosong($warga['tempat_lahir'] ?? '', '...................');
$tgl_lahir = (!empty($warga['tgl_lahir'])) ? date('d-m-Y', strtotime($warga['tgl_lahir'])) : '...................';
$agama = cek_kosong($warga['agama'] ?? '', '..................................');
$pekerjaan = cek_kosong($warga['pekerjaan'] ?? '', '.......................................................');

$alamat_dusun = $warga['nama_dusun'] ?? '';
if (!empty($alamat_dusun)) {
    $alamat_lengkap = $alamat_dusun . ' Dusun ' . $alamat_dusun . ' Desa Serage Kec. Praya Barat Daya Kab. Lombok Tengah.';
} else {
    $alamat_lengkap = '..................................................................................................';
}

// Data Dinamis
$keperluan = cek_kosong($input_data['keperluan'] ?? '', '.......................................................');
$nama_sasaran = strtoupper(cek_kosong($input_data['dyn_nama_sasaran'] ?? '', '.......................................................'));
$umur_sasaran = cek_kosong($input_data['dyn_umur_sasaran'] ?? '', '........');
$jk_sasaran = strtoupper(cek_kosong($input_data['dyn_jk_sasaran'] ?? '', '........................'));
$ttl_sasaran = cek_kosong($input_data['dyn_ttl_sasaran'] ?? '', '..................................................');
$sekolah_sasaran = cek_kosong($input_data['dyn_sekolah_sasaran'] ?? '', '.......................................................................................................');
?>

<style>
    .judul-surat-std { text-align: center; margin-bottom: 20px; line-height: 1.2; font-family: 'Times New Roman', Times, serif; }
    .judul-surat-std .judul { text-decoration: underline; font-weight: bold; font-size: 13pt; margin-bottom: 2px; text-transform: uppercase; }
    .judul-surat-std .nomor { font-weight: normal; font-size: 11pt; }
    
    /* Mengurangi jarak antar baris dari 1.5 menjadi 1.15 */
    .konten-std { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #000; line-height: 1.15; text-align: justify; }
    
    /* Mengurangi jarak antar paragraf (margin-bottom) agar lebih rapat */
    .konten-std p { margin-top: 0; margin-bottom: 5px; } 
    .paragraf-indent { text-indent: 40px; }
    
    .tabel-identitas { width: 95%; margin-left: 0px; margin-bottom: 8px; border-collapse: collapse; line-height: 1.15; }
    .tabel-identitas td { vertical-align: top; padding: 1px 0; } /* Padding baris tabel diperkecil */
    .tabel-identitas td:first-child { width: 30%; }
    .tabel-identitas td:nth-child(2) { width: 3%; text-align: center; }

    /* Mengurangi jarak atas-bawah pada nama anak */
    .nama-sasaran { text-align: center; font-weight: bold; font-size: 13pt; margin: 4px 0; letter-spacing: 1px; }
</style>

<div class="judul-surat-std">
    <div class="judul">SURAT KETERANGAN TIDAK MAMPU</div>
    <div class="nomor">Nomor : <?= htmlspecialchars($nomor_surat_lengkap ?? '...........................................') ?></div>
</div>

<div class="konten-std">
    <p class="paragraf-indent">Yang bertanda tangan di bawah ini Kepala Desa Serage Kecamatan Praya Barat Daya Kabupaten Lombok Tengah, menerangkan dengan sebenarnya bahwa:</p>

    <table class="tabel-identitas">
        <tr>
            <td>Nama</td><td>:</td><td><?= $nama ?></td>
        </tr>
        <tr>
            <td>NIK</td><td>:</td><td><?= $nik ?></td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td><td>:</td><td><?= $jk ?></td>
        </tr>
        <tr>
            <td>Tempat/Tgl Lahir</td><td>:</td><td><?= $tempat_lahir ?>, <?= $tgl_lahir ?></td>
        </tr>
        <tr>
            <td>Agama</td><td>:</td><td><?= $agama ?></td>
        </tr>
        <tr>
            <td>Pekerjaan</td><td>:</td><td><?= $pekerjaan ?></td>
        </tr>
        <tr>
            <td>Alamat</td><td>:</td><td><?= $alamat_lengkap ?></td>
        </tr>
    </table>

    <p class="paragraf-indent">
        Bahwa yang namanya tersebut diatas adalah warga Desa Serage Kecamatan Praya Barat Daya Kabupaten Lombok Tengah. Dengan sepengetahuan kami dan sesuai data yang ada di kantor Desa orang tersebut diatas memang benar Keluarga Kurang Mampu / ekonomi lemah. Surat keterangan ini diminta secara langsung oleh yang bersangkutan guna untuk <?= $keperluan ?> Bernama:
    </p>

    <div class="nama-sasaran">
        <?= $nama_sasaran ?>
    </div>

    <!-- Menghilangkan margin kiri-kanan berlebih agar sejajar dengan teks atasnya -->
    <p style="margin-bottom: 12px;">
        Umur <?= $umur_sasaran ?> tahun, Jenis kelamin <?= $jk_sasaran ?> Tempat Tanggal Lahir <?= $ttl_sasaran ?> Yang akan sekolah/ kuliah di <?= $sekolah_sasaran ?>.
    </p>

    <p class="paragraf-indent">
        Demikian surat keterangan ini dibuat dengan sebenarnya untuk yang bersangkutan dan kiranya dapat dipergunakan seperlunya.
    </p>
</div>