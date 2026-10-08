<?php
/**
 * File Template: warisan.php
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

$warga_nama = cek_kosong($warga['nama_lengkap'] ?? ($input_data['nama_lengkap'] ?? ''), '........................................');

// =========================================================================
// 2. EKSTRAKSI VARIABEL FORM DINAMIS DENGAN TITIK-TITIK MANUAL
// =========================================================================
$nama_pewaris = cek_kosong($input_data['dyn_nama_pewaris'] ?? '', '.............................................');
$alamat_pewaris = cek_kosong($input_data['dyn_alamat_pewaris'] ?? '', '.................................................................');
$tahun_meninggal = cek_kosong($input_data['dyn_tahun_meninggal'] ?? '', '........');
$nama_pasangan = cek_kosong($input_data['dyn_nama_pasangan'] ?? '', '.............................................');
$status_pasangan = !empty($input_data['dyn_status_pasangan']) ? $input_data['dyn_status_pasangan'] : 'hidup';
$jumlah_waris_huruf = strtoupper(cek_kosong($input_data['dyn_jumlah_waris_huruf'] ?? '', '...................'));

// Logika coret teks (Strikethrough)
if ($status_pasangan === 'hidup') {
    $teks_status = 'kini masih hidup / <del>telah meninggal dunia</del>';
} else {
    $teks_status = '<del>kini masih hidup</del> / telah meninggal dunia';
}

$jumlah_waris = !empty($input_data['jumlah_waris']) ? (int)$input_data['jumlah_waris'] : 1;

$jenis_tanah = cek_kosong($input_data['dyn_jenis_tanah'] ?? '', 'Pertanian/Pekarangan');
$lokasi_tanah = cek_kosong($input_data['dyn_lokasi_tanah'] ?? '', '........................................');
$pipil = cek_kosong($input_data['dyn_pipil'] ?? '', '..............');
$persil = cek_kosong($input_data['dyn_persil'] ?? '', '..............');
$klas = cek_kosong($input_data['dyn_klas'] ?? '', '..............');
$sertipikat = cek_kosong($input_data['dyn_sertipikat'] ?? '', '..............');
$no_sppt = cek_kosong($input_data['dyn_no_sppt'] ?? '', '...........................');
$luas_angka = cek_kosong($input_data['dyn_luas_angka'] ?? '', '..............');

$b_utara = cek_kosong($input_data['dyn_batas_utara'] ?? '', '........................................');
$b_selatan = cek_kosong($input_data['dyn_batas_selatan'] ?? '', '........................................');
$b_timur = cek_kosong($input_data['dyn_batas_timur'] ?? '', '........................................');
$b_barat = cek_kosong($input_data['dyn_batas_barat'] ?? '', '........................................');

$nama_s1 = cek_kosong($input_data['dyn_nama_saksi1'] ?? '', '.........................................');
$nama_s2 = cek_kosong($input_data['dyn_nama_saksi2'] ?? '', '.........................................');
?>

<style>
    /* STYLING DIPADATKAN (MARGIN & LINE-HEIGHT DIRAPATKAN) */
    .kop-surat { width: 100%; border-collapse: collapse; margin-bottom: 5px; border-bottom: 3px solid #000; }
    .kop-surat td { vertical-align: middle; padding-bottom: 4px; }
    .teks-kop { text-align: center; line-height: 1.1; font-family: 'Times New Roman', Times, serif; }
    .teks-kop h3 { margin: 0; font-size: 14pt; font-weight: normal; }
    .teks-kop h1 { margin: 0; font-size: 16pt; font-weight: bold; }
    
    .judul-surat { text-align: center; margin-bottom: 10px; font-family: 'Times New Roman', Times, serif; }
    .judul-surat .judul { text-decoration: underline; font-weight: bold; font-size: 13pt; margin-bottom: 0; }
    
    .konten-surat { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #000; }
    
    .konten-surat p {
        text-align: justify;
        text-indent: 40px; 
        line-height: 1.15; 
        margin-top: 0;
        margin-bottom: 4px; 
    }

    .konten-surat ol, .konten-surat ul { margin-top: 0; margin-bottom: 4px; padding-left: 40px; }
    .konten-surat li { text-align: justify; line-height: 1.15; margin-bottom: 0; }
    .konten-surat table { line-height: 1.15; border-collapse: collapse; }
    
    .tabel-list { width: 100%; margin-left: 40px; margin-bottom: 4px; }
    .tabel-list td { padding: 0; vertical-align: top; }
    
    .tabel-batas { width: 85%; margin-top: 0; margin-left: 40px; margin-bottom: 5px; }
    .tabel-batas td { padding: 0; }
</style>

<!-- KOP SURAT OPTIMAL -->
<table class="kop-surat">
    <tr>
        <td style="width: 15%; text-align: center;">
            <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="" style="width: 80px; height: auto;" onerror="this.style.display='none'">
        </td>
        <td style="width: 85%;" class="teks-kop">
            <h3>PEMERINTAH KABUPATEN LOMBOK TENGAH</h3>
            <h3>KECAMATAN PRAYA BARAT DAYA</h3>
            <h1>DESA SERAGE</h1>
        </td>
    </tr>
</table>

<!-- JUDUL SURAT -->
<div class="judul-surat">
    <div class="judul">SURAT KETERANGAN WARIS</div>
</div>

<!-- ISI SURAT -->
<div class="konten-surat">
    <p>
        Yang bertanda tangan dibawah ini, para ahli waris dari almarhum/almarhumah <strong><?= $nama_pewaris ?></strong> menerangkan dengan sesungguhnya dan sanggup diangkat sumpah bahwa almarhum/almarhumah <strong><?= $nama_pewaris ?></strong> Tempat tinggal yang terakhir di <?= $alamat_pewaris ?>, pada tahun <strong><?= $tahun_meninggal ?></strong> Telah Meninggal Dunia di <?= $alamat_pewaris ?>, dari perkawinan dengan Suaminya/Istrinya bernama <strong><?= $nama_pasangan ?></strong> dan <?= $teks_status ?>, telah melahirkan : <strong><?= $jumlah_waris_huruf ?></strong> ( <strong><?= $jumlah_waris ?></strong> ) orang anak, yakni :
    </p>

    <!-- LIST AHLI WARIS (Berfungsi sama, jika nama kosong diganti titik-titik) -->
    <table class="tabel-list">
        <?php for ($i = 1; $i <= $jumlah_waris; $i++): 
            $nama_anak = cek_kosong($input_data['dyn_nama_waris_'.$i] ?? '', '................................................................');
        ?>
            <tr>
                <td style="width: 4%;"><?= $i ?>.</td>
                <td style="width: 96%;"><strong><?= $nama_anak ?></strong></td>
            </tr>
        <?php endfor; ?>
    </table>

    <p>
        Pada masa hidupnya beliau memiliki sebidang tanah <?= $jenis_tanah ?> yang terletak di <?= $lokasi_tanah ?>, Desa Serage, Kecamatan Praya Barat Daya, Kabupaten Lombok Tengah, tersebut dalam Pipil No. <strong><?= $pipil ?></strong> SPPT No. <strong><?= $no_sppt ?></strong> Persil No. <strong><?= $persil ?></strong> Klas: <strong><?= $klas ?></strong> Sertipikat No. <strong><?= $sertipikat ?></strong> Luas &plusmn; <strong><?= $luas_angka ?> M&sup2;</strong> dengan batas-batas sebagai berikut:
    </p>

    <table class="tabel-batas">
        <tr>
            <td style="width: 15%;">Utara</td><td style="width: 35%;">: <strong><?= $b_utara ?></strong></td>
            <td style="width: 15%;">Timur</td><td style="width: 35%;">: <strong><?= $b_timur ?></strong></td>
        </tr>
        <tr>
            <td>Selatan</td><td>: <strong><?= $b_selatan ?></strong></td>
            <td>Barat</td><td>: <strong><?= $b_barat ?></strong></td>
        </tr>
    </table>

    <p>
        Setelah diadakan musyawarah, maka kami para ahli waris sepakat bahwa tanah tersebut di atas adalah merupakan bagian untuk:
    </p>

    <div style="text-align: center; font-size: 13pt; font-weight: bold; text-decoration: underline; margin: 8px 0;">
        <?= $warga_nama ?>
    </div>

    <p>
        Dan kami para ahli waris menyatakan tidak keberatan apabila tanah tersebut disertipikatkan atas namanya karena memang sudah merupakan bagian masing-masing dan tidak ada lagi ahli waris lain yang berhak atas tanah tersebut.
    </p>
    
    <p style="margin-bottom: 15px;">
        Demikian Surat Keterangan ini kami buat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
    </p>
    
    <!-- BAGIAN TANDA TANGAN AHLI WARIS (ZIG-ZAG) -->
    <div style="text-align: center; font-family: 'Times New Roman', Times, serif; font-size: 12pt; margin-bottom: 10px;">
        Para ahli waris tersebut di atas,
    </div>

    <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                <table style="width: 100%;">
                    <?php for ($i = 1; $i <= $jumlah_waris; $i++): 
                        $nama_anak_ttd = cek_kosong($input_data['dyn_nama_waris_'.$i] ?? '', '.............................................');
                    ?>
                        <tr>
                            <td style="width: 8%; vertical-align: top; padding-bottom: 15px;"><?= $i ?>.</td>
                            <td style="width: 92%; vertical-align: top; padding-bottom: 15px;"><?= $nama_anak_ttd ?></td>
                        </tr>
                    <?php endfor; ?>
                </table>
            </td>
            
            <td style="width: 50%; vertical-align: top;">
                <table style="width: 100%;">
                    <?php for ($i = 1; $i <= $jumlah_waris; $i++): ?>
                        <tr>
                            <?php if ($i % 2 != 0): ?>
                                <td style="width: 50%; padding-bottom: 15px;">
                                    <?= $i ?>. ............................
                                </td>
                                <td style="width: 50%;"></td>
                            <?php else: ?>
                                <td style="width: 50%; text-align: center; vertical-align: bottom;">
                                    <?php if ($i == 4 || ($jumlah_waris <= 3 && $i == 2)): ?>
                                        <span style="font-size: 9pt; font-style: italic; color: #333;">Materai Rp. 10.000,-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="width: 50%; padding-bottom: 15px; text-align: right;">
                                    <?= $i ?>. ............................
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endfor; ?>
                </table>
            </td>
        </tr>
    </table>

    <!-- TANDA TANGAN SAKSI & KADES -->
    <div style="text-align: center; font-weight: bold; margin-bottom: 10px;">
        SAKSI &ndash; SAKSI
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; text-align: center; padding-bottom: 5px;">
                1. <u><strong><?= $nama_s1 ?></strong></u>
            </td>
            <td style="width: 50%; text-align: center; padding-bottom: 5px;">
                2. <u><strong><?= $nama_s2 ?></strong></u>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; vertical-align: top;"></td>
            <td style="width: 50%; text-align: center; vertical-align: top;">
                Nomor : <?= htmlspecialchars($nomor_surat_lengkap ?? '') ?><br>
                Disaksikan dan dibenarkan<br>
                Oleh kami<br>
                Serage, Tgl. <?= format_tanggal_indo($input_data['tgl_terbit'] ?? date('Y-m-d')) ?><br>
                Kepala Desa/Lurah Serage<br><br><br><br>
                ( <u><strong>[NAMA_KADES]</strong></u> )
            </td>
        </tr>
    </table>

    <div style="margin-top: 5px; font-size: 11pt; text-align: left;">
        <u><strong>Catatan :</strong></u> <em>Coret yang tidak perlu.</em>
    </div>
</div>