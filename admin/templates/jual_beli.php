<?php
/**
 * File Template: jual_beli.php
 * @var array $warga (Data Pihak Pertama / Penjual)
 * @var string $nomor_surat_lengkap
 * @var array $data_surat (Data dinamis dari form inputan JS)
 */

$data_surat = !empty($data_surat) ? $data_surat : $_POST;

// Menangkap Array Data Pihak Kedua (Bisa satu, bisa banyak pembeli)
$jumlah_penerima = isset($data_surat['jumlah_penerima']) ? (int)$data_surat['jumlah_penerima'] : 1;

// Ekstraksi Data Tanah & Harga
$jenis_tanah = htmlspecialchars($data_surat['dyn_jenis_tanah'] ?? '');
$luas_angka = htmlspecialchars($data_surat['dyn_luas_angka'] ?? '');
$luas_huruf = htmlspecialchars($data_surat['dyn_luas_huruf'] ?? '');
$harga_angka = htmlspecialchars($data_surat['dyn_harga_angka'] ?? '');
$harga_huruf = htmlspecialchars($data_surat['dyn_harga_huruf'] ?? '');
$tahun_jual = htmlspecialchars($data_surat['dyn_tahun_jual'] ?? '');
$no_sppt = htmlspecialchars($data_surat['dyn_no_sppt'] ?? '');
$lokasi_tanah = htmlspecialchars($data_surat['dyn_lokasi_tanah'] ?? '');

$b_utara = htmlspecialchars($data_surat['dyn_batas_utara'] ?? '-');
$b_selatan = htmlspecialchars($data_surat['dyn_batas_selatan'] ?? '-');
$b_timur = htmlspecialchars($data_surat['dyn_batas_timur'] ?? '-');
$b_barat = htmlspecialchars($data_surat['dyn_batas_barat'] ?? '-');

// Ekstraksi Data Saksi 1
$nik_s1 = htmlspecialchars($data_surat['dyn_nik_saksi1'] ?? '');
$nama_s1 = htmlspecialchars($data_surat['dyn_nama_saksi1'] ?? '...........................');
$umur_s1 = htmlspecialchars($data_surat['dyn_umur_saksi1'] ?? '......');
$kerja_s1 = htmlspecialchars($data_surat['dyn_kerja_saksi1'] ?? '...........................');
$alamat_s1 = htmlspecialchars($data_surat['dyn_alamat_saksi1'] ?? '...........................');

// Ekstraksi Data Saksi 2
$nik_s2 = htmlspecialchars($data_surat['dyn_nik_saksi2'] ?? '');
$nama_s2 = htmlspecialchars($data_surat['dyn_nama_saksi2'] ?? '...........................');
$umur_s2 = htmlspecialchars($data_surat['dyn_umur_saksi2'] ?? '......');
$kerja_s2 = htmlspecialchars($data_surat['dyn_kerja_saksi2'] ?? '...........................');
$alamat_s2 = htmlspecialchars($data_surat['dyn_alamat_saksi2'] ?? '...........................');
?>

<div class="judul-surat">
    <div class="judul" style="text-decoration: underline;">SURAT PERNYATAAN JUAL BELI</div>
    <div class="nomor" style="display:none;">Nomor : <?= $nomor_surat_lengkap ?></div>
</div>

<p style="margin-bottom: 10px; text-align: justify;">Yang bertanda tangan/cap jempol di bawah ini:</p>

<!-- PIHAK PERTAMA (PENJUAL) -->
<p style="margin-bottom: 5px;"><strong>1. PIHAK PERTAMA (Penjual)</strong></p>
<table class="tabel-identitas" style="margin-bottom: 15px;">
    <tr><td style="width: 25%;">Nama Lengkap</td><td style="width: 2%;">:</td><td><strong><?= htmlspecialchars($warga['nama_lengkap'] ?? '') ?></strong></td></tr>
    <tr><td>NIK</td><td>:</td><td><?= htmlspecialchars($warga['nik'] ?? '') ?></td></tr>
    <tr><td>Agama</td><td>:</td><td><?= htmlspecialchars($warga['agama'] ?? 'ISLAM') ?></td></tr>
    <tr><td>Umur</td><td>:</td><td><?= isset($warga['tgl_lahir']) ? hitung_umur($warga['tgl_lahir']) : '...' ?> Tahun</td></tr>
    <tr><td>Pekerjaan</td><td>:</td><td><?= htmlspecialchars($warga['pekerjaan'] ?? '') ?></td></tr>
    <tr><td>Alamat</td><td>:</td><td>Dusun <?= htmlspecialchars($warga['nama_dusun'] ?? '') ?> RT <?= htmlspecialchars($warga['rt'] ?? '') ?>/RW <?= htmlspecialchars($warga['rw'] ?? '') ?> Desa Serage</td></tr>
</table>
<p class="paragraf-indent" style="margin-bottom: 15px;">Dalam hal ini bertindak untuk dan atas nama diri sendiri selanjutnya disebut <strong>PIHAK PERTAMA</strong>.</p>

<!-- PIHAK KEDUA (PEMBELI) -->
<p style="margin-bottom: 5px;"><strong>2. PIHAK KEDUA (Pembeli)</strong></p>

<?php for ($i = 1; $i <= $jumlah_penerima; $i++): ?>
    <?php 
        $nama = htmlspecialchars($data_surat['dyn_nama_kedua_'.$i] ?? '');
        $nik = htmlspecialchars($data_surat['dyn_nik_kedua_'.$i] ?? '');
        $umur = htmlspecialchars($data_surat['dyn_umur_kedua_'.$i] ?? '');
        $kerja = htmlspecialchars($data_surat['dyn_kerja_kedua_'.$i] ?? '');
        $alamat = htmlspecialchars($data_surat['dyn_alamat_kedua_'.$i] ?? '');
    ?>
    
    <?php if ($jumlah_penerima > 1): ?>
        <p style="margin-bottom: 3px; font-weight: 600; font-size: 11pt; padding-left: 20px;">2.<?= $i ?>. Pembeli <?= $i ?></p>
    <?php endif; ?>
    
    <table class="tabel-identitas" style="margin-bottom: <?= ($i == $jumlah_penerima) ? '15px' : '5px' ?>; <?= ($jumlah_penerima > 1) ? 'margin-left: 20px; width: 95%;' : '' ?>">
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
        Kami PIHAK PERTAMA telah menjual kepada PIHAK KEDUA sebidang Tanah <strong><?= $jenis_tanah ?></strong> seluas <strong><?= $luas_angka ?> M&sup2;</strong> (<em><?= $luas_huruf ?> Meter Persegi</em>) sesuai SPPT Nomor <strong><?= $no_sppt ?></strong> dengan harga sebesar <strong>Rp. <?= $harga_angka ?></strong> (<em><?= $harga_huruf ?> Rupiah</em>) pada tahun <strong><?= $tahun_jual ?></strong>. Adapun tanah tersebut terletak di <?= $lokasi_tanah ?>, Desa Serage, Kecamatan Praya Barat Daya, Kabupaten Lombok Tengah, Provinsi Nusa Tenggara Barat dengan batas-batas sebagai berikut:
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
    <li style="margin-bottom: 8px;">PIHAK KEDUA menyatakan telah membeli sebidang tanah <?= $jenis_tanah ?> seluas <?= $luas_angka ?> M&sup2; (<?= $luas_huruf ?> Meter Persegi) dengan harga Rp. <?= $harga_angka ?> (<?= $harga_huruf ?> Rupiah) pada tahun <?= $tahun_jual ?>.</li>
    <li>Kami PIHAK PERTAMA menyatakan bahwa jual beli tanah bersifat permanen/tetap sehingga kami PIHAK PERTAMA tidak berhak lagi atas tanah tersebut dan selanjutnya tanah tersebut menjadi milik sah PIHAK KEDUA serta kami PIHAK PERTAMA menyatakan setuju/tidak keberatan apabila tanah tersebut disertipikatkan menjadi Hak Milik PIHAK KEDUA.</li>
</ol>

<p style="text-align: justify;">Demikian kami buat pernyataan ini dengan sebenar-benarnya dan untuk dapat dipergunakan sebagaimana mestinya.</p>

<!-- TANDA TANGAN PIHAK -->
<table style="width: 100%; margin-top: 30px; text-align: center;">
    <tr>
        <td style="width: 40%; vertical-align: top;">
            <strong>PIHAK KEDUA</strong><br><br><br>
            <?php for ($i = 1; $i <= $jumlah_penerima; $i++): ?>
                <div style="margin-bottom: 25px;">
                    ( <strong><?= htmlspecialchars($data_surat['dyn_nama_kedua_'.$i] ?? '....................') ?></strong> )
                </div>
            <?php endfor; ?>
        </td>
        <td style="width: 20%;"></td>
        <td style="width: 40%; vertical-align: top;">
            <strong>PIHAK PERTAMA</strong><br>
            <span style="font-size: 11px; color: #64748b; display: block; margin: 10px 0; border: 1px dashed #cbd5e1; padding: 5px; width: 80px; margin-left: auto; margin-right: auto;">Materai<br>Rp. 10.000</span><br><br>
            ( <strong><?= htmlspecialchars($warga['nama_lengkap']) ?></strong> )
        </td>
    </tr>
</table>

<!-- SAKSI-SAKSI LENGKAP -->
<div style="margin-top: 20px;">
    <strong>SAKSI-SAKSI:</strong>
    <table class="tabel-identitas" style="margin-top: 5px; margin-left: 15px; width: 95%;">
        <tr>
            <td style="width: 3%; vertical-align: top;">1.</td>
            <td style="width: 15%; vertical-align: top;">Nama</td><td style="width: 2%; vertical-align: top;">:</td>
            <td style="width: 45%; vertical-align: top;"><strong><?= $nama_s1 ?></strong></td>
            <td style="width: 35%; text-align: right; vertical-align: bottom;" rowspan="6">1. .....................................</td>
        </tr>
        <tr><td></td><td>NIK</td><td>:</td><td><?= $nik_s1 ?></td></tr>
        <tr><td></td><td>Agama</td><td>:</td><td>ISLAM</td></tr>
        <tr><td></td><td>Usia</td><td>:</td><td><?= $umur_s1 ?> Tahun</td></tr>
        <tr><td></td><td>Pekerjaan</td><td>:</td><td><?= $kerja_s1 ?></td></tr>
        <tr><td></td><td>Alamat</td><td>:</td><td><?= $alamat_s1 ?></td></tr>
        
        <tr><td colspan="5" style="height: 15px;"></td></tr>
        
        <tr>
            <td style="vertical-align: top;">2.</td>
            <td style="vertical-align: top;">Nama</td><td style="vertical-align: top;">:</td>
            <td style="vertical-align: top;"><strong><?= $nama_s2 ?></strong></td>
            <td style="text-align: right; vertical-align: bottom;" rowspan="6">2. .....................................</td>
        </tr>
        <tr><td></td><td>NIK</td><td>:</td><td><?= $nik_s2 ?></td></tr>
        <tr><td></td><td>Agama</td><td>:</td><td>ISLAM</td></tr>
        <tr><td></td><td>Usia</td><td>:</td><td><?= $umur_s2 ?> Tahun</td></tr>
        <tr><td></td><td>Pekerjaan</td><td>:</td><td><?= $kerja_s2 ?></td></tr>
        <tr><td></td><td>Alamat</td><td>:</td><td><?= $alamat_s2 ?></td></tr>
    </table>
</div>

<table style="width: 100%; margin-top: 30px;">
    <tr>
        <td style="width: 50%;"></td>
        <td style="text-align: center;">
            Reg. No : <?= $nomor_surat_lengkap ?><br>
            Tanggal : <?= format_tanggal_indo(date('Y-m-d')) ?><br><br>
            <strong>MENGETAHUI/MEMBENARKAN<br>Kepala Desa Serage</strong><br><br><br><br><br>
            <strong><u>[NAMA_KADES]</u></strong>
        </td>
    </tr>
</table>