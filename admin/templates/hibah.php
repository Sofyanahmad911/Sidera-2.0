<?php
/**
 * File Template: hibah.php
 * @var array $warga (Data Pihak Pertama / Pemohon)
 * @var string $nomor_surat_lengkap
 * @var array $data_surat (Data dinamis dari form inputan JS)
 */

// Memastikan $data_surat selalu terisi (fallback ke $_POST saat Live Preview)
$data_surat = !empty($data_surat) ? $data_surat : $_POST;

// Menangkap Array Data Pihak Kedua (Bisa satu, bisa banyak)
$nik_kedua_arr    = isset($data_surat['dyn_nik_kedua']) ? (array)$data_surat['dyn_nik_kedua'] : [];
$nama_kedua_arr   = isset($data_surat['dyn_nama_kedua']) ? (array)$data_surat['dyn_nama_kedua'] : [];
$umur_kedua_arr   = isset($data_surat['dyn_umur_kedua']) ? (array)$data_surat['dyn_umur_kedua'] : [];
$kerja_kedua_arr  = isset($data_surat['dyn_kerja_kedua']) ? (array)$data_surat['dyn_kerja_kedua'] : [];
$alamat_kedua_arr = isset($data_surat['dyn_alamat_kedua']) ? (array)$data_surat['dyn_alamat_kedua'] : [];

// Menghitung jumlah penerima (minimal 1)
$jumlah_penerima = max(1, count($nama_kedua_arr));

// Ekstraksi Data Tanah
$jenis_tanah = htmlspecialchars($data_surat['dyn_jenis_tanah'] ?? '');
$luas_angka = htmlspecialchars($data_surat['dyn_luas_angka'] ?? '');
$luas_huruf = htmlspecialchars($data_surat['dyn_luas_huruf'] ?? '');
$tahun_hibah = htmlspecialchars($data_surat['dyn_tahun_hibah'] ?? '');
$no_sppt = htmlspecialchars($data_surat['dyn_no_sppt'] ?? '');
$lokasi_tanah = htmlspecialchars($data_surat['dyn_lokasi_tanah'] ?? '');

$b_utara = htmlspecialchars($data_surat['dyn_batas_utara'] ?? '-');
$b_selatan = htmlspecialchars($data_surat['dyn_batas_selatan'] ?? '-');
$b_timur = htmlspecialchars($data_surat['dyn_batas_timur'] ?? '-');
$b_barat = htmlspecialchars($data_surat['dyn_batas_barat'] ?? '-');

$saksi_1 = htmlspecialchars($data_surat['dyn_saksi_1'] ?? '...........................');
$saksi_2 = htmlspecialchars($data_surat['dyn_saksi_2'] ?? '...........................');
?>

<div class="judul-surat">
    <div class="judul" style="text-decoration: underline;">SURAT PERNYATAAN HIBAH</div>
    <div class="nomor">Nomor : <?= $nomor_surat_lengkap ?></div>
</div>

<p style="margin-bottom: 10px; text-align: justify;">Yang bertanda tangan/cap jempol di bawah ini:</p>

<!-- PIHAK PERTAMA -->
<p style="margin-bottom: 5px;"><strong>1. PIHAK PERTAMA (Pemberi Hibah)</strong></p>
<table class="tabel-identitas" style="margin-bottom: 15px;">
    <tr><td style="width: 25%;">Nama Lengkap</td><td style="width: 2%;">:</td><td><strong><?= htmlspecialchars($warga['nama_lengkap'] ?? '') ?></strong></td></tr>
    <tr><td>NIK</td><td>:</td><td><?= htmlspecialchars($warga['nik'] ?? '') ?></td></tr>
    <tr><td>Agama</td><td>:</td><td><?= htmlspecialchars($warga['agama'] ?? 'ISLAM') ?></td></tr>
    <tr><td>Umur</td><td>:</td><td><?= isset($warga['tgl_lahir']) ? hitung_umur($warga['tgl_lahir']) : '...' ?> Tahun</td></tr>
    <tr><td>Pekerjaan</td><td>:</td><td><?= htmlspecialchars($warga['pekerjaan'] ?? '') ?></td></tr>
    <tr><td>Alamat</td><td>:</td><td>Dusun <?= htmlspecialchars($warga['nama_dusun'] ?? '') ?> RT <?= htmlspecialchars($warga['rt'] ?? '') ?>/RW <?= htmlspecialchars($warga['rw'] ?? '') ?> Desa Serage</td></tr>
</table>
<p class="paragraf-indent" style="margin-bottom: 15px;">Dalam hal ini bertindak untuk dan atas nama diri sendiri selanjutnya disebut <strong>PIHAK PERTAMA</strong>.</p>

<!-- PIHAK KEDUA (BISA LEBIH DARI SATU) -->
<p style="margin-bottom: 5px;"><strong>2. PIHAK KEDUA (Penerima Hibah)</strong></p>

<?php for ($i = 0; $i < $jumlah_penerima; $i++): ?>
    <?php 
        $nama = htmlspecialchars($nama_kedua_arr[$i] ?? '');
        $nik = htmlspecialchars($nik_kedua_arr[$i] ?? '');
        $umur = htmlspecialchars($umur_kedua_arr[$i] ?? '');
        $kerja = htmlspecialchars($kerja_kedua_arr[$i] ?? '');
        $alamat = htmlspecialchars($alamat_kedua_arr[$i] ?? '');
    ?>
    
    <?php if ($jumlah_penerima > 1): ?>
        <p style="margin-bottom: 3px; font-weight: 600; font-size: 11pt; padding-left: 20px;">2.<?= $i+1 ?>. Penerima <?= $i+1 ?></p>
    <?php endif; ?>
    
    <table class="tabel-identitas" style="margin-bottom: <?= ($i == $jumlah_penerima - 1) ? '15px' : '5px' ?>; <?= ($jumlah_penerima > 1) ? 'margin-left: 20px; width: 95%;' : '' ?>">
        <tr><td style="width: 25%;">Nama Lengkap</td><td style="width: 2%;">:</td><td><strong><?= $nama ?></strong></td></tr>
        <tr><td>NIK</td><td>:</td><td><?= $nik ?></td></tr>
        <tr><td>Agama</td><td>:</td><td>ISLAM</td></tr>
        <tr><td>Umur</td><td>:</td><td><?= $umur ?> Tahun</td></tr>
        <tr><td>Pekerjaan</td><td>:</td><td><?= $kerja ?></td></tr>
        <tr><td>Alamat</td><td>:</td><td><?= $alamat ?></td></tr>
    </table>
<?php endfor; ?>

<p class="paragraf-indent" style="margin-bottom: 15px;">Dalam hal ini bertindak untuk dan atas nama diri sendiri selanjutnya disebut <strong>PIHAK KEDUA</strong>.</p>

<p style="margin-bottom: 10px; text-align: justify;">Kami PIHAK PERTAMA dan PIHAK KEDUA di atas menyatakan dengan sebenarnya bahwa:</p>
<ol style="text-align: justify; padding-left: 20px; margin-bottom: 15px;">
    <li style="margin-bottom: 8px;">
        Kami PIHAK PERTAMA telah menghibah/memberikan kepada PIHAK KEDUA sebidang Tanah <strong><?= $jenis_tanah ?></strong> seluas <strong><?= $luas_angka ?> M&sup2;</strong> (<em><?= $luas_huruf ?> Meter Persegi</em>) pada tahun <strong><?= $tahun_hibah ?></strong> sesuai SPPT Nomor <strong><?= $no_sppt ?></strong>. Adapun tanah tersebut terletak di <?= $lokasi_tanah ?>, Desa Serage, Kecamatan Praya Barat Daya, Kabupaten Lombok Tengah, Provinsi Nusa Tenggara Barat dengan batas-batas sebagai berikut:
        <table style="width: 90%; margin-top: 5px; margin-left: 10px; line-height: 1.5;">
            <tr>
                <td style="width: 15%;">Utara</td><td style="width: 35%;">: <?= $b_utara ?></td>
                <td style="width: 15%;">Timur</td><td style="width: 35%;">: <?= $b_timur ?></td>
            </tr>
            <tr>
                <td>Selatan</td><td>: <?= $b_selatan ?></td>
                <td>Barat</td><td>: <?= $b_barat ?></td>
            </tr>
        </table>
    </li>
    <li style="margin-bottom: 8px;">PIHAK KEDUA menyatakan telah menerima penghibahan/pemberian/penyerahan sebidang tanah <?= $jenis_tanah ?> seluas <?= $luas_angka ?> M&sup2; (<?= $luas_huruf ?> Meter Persegi) sejak tahun <?= $tahun_hibah ?>.</li>
    <li>Kami PIHAK PERTAMA menyatakan bahwa hibah/pemberian tanah bersifat permanen/tetap sehingga kami PIHAK PERTAMA tidak berhak lagi atas tanah tersebut dan selanjutnya tanah tersebut menjadi milik sah PIHAK KEDUA serta kami PIHAK PERTAMA menyatakan setuju/tidak keberatan apabila tanah tersebut disertipikatkan menjadi Hak Milik PIHAK KEDUA.</li>
</ol>

<p style="text-align: justify;">Demikian kami buat pernyataan ini dengan sebenar-benarnya dan untuk dapat dipergunakan sebagaimana mestinya.</p>

<!-- TANDA TANGAN PIHAK -->
<table style="width: 100%; margin-top: 30px; text-align: center;">
    <tr>
        <td style="width: 40%; vertical-align: top;">
            <strong>PIHAK KEDUA</strong><br><br><br>
            <?php for ($i = 0; $i < $jumlah_penerima; $i++): ?>
                <div style="margin-bottom: 25px;">
                    ( <strong><?= htmlspecialchars($nama_kedua_arr[$i] ?? '....................') ?></strong> )
                </div>
            <?php endfor; ?>
        </td>
        <td style="width: 20%;"></td>
        <td style="width: 40%; vertical-align: top;">
            <strong>PIHAK PERTAMA</strong><br>
            <span style="font-size: 11px; color: #64748b; display: block; margin: 10px 0; border: 1px dashed #cbd5e1; padding: 5px; width: 80px; margin-left: auto; margin-right: auto;">Materai<br>Rp. 10.000</span><br><br>
            ( <strong><?= htmlspecialchars($warga['nama_lengkap'] ?? '....................') ?></strong> )
        </td>
    </tr>
</table>

<!-- SAKSI & KELUARGA -->
<table style="width: 100%; margin-top: 10px; font-size: 14px;">
    <tr>
        <td style="width: 50%; vertical-align: top;">
            <strong>SAKSI-SAKSI:</strong>
            <ul style="list-style-type: none; padding-left: 0; line-height: 2;">
                <li>1. <?= $saksi_1 ?> <span style="float: right; margin-right: 20px;">( .................... )</span></li>
                <li>2. <?= $saksi_2 ?> <span style="float: right; margin-right: 20px;">( .................... )</span></li>
            </ul>
        </td>
        <td style="width: 50%; vertical-align: top;">
            <strong>Persetujuan Keluarga:</strong>
            <ul style="list-style-type: none; padding-left: 0; line-height: 2;">
                <li>1. ........................................ <span style="float: right;">( .................. )</span></li>
                <li>2. ........................................ <span style="float: right;">( .................. )</span></li>
                <li>3. ........................................ <span style="float: right;">( .................. )</span></li>
                <li>4. ........................................ <span style="float: right;">( .................. )</span></li>
            </ul>
        </td>
    </tr>
</table>