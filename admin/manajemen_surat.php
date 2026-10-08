<?php
session_start();
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
require_once 'includes/admin_header.php';

$pesan = '';

// --- PROSES UPDATE KODE SURAT (MASTER TEMPLATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_kode_surat'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $pesan = "<div class='alert-error'>Sesi tidak valid. Muat ulang halaman.</div>";
    } else {
        $id_template = (int)$_POST['id_template'];
        $kode_baru = $koneksi->real_escape_string(trim($_POST['kode_surat']));
        
        $cek = $koneksi->query("SELECT id FROM template_surat WHERE kode_surat = '$kode_baru' AND id != $id_template");
        if ($cek->num_rows > 0) {
            $pesan = "<div class='alert-error'>Gagal! Kode Surat <b>$kode_baru</b> sudah digunakan.</div>";
        } else {
            $stmt = $koneksi->prepare("UPDATE template_surat SET kode_surat = ? WHERE id = ?");
            $stmt->bind_param("si", $kode_baru, $id_template);
            if ($stmt->execute()) {
                catat_log($koneksi, $_SESSION['admin_id'], 'Edit Kode Surat', "Mengubah kode surat master menjadi: $kode_baru");
                $pesan = "<div class='alert-success'>Kode Surat master berhasil diperbarui!</div>";
            } else {
                $pesan = "<div class='alert-error'>Gagal menyimpan pembaruan.</div>";
            }
            $stmt->close();
        }
    }
}

// --- PROSES UPDATE NOMOR SURAT (RIWAYAT TERBIT) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_nomor_surat'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $pesan = "<div class='alert-error'>Sesi tidak valid. Muat ulang halaman.</div>";
    } else {
        $id_transaksi = (int)$_POST['id_transaksi'];
        $nomor_baru = $koneksi->real_escape_string(trim($_POST['nomor_surat']));
        
        $cek = $koneksi->query("SELECT id FROM transaksi_surat WHERE nomor_surat = '$nomor_baru' AND id != $id_transaksi");
        if ($cek->num_rows > 0) {
            $pesan = "<div class='alert-error'>Gagal! Nomor Surat <b>$nomor_baru</b> sudah tercatat di arsip lain.</div>";
        } else {
            $stmt = $koneksi->prepare("UPDATE transaksi_surat SET nomor_surat = ? WHERE id = ?");
            $stmt->bind_param("si", $nomor_baru, $id_transaksi);
            if ($stmt->execute()) {
                catat_log($koneksi, $_SESSION['admin_id'], 'Edit Nomor Surat', "Merevisi nomor surat keluar menjadi: $nomor_baru");
                $pesan = "<div class='alert-success'>Nomor Surat fisik berhasil disinkronisasi!</div>";
            } else {
                $pesan = "<div class='alert-error'>Gagal menyimpan pembaruan.</div>";
            }
            $stmt->close();
        }
    }
}

if (!isset($_SESSION['admin_logged_in'])) { header("Location: ../login.php"); exit; }

$query_riwayat = "SELECT t.id, t.nomor_surat, t.tgl_terbit, t.data_dinamis, p.nik, p.nama_lengkap 
                  FROM transaksi_surat t 
                  LEFT JOIN penduduk p ON t.penduduk_id = p.id 
                  ORDER BY t.tgl_terbit DESC, t.id DESC";
$result_riwayat = $koneksi->query($query_riwayat);

// Simpan data template ke array
$query_template = "SELECT * FROM template_surat ORDER BY nama_surat ASC";
$result_template = $koneksi->query($query_template);
$templates = [];
if ($result_template) {
    while ($t = $result_template->fetch_assoc()) {
        $templates[] = $t;
    }
}
?>

<style>
    /* Styling Navigasi Tab */
    .tab-container { display: flex; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px; overflow-x: auto; }
    .tab-btn { padding: 12px 24px; background: none; border: none; cursor: pointer; font-weight: 600; font-size: 15px; color: #64748b; transition: 0.3s; border-bottom: 3px solid transparent; margin-bottom: -2px; white-space: nowrap; }
    .tab-btn:hover, .tab-btn.active { color: #1a6f76; border-bottom-color: #1a6f76; }
    .tab-content { display: none; animation: fadeIn 0.4s; }
    .tab-content.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    
    .table-surat { width: 100%; border-collapse: collapse; text-align: left; }
    .table-surat th { background: #f8fafc; padding: 14px 12px; border-bottom: 2px solid #e2e8f0; color: #334155; font-size: 13.5px; }
    .table-surat td { padding: 14px 12px; border-bottom: 1px solid #e2e8f0; color: #475569; font-size: 13.5px; vertical-align: middle; }
    .col-keperluan { max-width: 350px; word-break: break-word; line-height: 1.4; }

    /* ================= GRID TEMPLATE SURAT ================= */
    .template-grid-container { padding: 20px; background: #ffffff; border-radius: 12px; border: 1px dashed #cbd5e1; }
    .section-header { text-align: center; margin-bottom: 30px; }
    .section-header h3 { color: #1a6f76; margin-bottom: 5px; font-weight: 700; font-size: 20px; }
    .section-header p { color: #64748b; font-size: 14px; margin-bottom: 0; }

    .template-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
    .template-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; gap: 15px; cursor: pointer; transition: all 0.3s ease; text-align: center; }
    .template-card:hover { border-color: #59d5e0; box-shadow: 0 10px 25px rgba(89, 213, 224, 0.15); transform: translateY(-5px); }
    .card-icon i { font-size: 38px; color: #1a6f76; }
    .kode-surat { font-size: 12px; font-weight: 600; color: #64748b; background: #e2e8f0; padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 8px; }
    .card-info h4 { margin: 0; font-size: 16px; color: #0f172a; line-height: 1.4; font-weight: 600; }
    .btn-buat-surat { background: transparent; border: 1px solid #1a6f76; color: #1a6f76; padding: 8px; width: 100%; border-radius: 8px; font-weight: 600; transition: 0.3s; pointer-events: none; margin-top: auto; }
    .template-card:hover .btn-buat-surat { background: #1a6f76; color: #fff; }

    /* ================= POPUP MODAL STYLING ================= */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(5px); display: none; align-items: center; justify-content: center; z-index: 9999; opacity: 0; transition: opacity 0.3s ease; }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-box { background: #fff; width: 96%; max-width: 1200px; height: 90vh; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); transform: scale(0.97); transition: transform 0.3s ease; display: flex; flex-direction: column; overflow: hidden; }
    .modal-overlay.show .modal-box { transform: scale(1); }
    
    .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding: 18px 25px; background: #ffffff; }
    .btn-close { background: #fee2e2; border: none; width: 34px; height: 34px; border-radius: 8px; font-size: 16px; color: #ef4444; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; }
    .btn-close:hover { background: #ef4444; color: #fff; }
    
    .modal-body { display: grid; grid-template-columns: 450px 1fr; height: calc(100% - 71px); overflow: hidden; }
    .form-section { padding: 25px; overflow-y: auto; background: #ffffff; border-right: 1px solid #e2e8f0; }
    .form-section::-webkit-scrollbar { width: 6px; }
    .form-section::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .form-control { width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #cbd5e1; box-sizing: border-box; font-family: 'Poppins', sans-serif; transition: 0.3s; font-size: 14px; background: #f8fafc; }
    .form-control:focus { outline: none; border-color: #1a6f76; background: #fff; box-shadow: 0 0 0 3px rgba(26, 111, 118, 0.15); }
    .form-group { margin-bottom: 20px; }
    .form-group label { font-weight: 600; color: #334155; display: block; margin-bottom: 8px; font-size: 13.5px; }

    .preview-wrapper { position: relative; background: #475569; display: flex; align-items: center; justify-content: center; flex-direction: column; width: 100%; height: 100%; overflow: hidden; }
    .preview-iframe { width: 100%; height: 100%; border: none; background: #526075; transition: opacity 0.3s ease; }
    .preview-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(71, 85, 105, 0.6); backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; z-index: 10; color: white; font-size: 16px; font-weight: bold; flex-direction: column; gap: 12px; }
    .mode-manual-box { background: #f8fafc; padding: 18px; border-radius: 8px; border: 1px dashed #f59e0b; display: none; margin-bottom: 20px; }

    .modal-body-single { display: block; overflow-y: auto; padding: 25px; height: auto; }

    @media (max-width: 1024px) {
        .modal-body { grid-template-columns: 1fr; overflow-y: auto; height: calc(100% - 71px); }
        .form-section { border-right: none; border-bottom: 2px solid #e2e8f0; }
        .preview-wrapper { min-height: 650px; }
    }
</style>

<div style="padding: 25px; background: #fff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin: 20px;">
    <h2 style="color: #1a6f76; margin-top: 0;">Manajemen Surat Administrasi</h2>
    <?= $pesan ?>
    
    <div class="tab-container">
        <button class="tab-btn active" onclick="bukaTab(event, 'buat-surat')"><i class="fa-solid fa-pen-to-square"></i> Buat Surat Baru</button>
        <button class="tab-btn" onclick="bukaTab(event, 'RiwayatSurat')"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Surat Keluar</button>
        <button class="tab-btn" onclick="bukaTab(event, 'MasterTemplate')"><i class="fa-solid fa-code"></i> Master Template Surat</button>
    </div>

    <!-- TAB 1: BUAT SURAT -->
    <div class="tab-content active" id="buat-surat">
        <div class="template-grid-container">
            <div class="section-header">
                <div style="display:inline-block; background:#e0f2fe; padding:15px; border-radius:50%; margin-bottom:15px;">
                    <i class="fa-solid fa-envelope-open-text" style="font-size:35px; color:#1a6f76;"></i>
                </div>
                <h3>Pusat Pembuatan Dokumen Desa</h3>
                <p>Gunakan lembar kerja interaktif untuk menerbitkan surat administrasi warga dengan fitur pratinjau waktu nyata (real-time).</p>
            </div>

            <div class="template-grid">
                <?php if (!empty($templates)): ?>
                    <?php foreach ($templates as $template): ?>
                    <!-- KARTU TEMPLATE -->
                    <div class="template-card" onclick="bukaModalSurat('<?= htmlspecialchars($template['nama_surat']) ?>')">
                        <div class="card-icon"><i class="fa-solid fa-file-signature"></i></div>
                        <div class="card-info">
                            <span class="kode-surat"><?= htmlspecialchars($template['kode_surat']) ?></span>
                            <h4><?= htmlspecialchars($template['nama_surat']) ?></h4>
                        </div>
                        <div class="card-action">
                            <button type="button" class="btn-buat-surat"><i class="fa-solid fa-plus"></i> Buka Lembar Kerja</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align:center; padding:40px; color:#94a3b8;">
                        <i class='fa-solid fa-folder-open' style="font-size:40px; margin-bottom:10px;"></i>
                        <p>Belum ada template surat yang tersedia.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- TAB 2: RIWAYAT SURAT -->
    <div id="RiwayatSurat" class="tab-content">
        <div style="overflow-x: auto; background: #fff; border-radius: 8px;">
            <table class="table-surat">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 20%;">Nomor Surat</th>
                        <th style="width: 18%;">Identitas Pemohon</th>
                        <th style="width: 12%;">Jenis</th>
                        <th style="width: 15%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_riwayat && $result_riwayat->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $result_riwayat->fetch_assoc()): 
                            $data_surat = json_decode($row['data_dinamis'], true); 
                            $is_manual = isset($data_surat['is_manual']) && $data_surat['is_manual'];
                            $tampil_nik = $is_manual ? ($data_surat['data_warga']['nik'] ?? '-') : $row['nik'];
                            $tampil_nama = $is_manual ? ($data_surat['data_warga']['nama_lengkap'] ?? 'Data Manual') : $row['nama_lengkap'];
                            $clean_keperluan = strip_tags($data_surat['keperluan'] ?? '-');
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= format_tanggal_indo($row['tgl_terbit']) ?></td>
                            <td style="font-weight: bold; color: #1a6f76;"><?= htmlspecialchars($row['nomor_surat']) ?></td>
                            <td>
                                <div style="font-size: 11.5px; color: #64748b;"><?= htmlspecialchars($tampil_nik) ?> <?= $is_manual ? '<span style="color:#f59e0b; font-weight:bold;">(Manual)</span>' : '' ?></div>
                                <strong style="color: #0f172a;"><?= htmlspecialchars($tampil_nama) ?></strong>
                            </td>
                            <td>
                                <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; display: inline-block;">
                                    <?= htmlspecialchars($data_surat['jenis'] ?? '-') ?>
                                </span>
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                <a href="lihat_surat.php?id=<?= $row['id'] ?>" target="_blank" style="padding: 6px 10px; background: #1a6f76; color: white; border-radius: 4px; text-decoration: none; font-size: 12.5px; font-weight: 500; display: inline-block; margin-right: 4px;" title="Lihat/Cetak"><i class="fa-solid fa-eye"></i></a>
                                <button onclick="bukaEditNomor(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nomor_surat']) ?>')" style="padding: 6px 10px; background: #f59e0b; color: white; border: none; border-radius: 4px; font-size: 12.5px; font-weight: 500; cursor: pointer; margin-right: 4px;" title="Revisi Nomor"><i class="fa-solid fa-hashtag"></i></button>
                                <a href="hapus_surat.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus permanen arsip ini?');" style="padding: 6px 10px; background: #ef4444; color: white; border-radius: 4px; text-decoration: none; font-size: 12.5px; font-weight: 500; display: inline-block;" title="Hapus"><i class="fa-solid fa-trash-can"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="padding: 40px; text-align: center; color: #94a3b8;">Belum ada riwayat surat keluar.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 3: MASTER TEMPLATE -->
    <div id="MasterTemplate" class="tab-content">
        <div style="overflow-x: auto; background: #fff; border-radius: 8px;">
            <table class="table-surat">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 40%;">Jenis Dokumen / Nama Surat</th>
                        <th style="width: 40%;">Format Kode Surat (Buku Register)</th>
                        <th style="width: 15%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($templates as $tpl): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($tpl['nama_surat']) ?></td>
                        <td><span style="background: #f1f5f9; padding: 6px 12px; border-radius: 4px; font-family: monospace; font-size: 14px; border: 1px solid #cbd5e1; color: #0f172a;"><?= htmlspecialchars($tpl['kode_surat']) ?></span></td>
                        <td style="text-align: center;">
                            <button onclick="bukaEditKode(<?= $tpl['id'] ?>, '<?= htmlspecialchars($tpl['kode_surat']) ?>')" style="padding: 8px 14px; background: #3b82f6; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;" title="Edit Kode Master"><i class="fa-solid fa-pen"></i> Sesuaikan</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= POPUP MODAL LEMBAR KERJA (SATU MODAL SAJA) ================= -->
<div id="modalSuratKerja" class="modal-overlay">
    <div class="modal-box">
        <!-- Header Modal -->
        <div class="modal-header">
            <h3 style="margin: 0; color: #0f172a; font-size: 18px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-file-pen" style="color: #1a6f76; font-size: 22px;"></i> 
                Lembar Kerja Persuratan Desa
            </h3>
            <button class="btn-close" onclick="tutupModalSurat()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <!-- Body Modal (Split Layout) -->
        <div class="modal-body">
            <!-- SISI KIRI: Form Input -->
            <div class="form-section">
                <form id="formSurat" action="cetak_surat.php" method="POST" target="_blank">
                    
                    <div class="form-group">
                        <label>Jenis Surat Administrasi</label>
                        <select name="jenis_surat" id="inputJenis" class="form-control" required>
                            <option value="" data-nama="">-- Pilih Jenis Surat --</option>
                            <?php foreach ($templates as $tpl): ?>
                                <!-- value diisi nama file untuk backend, data-nama diisi nama surat untuk JS -->
                                <option value="<?= htmlspecialchars($tpl['header_surat']) ?>" data-nama="<?= htmlspecialchars($tpl['nama_surat']) ?>">
                                    <?= htmlspecialchars($tpl['nama_surat']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nomor Urut Surat</label>
                        <div style="display: flex; align-items: center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden;">
                            <span style="padding: 11px 14px; background: #e2e8f0; color: #475569; font-weight: 600; font-size: 13px; border-right: 1px solid #cbd5e1;">[KODE]/</span>
                            <input type="text" name="nomor_urut" id="inputNomorUrut" style="border: none; border-radius: 0; flex: 1; padding: 11px 14px; outline: none; font-weight: bold; color: #1a6f76;" required placeholder="001" autocomplete="off">
                            <span style="padding: 11px 14px; background: #e2e8f0; color: #475569; font-weight: 600; font-size: 13px; border-left: 1px solid #cbd5e1;">/DS-SRG/[BLN]/[THN]</span>
                        </div>
                        <small style="color: #64748b; font-size: 11.5px; margin-top: 5px; display: block;">*Isi angka urutan saja (cth: 001). Kode dan tanggal di-generate otomatis.</small>
                    </div>

                    <div class="form-group">
                        <label>NIK Pemohon (Pihak Pertama)</label>
                        <input type="text" name="nik" id="inputNik" maxlength="16" placeholder="Ketik 16 Digit NIK..." class="form-control" required autocomplete="off">
                        <div id="hasilPencarian" style="margin-top: 6px; font-size: 12.5px;"></div>
                    </div>

                    <div class="form-group" style="background: #fffbeb; padding: 12px 14px; border-radius: 8px; border: 1px solid #fde68a;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #b45309; font-weight: 600; font-size: 13px; margin:0;">
                            <input type="checkbox" id="modeManual" name="mode_manual" value="1" style="width: 16px; height: 16px; accent-color: #f59e0b;">
                            Gunakan Data Manual (Pemohon Warga Baru/Luar)
                        </label>
                    </div>

                    <!-- FORM MANUAL HIDDEN -->
                    <div id="formManual" class="mode-manual-box">
                        <div class="form-group" style="margin-bottom:10px;">
                            <input type="text" name="nik_manual" id="mNik" placeholder="Nomor Induk Kependudukan (NIK)" class="form-control manual-input" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="form-group" style="margin-bottom:10px;">
                            <input type="text" name="nama_manual" id="mNama" placeholder="Nama Lengkap & Gelar" class="form-control manual-input">
                        </div>
                        <div style="display:flex; gap:10px; margin-bottom:10px;">
                            <div style="flex:1"><input type="text" name="tempat_manual" id="mTempat" placeholder="Tempat Lahir" class="form-control manual-input"></div>
                            <div style="flex:1"><input type="date" name="tgl_manual" id="mTgl" class="form-control manual-input"></div>
                        </div>
                        <div class="form-group" style="margin-bottom:10px;">
                            <select name="jk_manual" id="mJk" class="form-control manual-input">
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:10px;"><input type="text" name="pekerjaan_manual" id="mPekerjaan" placeholder="Pekerjaan" class="form-control manual-input"></div>
                        <div class="form-group" style="margin-bottom:10px;"><input type="text" name="dusun_manual" id="mDusun" placeholder="Nama Dusun" class="form-control manual-input"></div>
                        <div style="display:flex; gap:10px; margin-bottom:0;">
                            <div style="flex:1"><input type="text" name="rt_manual" id="mRt" placeholder="RT (001)" class="form-control manual-input"></div>
                            <div style="flex:1"><input type="text" name="rw_manual" id="mRw" placeholder="RW (002)" class="form-control manual-input"></div>
                        </div>
                    </div>

                    <!-- AREA INPUT DINAMIS SESUAI JENIS SURAT -->
                    <div id="dynamicInputArea" style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px dashed #cbd5e1; margin-bottom: 20px;">
                        <p style="margin:0; font-size:12.5px; color:#64748b; text-align:center;"><i class="fa-solid fa-circle-info"></i> Pilih Jenis Surat untuk menampilkan isian spesifik.</p>
                    </div>

                    <div style="display: flex; gap: 10px; flex-direction: column; padding-top: 5px;">
                        <button type="submit" id="btnProses" disabled style="width: 100%; padding: 13px; background: #cbd5e1; color: #475569; border: none; border-radius: 8px; cursor: not-allowed; font-weight: bold; font-size: 14.5px; transition: 0.3s;">Menunggu Validasi...</button>
                        <button type="button" onclick="tutupModalSurat()" style="width: 100%; padding: 11px; background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13.5px;">Batal & Tutup</button>
                    </div>
                </form>
            </div>

            <!-- SISI KANAN: Live Preview -->
            <div class="preview-wrapper" id="boxPreview">
                <div id="previewPlaceholder" style="color: #cbd5e1; text-align: center; padding: 20px;">
                    <i class="fa-solid fa-eye" style="font-size: 55px; margin-bottom: 15px; color: #94a3b8;"></i>
                    <p style="margin:0; font-size: 15px; color: #cbd5e1;">Pilih Jenis Surat & Masukkan NIK<br>untuk melihat <i>Live Preview</i>.</p>
                </div>
                <div class="preview-overlay" id="previewOverlay">
                    <i class="fa-solid fa-circle-notch fa-spin" style="font-size: 35px;"></i><span style="font-size: 14px;">Memperbarui Pratinjau...</span>
                </div>
                <iframe id="framePreview" class="preview-iframe"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- ================= POPUP MODAL KODE & NOMOR SURAT ================= -->
<div class="modal-overlay" id="modalEditKode">
    <div class="modal-box" style="max-width: 450px; height: auto;">
        <div class="modal-header">
            <h3 style="margin:0; font-size:16px;"><i class="fa-solid fa-code"></i> Revisi Kode Surat</h3>
            <button type="button" class="btn-close" onclick="tutupModalId('modalEditKode')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body-single">
            <form action="" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <input type="hidden" name="id_template" id="form_id_template">
                
                <div class="form-group">
                    <label class="form-label">Format Kode Surat</label>
                    <input type="text" name="kode_surat" id="form_kode_surat" class="form-control" placeholder="Cth: 145/Pem-Des" required autocomplete="off">
                    <small style="color: #64748b; font-size: 11.5px; margin-top: 6px; display: block;">*Pembaruan ini akan memengaruhi format penomoran otomatis untuk surat yang akan dibuat selanjutnya.</small>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 25px;">
                    <button type="button" style="background:#cbd5e0; border:none; border-radius:6px; font-weight:600; color:#1e293b; flex:1; cursor:pointer;" onclick="tutupModalId('modalEditKode')">Batal</button>
                    <button type="submit" name="update_kode_surat" style="background:#1a6f76; border:none; border-radius:6px; font-weight:600; color:#fff; padding:12px; flex:2; cursor:pointer;"><i class="fa-solid fa-save"></i> Simpan Kode</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modalEditNomor">
    <div class="modal-box" style="max-width: 450px; height: auto;">
        <div class="modal-header" style="background: #f59e0b;">
            <h3 style="margin:0; font-size:16px; color:white;"><i class="fa-solid fa-hashtag"></i> Sinkronisasi Nomor Surat</h3>
            <button type="button" class="btn-close" style="background:rgba(255,255,255,0.2); color:white;" onclick="tutupModalId('modalEditNomor')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body-single">
            <form action="" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <input type="hidden" name="id_transaksi" id="form_id_transaksi">
                
                <div class="form-group">
                    <label class="form-label">Nomor Surat Final</label>
                    <input type="text" name="nomor_surat" id="form_nomor_surat" class="form-control" placeholder="Cth: 145/001/Pem-Des/VIII/2026" required autocomplete="off">
                    <small style="color: #ef4444; font-size: 11.5px; margin-top: 6px; display: block; font-weight:500;">*Peringatan: Pastikan nomor ini sama persis dengan yang tercatat di Buku Register Desa.</small>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 25px;">
                    <button type="button" style="background:#cbd5e0; border:none; border-radius:6px; font-weight:600; color:#1e293b; flex:1; cursor:pointer;" onclick="tutupModalId('modalEditNomor')">Batal</button>
                    <button type="submit" name="update_nomor_surat" style="background:#f59e0b; border:none; border-radius:6px; font-weight:600; color:#fff; padding:12px; flex:2; cursor:pointer;"><i class="fa-solid fa-check-double"></i> Terapkan Nomor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ================= LOGIC NAVIGASI TAB & MODAL =================
function bukaTab(evt, namaTab) {
    document.querySelectorAll(".tab-content").forEach(el => el.classList.remove("active"));
    document.querySelectorAll(".tab-btn").forEach(el => el.classList.remove("active"));
    document.getElementById(namaTab).classList.add("active");
    evt.currentTarget.classList.add("active");
}

function tutupModalId(modalId) {
    document.getElementById(modalId).classList.remove('show');
}

function bukaEditKode(id, kodeLama) {
    document.getElementById('form_id_template').value = id;
    document.getElementById('form_kode_surat').value = kodeLama;
    document.getElementById('modalEditKode').classList.add('show');
}

function bukaEditNomor(id, nomorLama) {
    document.getElementById('form_id_transaksi').value = id;
    document.getElementById('form_nomor_surat').value = nomorLama;
    document.getElementById('modalEditNomor').classList.add('show');
}

// ================= DEKLARASI VARIABEL UTAMA =================
let timeoutId;
let penerimaTimeoutId; 

const modalSurat = document.getElementById('modalSuratKerja');
const inputNik = document.getElementById('inputNik');
const inputJenis = document.getElementById('inputJenis');
const inputNomorUrut = document.getElementById('inputNomorUrut');
const modeManual = document.getElementById('modeManual');
const formManual = document.getElementById('formManual');
const manualInputs = document.querySelectorAll('.manual-input');
const btnProses = document.getElementById('btnProses');
const hasilDiv = document.getElementById('hasilPencarian');
const framePreview = document.getElementById('framePreview');
const previewPlaceholder = document.getElementById('previewPlaceholder');
const previewOverlay = document.getElementById('previewOverlay');
const dynamicInputArea = document.getElementById('dynamicInputArea');

function bukaModalSurat(namaSurat) {
    modalSurat.classList.add('show');
    if(namaSurat) {
        let options = inputJenis.options;
        for(let i = 0; i < options.length; i++) {
            if(options[i].getAttribute('data-nama') === namaSurat) {
                inputJenis.selectedIndex = i;
                break;
            }
        }
        inputJenis.dispatchEvent(new Event('change'));
    }
}

function tutupModalSurat() {
    modalSurat.classList.remove('show');
    document.getElementById('formSurat').reset();
    formManual.style.display = 'none';
    hasilDiv.innerHTML = '';
    dynamicInputArea.innerHTML = '<p style="margin:0; font-size:12.5px; color:#64748b; text-align:center;"><i class="fa-solid fa-circle-info"></i> Pilih Jenis Surat untuk menampilkan isian spesifik.</p>';
    setButtonState(false);
    updateLivePreview();
}

function setButtonState(isValid) {
    if(isValid) {
        btnProses.disabled = false; btnProses.style.background = '#1a6f76'; btnProses.style.color = '#ffffff'; 
        btnProses.innerHTML = '<i class="fa-solid fa-print"></i> Cetak & Simpan Surat'; btnProses.style.cursor = 'pointer';
    } else {
        btnProses.disabled = true; btnProses.style.background = '#cbd5e1'; btnProses.style.color = '#475569'; 
        btnProses.innerHTML = 'Data Belum Lengkap'; btnProses.style.cursor = 'not-allowed';
    }
}

modeManual.addEventListener('change', function() {
    if (this.checked) {
        formManual.style.display = 'block';
        hasilDiv.innerHTML = '<span style="color:#f59e0b;"><i class="fa-solid fa-pen"></i> Mode Manual Aktif</span>';
        setButtonState(true);
    } else {
        formManual.style.display = 'none';
        inputNik.dispatchEvent(new Event('input')); 
    }
    updateLivePreview();
});

inputNik.addEventListener('input', function() {
    clearTimeout(timeoutId);
    if (modeManual.checked) { updateLivePreview(); return; }
    let nik = this.value;
    if (nik.length >= 15) {
        hasilDiv.innerHTML = '<span style="color:#f59e0b;"><i class="fa-solid fa-spinner fa-spin"></i> Memverifikasi data warga...</span>';
        timeoutId = setTimeout(() => {
            fetch(`../ajax_get_penduduk.php?nik=${nik}`)
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        hasilDiv.innerHTML = `<span style="color:#ef4444; font-weight:600;"><i class="fa-solid fa-circle-xmark"></i> ${data.error}</span>`;
                        setButtonState(false);
                    } else {
                        hasilDiv.innerHTML = `<span style="color:#10b981;"><i class="fa-solid fa-circle-check"></i> <strong style="color:#1a6f76;">${data.nama_lengkap}</strong> - RT ${data.rt}</span>`;
                        setButtonState(true);
                    }
                    updateLivePreview();
                });
        }, 300);
    } else {
        hasilDiv.innerHTML = '';
        setButtonState(false);
        updateLivePreview();
    }
});

inputNomorUrut.addEventListener('input', function() {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(updateLivePreview, 300);
});

// ================= CONFIG & FORM DINAMIS (JUAL BELI & HIBAH) =================
const configSurat = {
    'Surat Keterangan Usaha': `
        <div class="form-group"><label>Nama Usaha</label><input type="text" name="dyn_nama_usaha" class="form-control dyn-inp" required placeholder="Contoh: Kios Berkah"></div>
        <div class="form-group"><label>Bidang Usaha</label><input type="text" name="dyn_jenis_usaha" class="form-control dyn-inp" required placeholder="Contoh: Sembako"></div>
        <div class="form-group" style="margin-bottom:0;"><label>Tujuan Pembuatan</label><input type="text" name="keperluan" class="form-control dyn-inp" placeholder="Contoh: Pengajuan KUR BRI"></div>`,
    
    'Surat Keterangan Kelahiran': `
        <div class="form-group"><label>Nama Anak</label><input type="text" name="dyn_nama_anak" class="form-control dyn-inp" required></div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1"><label>Tempat Lahir</label><input type="text" name="dyn_tempat_lahir" class="form-control dyn-inp"></div>
            <div class="form-group" style="flex:1"><label>Tanggal Lahir</label><input type="date" name="dyn_tgl_lahir" class="form-control dyn-inp" required></div>
        </div>
        <div class="form-group" style="margin-bottom:0;"><label>Anak Ke-</label><input type="number" name="dyn_anak_ke" class="form-control dyn-inp"></div>`,
        
    'Surat Pernyataan Jual Beli': `
        <input type="hidden" name="keperluan" value="Pernyataan Jual Beli Tanah">
        <input type="hidden" name="jumlah_penerima" id="jumlahPenerima" value="1">
        
        <div style="background: #f1f5f9; padding: 8px 12px; border-radius: 6px; margin-bottom: 10px; font-weight: bold; color: #0f172a; font-size: 13px; display: flex; justify-content: space-between; align-items: center;">
            <span style="display:flex; align-items:center; gap:8px;"><i class="fa-solid fa-users"></i> A. Pihak Kedua (Pembeli)</span>
            <button type="button" onclick="tambahPembeli()" style="background:#10b981; color:#fff; border:none; padding:6px 12px; border-radius:4px; font-size:11px; cursor:pointer; font-weight:bold;"><i class="fa-solid fa-plus"></i> Tambah Pembeli</button>
        </div>
        
        <div id="wadahPenerima">
            <div class="autofill-group penerima-item" style="border:1px solid #cbd5e1; padding:15px; border-radius:8px; margin-bottom:15px; background:#ffffff;">
                <div class="title-penerima" style="font-weight:bold; font-size:12px; margin-bottom:10px; color:#1a6f76;">Pembeli 1</div>
                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1">
                        <label>NIK Pembeli</label>
                        <input type="text" name="dyn_nik_kedua_1" class="form-control dyn-inp dyn-nik" maxlength="16" placeholder="Ketik NIK..." oninput="autoFillWarga(this)">
                        <div class="status-cari" style="font-size:11.5px; margin-top:4px; height:15px; font-weight:500;"></div>
                    </div>
                    <div class="form-group" style="flex:1"><label>Nama Lengkap</label><input type="text" name="dyn_nama_kedua_1" class="form-control dyn-inp dyn-nama" required placeholder="Ketik Manual / Auto" oninput="updateLivePreview()"></div>
                </div>
                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1"><label>Umur (Tahun)</label><input type="number" name="dyn_umur_kedua_1" class="form-control dyn-inp dyn-umur" placeholder="Cth: 35" oninput="updateLivePreview()"></div>
                    <div class="form-group" style="flex:1"><label>Pekerjaan</label><input type="text" name="dyn_kerja_kedua_1" class="form-control dyn-inp dyn-kerja" placeholder="Cth: Petani" oninput="updateLivePreview()"></div>
                </div>
                <div class="form-group" style="margin-bottom:0;"><label>Alamat Lengkap</label><input type="text" name="dyn_alamat_kedua_1" class="form-control dyn-inp dyn-alamat" placeholder="RT / RW / Desa" oninput="updateLivePreview()"></div>
            </div>
        </div>

        <div style="background: #f1f5f9; padding: 8px 12px; border-radius: 6px; margin-bottom: 15px; font-weight: bold; color: #0f172a; font-size: 13px;">B. Spesifikasi Tanah & Harga</div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1"><label>Jenis Tanah</label><select name="dyn_jenis_tanah" class="form-control dyn-inp" onchange="updateLivePreview()"><option value="Pekarangan">Pekarangan</option><option value="Pertanian (Sawah)">Pertanian (Sawah)</option></select></div>
            <div class="form-group" style="flex:1"><label>Lokasi Tanah</label><input type="text" name="dyn_lokasi_tanah" class="form-control dyn-inp" placeholder="Cth: Dusun Belenje" oninput="updateLivePreview()"></div>
        </div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1"><label>Luas (M²)</label><input type="text" name="dyn_luas_angka" class="form-control dyn-inp" required placeholder="Cth: 500" oninput="updateLivePreview()"></div>
            <div class="form-group" style="flex:2"><label>Luas Terbilang</label><input type="text" name="dyn_luas_huruf" class="form-control dyn-inp" placeholder="Cth: Lima Ratus" oninput="updateLivePreview()"></div>
        </div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1"><label>Harga (Rp)</label><input type="text" name="dyn_harga_angka" class="form-control dyn-inp" required placeholder="Cth: 15.000.000" oninput="updateLivePreview()"></div>
            <div class="form-group" style="flex:2"><label>Harga Terbilang</label><input type="text" name="dyn_harga_huruf" class="form-control dyn-inp" placeholder="Cth: Lima Belas Juta" oninput="updateLivePreview()"></div>
        </div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1"><label>Nomor SPPT</label><input type="text" name="dyn_no_sppt" class="form-control dyn-inp" placeholder="Cth: 52.02..." oninput="updateLivePreview()"></div>
            <div class="form-group" style="flex:1"><label>Tahun Jual Beli</label><input type="text" name="dyn_tahun_jual" class="form-control dyn-inp" placeholder="Cth: 2024" oninput="updateLivePreview()"></div>
        </div>

        <div style="background: #f1f5f9; padding: 8px 12px; border-radius: 6px; margin-bottom: 15px; font-weight: bold; color: #0f172a; font-size: 13px;">C. Batas-Batas Tanah</div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1"><label>Utara</label><input type="text" name="dyn_batas_utara" class="form-control dyn-inp" placeholder="Batas Utara..." oninput="updateLivePreview()"></div>
            <div class="form-group" style="flex:1"><label>Selatan</label><input type="text" name="dyn_batas_selatan" class="form-control dyn-inp" placeholder="Batas Selatan..." oninput="updateLivePreview()"></div>
        </div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1"><label>Timur</label><input type="text" name="dyn_batas_timur" class="form-control dyn-inp" placeholder="Batas Timur..." oninput="updateLivePreview()"></div>
            <div class="form-group" style="flex:1"><label>Barat</label><input type="text" name="dyn_batas_barat" class="form-control dyn-inp" placeholder="Batas Barat..." oninput="updateLivePreview()"></div>
        </div>

        <div style="background: #f1f5f9; padding: 8px 12px; border-radius: 6px; margin-bottom: 15px; font-weight: bold; color: #0f172a; font-size: 13px;">D. Data Saksi (Auto-fill / Manual)</div>
        <div class="autofill-group" style="border:1px dashed #cbd5e1; padding:10px; border-radius:8px; margin-bottom:10px;">
            <div style="font-weight:bold; font-size:12px; margin-bottom:5px; color:#f59e0b;">Saksi 1</div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1; margin-bottom:10px;">
                    <input type="text" name="dyn_nik_saksi1" class="form-control dyn-inp dyn-nik" maxlength="16" placeholder="NIK Saksi 1..." oninput="autoFillWarga(this)">
                    <div class="status-cari" style="font-size:11px; margin-top:2px; height:12px;"></div>
                </div>
                <div class="form-group" style="flex:1; margin-bottom:10px;"><input type="text" name="dyn_nama_saksi1" class="form-control dyn-inp dyn-nama" placeholder="Nama Lengkap" oninput="updateLivePreview()"></div>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:0;">
                <div class="form-group" style="flex:1; margin-bottom:0;"><input type="number" name="dyn_umur_saksi1" class="form-control dyn-inp dyn-umur" placeholder="Umur" oninput="updateLivePreview()"></div>
                <div class="form-group" style="flex:1; margin-bottom:0;"><input type="text" name="dyn_kerja_saksi1" class="form-control dyn-inp dyn-kerja" placeholder="Pekerjaan" oninput="updateLivePreview()"></div>
            </div>
        </div>
        <div class="autofill-group" style="border:1px dashed #cbd5e1; padding:10px; border-radius:8px; margin-bottom:0;">
            <div style="font-weight:bold; font-size:12px; margin-bottom:5px; color:#f59e0b;">Saksi 2</div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1; margin-bottom:10px;">
                    <input type="text" name="dyn_nik_saksi2" class="form-control dyn-inp dyn-nik" maxlength="16" placeholder="NIK Saksi 2..." oninput="autoFillWarga(this)">
                    <div class="status-cari" style="font-size:11px; margin-top:2px; height:12px;"></div>
                </div>
                <div class="form-group" style="flex:1; margin-bottom:10px;"><input type="text" name="dyn_nama_saksi2" class="form-control dyn-inp dyn-nama" placeholder="Nama Lengkap" oninput="updateLivePreview()"></div>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:0;">
                <div class="form-group" style="flex:1; margin-bottom:0;"><input type="number" name="dyn_umur_saksi2" class="form-control dyn-inp dyn-umur" placeholder="Umur" oninput="updateLivePreview()"></div>
                <div class="form-group" style="flex:1; margin-bottom:0;"><input type="text" name="dyn_kerja_saksi2" class="form-control dyn-inp dyn-kerja" placeholder="Pekerjaan" oninput="updateLivePreview()"></div>
            </div>
        </div>
    `,

    'Surat pernyataan Waris': `
        <input type="hidden" name="keperluan" value="Keterangan Waris dan Pembagian Hak">
        <input type="hidden" name="jumlah_waris" id="jumlahWaris" value="1">
        
        <div style="background: #f1f5f9; padding: 8px 12px; border-radius: 6px; margin-bottom: 10px; font-weight: bold; color: #0f172a; font-size: 13px;">A. Data Pewaris (Almarhum/ah)</div>
        <div class="autofill-group" style="border:1px dashed #cbd5e1; padding:15px; border-radius:8px; margin-bottom:15px; background:#fff;">
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1">
                    <label>NIK Pewaris (Auto-fill)</label>
                    <input type="text" class="form-control dyn-inp dyn-nik" maxlength="16" placeholder="Ketik NIK..." oninput="autoFillWarga(this)">
                    <div class="status-cari" style="font-size:11px; margin-top:4px; height:15px;"></div>
                </div>
                <div class="form-group" style="flex:1"><label>Nama Almarhum/ah</label><input type="text" name="dyn_nama_pewaris" class="form-control dyn-inp dyn-nama" required placeholder="Wajib Diisi" oninput="updateLivePreview()"></div>
            </div>
            <div class="form-group"><label>Alamat Terakhir</label><input type="text" name="dyn_alamat_pewaris" class="form-control dyn-inp dyn-alamat" placeholder="Cth: Mapasan Desa Serage, Kecamatan Praya..." oninput="updateLivePreview()"></div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div class="form-group" style="flex:1; margin-bottom:0;"><label>Tahun Meninggal</label><input type="number" name="dyn_tahun_meninggal" class="form-control dyn-inp" placeholder="Cth: 2000" oninput="updateLivePreview()"></div>
                <div class="form-group" style="flex:1; margin-bottom:0;"><label>Nama Suami/Istri</label><input type="text" name="dyn_nama_pasangan" class="form-control dyn-inp" placeholder="Cth: Ribang" oninput="updateLivePreview()"></div>
                <div class="form-group" style="flex:1; margin-bottom:0;">
                    <label>Status Pasangan</label>
                    <select name="dyn_status_pasangan" class="form-control dyn-inp" onchange="updateLivePreview()">
                        <option value="hidup">Masih Hidup</option>
                        <option value="meninggal">Telah Meninggal</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:0;"><label>Jumlah Anak (Terbilang)</label><input type="text" name="dyn_jumlah_waris_huruf" class="form-control dyn-inp" placeholder="Cth: SATU" oninput="updateLivePreview()"></div>
        </div>

        <div style="background: #f1f5f9; padding: 8px 12px; border-radius: 6px; margin-bottom: 10px; font-weight: bold; color: #0f172a; font-size: 13px; display: flex; justify-content: space-between; align-items: center;">
            <span style="display:flex; align-items:center; gap:8px;"><i class="fa-solid fa-users"></i> B. Daftar Ahli Waris (Anak-anak)</span>
            <button type="button" onclick="tambahAhliWaris()" style="background:#10b981; color:#fff; border:none; padding:6px 12px; border-radius:4px; font-size:11px; cursor:pointer; font-weight:bold;"><i class="fa-solid fa-plus"></i> Tambah Ahli Waris</button>
        </div>
        
        <div id="wadahAhliWaris">
            <div class="autofill-group waris-item" style="border:1px solid #cbd5e1; padding:15px; border-radius:8px; margin-bottom:15px; background:#ffffff;">
                <div class="title-waris" style="font-weight:bold; font-size:12px; margin-bottom:10px; color:#1a6f76;">Ahli Waris 1</div>
                <div style="display:flex; gap:10px; margin-bottom:0;">
                    <div class="form-group" style="flex:1; margin-bottom:0;">
                        <input type="text" class="form-control dyn-inp dyn-nik" maxlength="16" placeholder="Ketik NIK..." oninput="autoFillWarga(this)">
                        <div class="status-cari" style="font-size:11px; margin-top:4px; height:15px;"></div>
                    </div>
                    <div class="form-group" style="flex:2; margin-bottom:0;"><input type="text" name="dyn_nama_waris_1" class="form-control dyn-inp dyn-nama" required placeholder="Nama Lengkap Ahli Waris (Wajib)" oninput="updateLivePreview()"></div>
                </div>
            </div>
        </div>

        <div style="background: #f1f5f9; padding: 8px 12px; border-radius: 6px; margin-bottom: 15px; font-weight: bold; color: #0f172a; font-size: 13px;">C. Spesifikasi Objek Tanah</div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1"><label>Jenis Tanah</label><select name="dyn_jenis_tanah" class="form-control dyn-inp" onchange="updateLivePreview()"><option value="Pertanian/Pekarangan">Pertanian/Pekarangan</option><option value="Kebun">Kebun</option><option value="Tegalan">Tegalan</option></select></div>
            <div class="form-group" style="flex:1"><label>Lokasi (Kampung/Lorong)</label><input type="text" name="dyn_lokasi_tanah" class="form-control dyn-inp" placeholder="Cth: Mapasan" oninput="updateLivePreview()"></div>
        </div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1"><label>Pipil No.</label><input type="text" name="dyn_pipil" class="form-control dyn-inp" placeholder="Kosongkan jika tidak ada" oninput="updateLivePreview()"></div>
            <div class="form-group" style="flex:1"><label>Persil No.</label><input type="text" name="dyn_persil" class="form-control dyn-inp" placeholder="Kosongkan jika tidak ada" oninput="updateLivePreview()"></div>
            <div class="form-group" style="flex:1"><label>Klas</label><input type="text" name="dyn_klas" class="form-control dyn-inp" placeholder="Cth: I, II, III" oninput="updateLivePreview()"></div>
        </div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1"><label>Sertipikat No.</label><input type="text" name="dyn_sertipikat" class="form-control dyn-inp" placeholder="Kosongkan jika tidak ada" oninput="updateLivePreview()"></div>
            <div class="form-group" style="flex:1"><label>SPPT No.</label><input type="text" name="dyn_no_sppt" class="form-control dyn-inp" placeholder="Kosongkan..." oninput="updateLivePreview()"></div>
            <div class="form-group" style="flex:1"><label>Luas (&plusmn; M²)</label><input type="text" name="dyn_luas_angka" class="form-control dyn-inp"  placeholder="Cth: 10,187 " oninput="updateLivePreview()"></div>
        </div>

        <div style="background: #f1f5f9; padding: 8px 12px; border-radius: 6px; margin-bottom: 15px; font-weight: bold; color: #0f172a; font-size: 13px;">D. Batas-Batas Tanah</div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1"><label>Utara</label><input type="text" name="dyn_batas_utara" class="form-control dyn-inp" placeholder="Batas Utara" oninput="updateLivePreview()"></div>
            <div class="form-group" style="flex:1"><label>Selatan</label><input type="text" name="dyn_batas_selatan" class="form-control dyn-inp" placeholder="Batas Selatan" oninput="updateLivePreview()"></div>
        </div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1"><label>Timur</label><input type="text" name="dyn_batas_timur" class="form-control dyn-inp" placeholder="Batas Timur" oninput="updateLivePreview()"></div>
            <div class="form-group" style="flex:1"><label>Barat</label><input type="text" name="dyn_batas_barat" class="form-control dyn-inp" placeholder="Batas Barat" oninput="updateLivePreview()"></div>
        </div>

        <div style="background: #f1f5f9; padding: 8px 12px; border-radius: 6px; margin-bottom: 15px; font-weight: bold; color: #0f172a; font-size: 13px;">E. Data Saksi (Auto-fill / Manual)</div>
        <div class="autofill-group" style="border:1px dashed #cbd5e1; padding:10px; border-radius:8px; margin-bottom:10px;">
            <div style="font-weight:bold; font-size:12px; margin-bottom:5px; color:#f59e0b;">Saksi 1</div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1; margin-bottom:10px;">
                    <input type="text" name="dyn_nik_saksi1" class="form-control dyn-inp dyn-nik" maxlength="16" placeholder="NIK Saksi 1..." oninput="autoFillWarga(this)">
                    <div class="status-cari" style="font-size:11px; margin-top:2px; height:12px;"></div>
                </div>
                <div class="form-group" style="flex:1; margin-bottom:10px;"><input type="text" name="dyn_nama_saksi1" class="form-control dyn-inp dyn-nama" required placeholder="Nama Lengkap" oninput="updateLivePreview()"></div>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:0;">
                <div class="form-group" style="flex:1; margin-bottom:0;"><input type="number" name="dyn_umur_saksi1" class="form-control dyn-inp dyn-umur" placeholder="Umur" oninput="updateLivePreview()"></div>
                <div class="form-group" style="flex:1; margin-bottom:0;"><input type="text" name="dyn_kerja_saksi1" class="form-control dyn-inp dyn-kerja" placeholder="Pekerjaan" oninput="updateLivePreview()"></div>
            </div>
        </div>
        <div class="autofill-group" style="border:1px dashed #cbd5e1; padding:10px; border-radius:8px; margin-bottom:0;">
            <div style="font-weight:bold; font-size:12px; margin-bottom:5px; color:#f59e0b;">Saksi 2</div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1; margin-bottom:10px;">
                    <input type="text" name="dyn_nik_saksi2" class="form-control dyn-inp dyn-nik" maxlength="16" placeholder="NIK Saksi 2..." oninput="autoFillWarga(this)">
                    <div class="status-cari" style="font-size:11px; margin-top:2px; height:12px;"></div>
                </div>
                <div class="form-group" style="flex:1; margin-bottom:10px;"><input type="text" name="dyn_nama_saksi2" class="form-control dyn-inp dyn-nama" required placeholder="Nama Lengkap" oninput="updateLivePreview()"></div>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:0;">
                <div class="form-group" style="flex:1; margin-bottom:0;"><input type="number" name="dyn_umur_saksi2" class="form-control dyn-inp dyn-umur" placeholder="Umur" oninput="updateLivePreview()"></div>
                <div class="form-group" style="flex:1; margin-bottom:0;"><input type="text" name="dyn_kerja_saksi2" class="form-control dyn-inp dyn-kerja" placeholder="Pekerjaan" oninput="updateLivePreview()"></div>
            </div>
        </div>
    `,
    'Surat Keterangan Ahli Waris': `
        <div style="background: #f1f5f9; padding: 8px 12px; border-radius: 6px; margin-bottom: 10px; font-weight: bold; color: #0f172a; font-size: 13px;">A. Data Pewaris (Almarhum/ah)</div>
        <div class="form-group"><label>Nama Almarhum/ah</label><input type="text" name="dyn_nama_alm" class="form-control dyn-inp" placeholder="Cth: H. Nasir" oninput="updateLivePreview()"></div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1"><label>Tgl Meninggal</label><input type="date" name="dyn_tgl_meninggal_alm" class="form-control dyn-inp" oninput="updateLivePreview()"></div>
            <div class="form-group" style="flex:1"><label>Tempat Meninggal</label><input type="text" name="dyn_tempat_meninggal_alm" class="form-control dyn-inp" placeholder="Cth: Rumah / RSUD" oninput="updateLivePreview()"></div>
        </div>
        
        <input type="hidden" name="jumlah_ahli_waris" id="jumlahAhliWaris" value="1">
        <div style="background: #f1f5f9; padding: 8px 12px; border-radius: 6px; margin-bottom: 10px; font-weight: bold; color: #0f172a; font-size: 13px; display: flex; justify-content: space-between; align-items: center;">
            <span style="display:flex; align-items:center; gap:8px;"><i class="fa-solid fa-users"></i> B. Daftar Ahli Waris</span>
            <button type="button" onclick="tambahDaftarAhliWaris()" style="background:#10b981; color:#fff; border:none; padding:6px 12px; border-radius:4px; font-size:11px; cursor:pointer; font-weight:bold;"><i class="fa-solid fa-plus"></i> Tambah</button>
        </div>
        
        <div id="wadahDaftarAhliWaris">
            <div class="aw-item" style="border:1px dashed #cbd5e1; padding:10px; border-radius:8px; margin-bottom:10px;">
                <div style="display:flex; gap:10px; margin-bottom:10px;">
                    <div class="form-group" style="flex:2; margin-bottom:0;"><input type="text" name="dyn_aw_nama_1" class="form-control dyn-inp" placeholder="Nama Ahli Waris" oninput="updateLivePreview()"></div>
                    <div class="form-group" style="flex:1; margin-bottom:0;"><input type="text" name="dyn_aw_umur_1" class="form-control dyn-inp" placeholder="Umur (Angka)" oninput="updateLivePreview()"></div>
                </div>
                <div class="form-group" style="margin-bottom:0;"><input type="text" name="dyn_aw_hubungan_1" class="form-control dyn-inp" placeholder="Hubungan (Cth: Anak Kandung)" oninput="updateLivePreview()"></div>
            </div>
        </div>
        <div class="form-group" style="margin-top:10px; margin-bottom:0;"><label>Tujuan Pembuatan</label><input type="text" name="keperluan" class="form-control dyn-inp" placeholder="Cth: Pencairan Dana Taspen" oninput="updateLivePreview()"></div>
    `,
    'Surat Keterangan Kematian': `
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1"><label>Tanggal Meninggal</label><input type="date" name="dyn_tgl_meninggal" class="form-control dyn-inp" oninput="updateLivePreview()"></div>
            <div class="form-group" style="flex:1"><label>Tempat Meninggal</label><input type="text" name="dyn_tempat_meninggal" class="form-control dyn-inp" placeholder="Cth: RSUD / Rumah" oninput="updateLivePreview()"></div>
        </div>
        <div class="form-group" style="margin-bottom:0;"><label>Penyebab Kematian</label><input type="text" name="dyn_penyebab" class="form-control dyn-inp" placeholder="Cth: Sakit / Karena Usia (Tua)" oninput="updateLivePreview()"></div>
    `,
    'Surat Keterangan Tidak Mampu (SKTM)': `
        <div class="form-group">
            <label>Keperluan / Tujuan</label>
            <input type="text" name="keperluan" class="form-control dyn-inp" placeholder="Cth: Pembuatan PASPOR / Daftar Kuliah" oninput="updateLivePreview()">
        </div>
        
        <div style="background: #f1f5f9; padding: 8px 12px; border-radius: 6px; margin-bottom: 10px; font-weight: bold; color: #0f172a; font-size: 13px;">
            Data Orang yang Dituju / Anak
        </div>
        
        <!-- KOLOM NIK SEBAGAI TRIGGER AUTOFILL -->
        <div class="form-group">
            <label>NIK Anak <small class="text-muted" id="status_autofill" style="font-weight:normal; float:right;">*Ketik 16 digit untuk autofill</small></label>
            <input type="text" class="form-control" placeholder="Ketik NIK Anak (opsional)..." oninput="autofillSasaran(this.value)" maxlength="16">
        </div>
        
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="dyn_nama_sasaran" id="dyn_nama_sasaran" class="form-control dyn-inp" placeholder="Cth: SITI SOLEHA" oninput="updateLivePreview()">
        </div>
        
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1">
                <label>Umur (Tahun)</label>
                <input type="text" name="dyn_umur_sasaran" id="dyn_umur_sasaran" class="form-control dyn-inp" placeholder="Cth: 34" oninput="updateLivePreview()">
            </div>
            <div class="form-group" style="flex:1">
                <label>Jenis Kelamin</label>
                <input type="text" name="dyn_jk_sasaran" id="dyn_jk_sasaran" class="form-control dyn-inp" placeholder="Cth: PEREMPUAN" oninput="updateLivePreview()">
            </div>
        </div>
        
        <div class="form-group">
            <label>Tempat, Tgl Lahir</label>
            <input type="text" name="dyn_ttl_sasaran" id="dyn_ttl_sasaran" class="form-control dyn-inp" placeholder="Cth: SULUNG, 01-07-1992" oninput="updateLivePreview()">
        </div>
        
        <div class="form-group" style="margin-bottom:0;">
            <label>Nama Instansi / Tujuan Sekolah</label>
            <textarea name="dyn_sekolah_sasaran" class="form-control dyn-inp" rows="2" placeholder="Cth: Universitas Bumigora Jln. Ismail Marzuki Cakranegara, Mataram." oninput="updateLivePreview()"></textarea>
        </div>
    `,
    'DEFAULT': `<div class="form-group" style="margin-bottom:0;"><label>Keperluan Surat</label><input type="text" name="keperluan" class="form-control dyn-inp" placeholder="Contoh: Persyaratan Administrasi"></div>`
};

// Fungsi Autofill Optimal (Tanpa tombol, berjalan senyap)
function autofillSasaran(nik) {
    let statusTeks = document.getElementById('status_autofill');
    
    // Cek jika panjang karakter tepat 16 (standar NIK KTP)
    if (nik.length === 16) {
        statusTeks.innerHTML = '<span style="color:#3b82f6;"><i class="fa fa-spinner fa-spin"></i> Mencari...</span>';
        
        fetch('../ajax_get_penduduk.php?nik=' + nik)
        .then(response => response.json())
        .then(data => {
            if (data && data.nama_lengkap) {
                // 1. Eksekusi pengisian data ke input manual
                document.getElementById('dyn_nama_sasaran').value = data.nama_lengkap;
                document.getElementById('dyn_jk_sasaran').value = (data.jenis_kelamin === 'L' || data.jenis_kelamin === 'Laki-laki' || data.jenis_kelamin === 'LAKI-LAKI') ? 'LAKI-LAKI' : 'PEREMPUAN';
                document.getElementById('dyn_umur_sasaran').value = data.umur;
                
                // 2. Format Tanggal Lahir (Ubah YYYY-MM-DD ke DD-MM-YYYY)
                let tglFormat = data.tgl_lahir;
                if (tglFormat && tglFormat.includes('-')) {
                    let parts = tglFormat.split('-');
                    if (parts[0].length === 4) { 
                        tglFormat = parts[2] + '-' + parts[1] + '-' + parts[0];
                    }
                }
                document.getElementById('dyn_ttl_sasaran').value = (data.tempat_lahir ? data.tempat_lahir : '') + ', ' + (tglFormat ? tglFormat : '');
                
                // 3. Update Status dan Pratinjau
                statusTeks.innerHTML = '<span style="color:#10b981;"><i class="fa fa-check"></i> Ditemukan</span>';
                if (typeof updateLivePreview === 'function') updateLivePreview();
                
            } else {
                statusTeks.innerHTML = '<span style="color:#ef4444;">Data tidak ditemukan</span>';
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            statusTeks.innerHTML = '<span style="color:#ef4444;">Gagal mengambil data</span>';
        });
    } 
    // Kembalikan ke teks default jika input dikosongkan atau kurang dari 16 digit
    else if (nik.length === 0) {
        statusTeks.innerHTML = '*Ketik 16 digit untuk autofill';
    } 
    else {
        statusTeks.innerHTML = '*Menunggu 16 digit...';
    }
}

// Tambahkan duplikasi logikanya untuk Hibah agar sejalan
configSurat['Surat Pernyataan Hibah'] = configSurat['Surat Pernyataan Jual Beli'].replace(/Jual Beli/g, 'Hibah').replace(/Pembeli/g, 'Penerima');

inputJenis.addEventListener('change', function() {
    let selectedOption = this.options[this.selectedIndex];
    let namaSurat = selectedOption.getAttribute('data-nama');
    let htmlInput = configSurat[namaSurat] || (this.value ? configSurat['DEFAULT'] : '<p style="margin:0; font-size:12.5px; color:#64748b; text-align:center;"><i class="fa-solid fa-circle-info"></i> Pilih Jenis Surat untuk menampilkan isian spesifik.</p>');
    
    dynamicInputArea.innerHTML = htmlInput;
    dynamicInputArea.querySelectorAll('.dyn-inp').forEach(inp => {
        inp.addEventListener('input', () => { clearTimeout(timeoutId); timeoutId = setTimeout(updateLivePreview, 300); });
    });
    updateLivePreview();
});

manualInputs.forEach(input => {
    input.addEventListener('input', () => { clearTimeout(timeoutId); timeoutId = setTimeout(updateLivePreview, 300); });
});

// --- FUNGSI AUTO-FILL KHUSUS PIHAK KEDUA (HIBAH & JUAL BELI) ---
function cariPenerima(el) {
    clearTimeout(penerimaTimeoutId);
    let nik = el.value;
    let parent = el.closest('.penerima-item');
    let statusDiv = parent.querySelector('.status-cari');
    
    if(nik.length < 15) {
        statusDiv.innerHTML = '';
        updateLivePreview(); 
        return;
    }

    statusDiv.innerHTML = '<span style="color:#f59e0b;"><i class="fa-solid fa-spinner fa-spin"></i> Mencari data...</span>';
    
    penerimaTimeoutId = setTimeout(() => {
        fetch(`../ajax_get_penduduk.php?nik=${nik}`)
        .then(res => res.json())
        .then(data => {
            if(!data.error) {
                let umur = '';
                if (data.tgl_lahir) {
                    let tglLahir = new Date(data.tgl_lahir);
                    let hariIni = new Date();
                    umur = hariIni.getFullYear() - tglLahir.getFullYear();
                }
                
                // FORMAT ALAMAT: Hanya Dusun, Desa, Kec, Kab (TANPA RT/RW)
                let dusunText = data.nama_dusun ? `${data.nama_dusun} ` : '';
                let desaText = data.kel_desa ? data.kel_desa : 'Serage';
                let kecText = data.kecamatan ? data.kecamatan : 'Praya Barat Daya';
                let kabText = data.kabupaten ? data.kabupaten : 'Lombok Tengah';
                let alamatFormal = `${dusunText}Desa ${desaText}, Kecamatan ${kecText}, Kabupaten ${kabText}`;

                let inpNama = parent.querySelector('.dyn-nama');
                let inpUmur = parent.querySelector('.dyn-umur');
                let inpKerja = parent.querySelector('.dyn-kerja');
                let inpAlamat = parent.querySelector('.dyn-alamat');

                if(inpNama) inpNama.value = data.nama_lengkap || '';
                if(inpUmur) inpUmur.value = data.umur ? data.umur : umur;
                if(inpKerja) inpKerja.value = data.pekerjaan || '';
                if(inpAlamat) inpAlamat.value = alamatFormal;
                
                statusDiv.innerHTML = '<span style="color:#10b981;"><i class="fa-solid fa-check"></i> Auto-fill berhasil</span>';
            } else {
                statusDiv.innerHTML = '<span style="color:#ef4444;"><i class="fa-solid fa-xmark"></i> Tidak terdaftar. Isi manual.</span>';
            }
            updateLivePreview(); 
        }).catch(err => {
            statusDiv.innerHTML = '<span style="color:#ef4444;">Gagal mengambil data.</span>';
            updateLivePreview();
        });
    }, 400);
}

// --- FUNGSI AUTO-FILL UNIVERSAL (Saksi, Pewaris) ---
function autoFillWarga(el) {
    clearTimeout(penerimaTimeoutId);
    let nik = el.value;
    let parent = el.closest('.autofill-group');
    let statusDiv = parent.querySelector('.status-cari');
    
    if(nik.length < 15) {
        statusDiv.innerHTML = '';
        updateLivePreview(); 
        return;
    }

    statusDiv.innerHTML = '<span style="color:#f59e0b;"><i class="fa-solid fa-spinner fa-spin"></i> Mencari data warga...</span>';
    
    penerimaTimeoutId = setTimeout(() => {
        fetch(`../ajax_get_penduduk.php?nik=${nik}`)
        .then(res => res.json())
        .then(data => {
            if(!data.error) {
                let umur = '';
                if (data.tgl_lahir) {
                    let tglLahir = new Date(data.tgl_lahir);
                    let hariIni = new Date();
                    umur = hariIni.getFullYear() - tglLahir.getFullYear();
                }
                
                // FORMAT ALAMAT: Hanya Dusun, Desa, Kec, Kab (TANPA RT/RW)
                let dusunText = data.nama_dusun ? `${data.nama_dusun} ` : '';
                let desaText = data.kel_desa ? data.kel_desa : 'Serage';
                let kecText = data.kecamatan ? data.kecamatan : 'Praya Barat Daya';
                let kabText = data.kabupaten ? data.kabupaten : 'Lombok Tengah';
                let alamatFormal = `${dusunText}Desa ${desaText}, Kecamatan ${kecText}, Kabupaten ${kabText}`;

                let inpNama = parent.querySelector('.dyn-nama');
                let inpUmur = parent.querySelector('.dyn-umur');
                let inpKerja = parent.querySelector('.dyn-kerja');
                let inpAlamat = parent.querySelector('.dyn-alamat');

                if(inpNama) inpNama.value = data.nama_lengkap || '';
                if(inpUmur) inpUmur.value = data.umur ? data.umur : umur;
                if(inpKerja) inpKerja.value = data.pekerjaan || '';
                if(inpAlamat) inpAlamat.value = alamatFormal;
                
                statusDiv.innerHTML = '<span style="color:#10b981;"><i class="fa-solid fa-check"></i> Auto-fill berhasil</span>';
            } else {
                statusDiv.innerHTML = '<span style="color:#ef4444;"><i class="fa-solid fa-xmark"></i> Tidak terdaftar. Isi manual.</span>';
            }
            updateLivePreview(); 
        }).catch(err => {
            statusDiv.innerHTML = '<span style="color:#ef4444;">Gagal mengambil data.</span>';
            updateLivePreview();
        });
    }, 400);
}

// ================= FUNGSI TAMBAH PIHAK KEDUA (DINAMIS) =================
// --- FUNGSI TAMBAH PIHAK KEDUA ---
function tambahPembeli() {
    let wadah = document.getElementById('wadahPenerima');
    let count = wadah.querySelectorAll('.penerima-item').length + 1;
    document.getElementById('jumlahPenerima').value = count;
    
    let isJualBeli = document.getElementById('inputJenis').options[document.getElementById('inputJenis').selectedIndex].getAttribute('data-nama') === 'Surat Pernyataan Jual Beli';
    let labelOrang = isJualBeli ? 'Pembeli' : 'Penerima';
    
    let htmlBaru = `
        <div class="autofill-group penerima-item" style="border:1px solid #cbd5e1; padding:15px; border-radius:8px; margin-bottom:15px; background:#ffffff; position:relative;">
            <button type="button" onclick="hapusPembeli(this)" style="position:absolute; top:15px; right:15px; background:#fee2e2; color:#ef4444; border:none; padding:5px 8px; border-radius:4px; cursor:pointer;" title="Hapus"><i class="fa-solid fa-trash"></i></button>
            <div class="title-penerima" style="font-weight:bold; font-size:12px; margin-bottom:10px; color:#1a6f76;">${labelOrang} ${count}</div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1">
                    <label>NIK Pihak Kedua</label>
                    <input type="text" name="dyn_nik_kedua_${count}" class="form-control dyn-inp dyn-nik" maxlength="16" placeholder="Ketik NIK..." oninput="autoFillWarga(this)">
                    <div class="status-cari" style="font-size:11px; margin-top:4px; height:15px;"></div>
                </div>
                <div class="form-group" style="flex:1"><label>Nama Lengkap</label><input type="text" name="dyn_nama_kedua_${count}" class="form-control dyn-inp dyn-nama" required placeholder="Ketik Manual / Auto" oninput="updateLivePreview()"></div>
            </div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1"><label>Umur (Tahun)</label><input type="number" name="dyn_umur_kedua_${count}" class="form-control dyn-inp dyn-umur" placeholder="Cth: 35" oninput="updateLivePreview()"></div>
                <div class="form-group" style="flex:1"><label>Pekerjaan</label><input type="text" name="dyn_kerja_kedua_${count}" class="form-control dyn-inp dyn-kerja" placeholder="Cth: Petani" oninput="updateLivePreview()"></div>
            </div>
            <div class="form-group" style="margin-bottom:0;"><label>Alamat Lengkap</label><input type="text" name="dyn_alamat_kedua_${count}" class="form-control dyn-inp dyn-alamat" placeholder="RT / RW / Desa" oninput="updateLivePreview()"></div>
        </div>
    `;
    wadah.insertAdjacentHTML('beforeend', htmlBaru);
    
    let itemBaru = wadah.lastElementChild;
    itemBaru.querySelectorAll('.dyn-inp').forEach(inp => {
        inp.addEventListener('input', () => { clearTimeout(timeoutId); timeoutId = setTimeout(updateLivePreview, 300); });
    });
    updateLivePreview();
}

// --- FUNGSI TAMBAH AHLI WARIS (WARISAN) ---
function tambahAhliWaris() {
    let wadah = document.getElementById('wadahAhliWaris');
    let count = wadah.querySelectorAll('.waris-item').length + 1;
    document.getElementById('jumlahWaris').value = count;
    
    let htmlBaru = `
        <div class="autofill-group waris-item" style="border:1px solid #cbd5e1; padding:15px; border-radius:8px; margin-bottom:15px; background:#ffffff; position:relative;">
            <button type="button" onclick="hapusAhliWaris(this)" style="position:absolute; top:15px; right:15px; background:#fee2e2; color:#ef4444; border:none; padding:5px 8px; border-radius:4px; cursor:pointer;" title="Hapus"><i class="fa-solid fa-trash"></i></button>
            <div class="title-waris" style="font-weight:bold; font-size:12px; margin-bottom:10px; color:#1a6f76;">Ahli Waris ${count}</div>
            <div style="display:flex; gap:10px; margin-bottom:0;">
                <div class="form-group" style="flex:1; margin-bottom:0;">
                    <input type="text" class="form-control dyn-inp dyn-nik" maxlength="16" placeholder="Ketik NIK..." oninput="autoFillWarga(this)">
                    <div class="status-cari" style="font-size:11px; margin-top:4px; height:15px;"></div>
                </div>
                <div class="form-group" style="flex:2; margin-bottom:0;"><input type="text" name="dyn_nama_waris_${count}" class="form-control dyn-inp dyn-nama" required placeholder="Nama Lengkap Ahli Waris" oninput="updateLivePreview()"></div>
            </div>
        </div>
    `;
    wadah.insertAdjacentHTML('beforeend', htmlBaru);
    
    let itemBaru = wadah.lastElementChild;
    itemBaru.querySelectorAll('.dyn-inp').forEach(inp => {
        inp.addEventListener('input', () => { clearTimeout(timeoutId); timeoutId = setTimeout(updateLivePreview, 300); });
    });
    updateLivePreview();
}
// --- FUNGSI TAMBAH DAFTAR AHLI WARIS ---
function tambahDaftarAhliWaris() {
    let wadah = document.getElementById('wadahDaftarAhliWaris');
    let count = wadah.querySelectorAll('.aw-item').length + 1;
    document.getElementById('jumlahAhliWaris').value = count;
    
    let htmlBaru = `
        <div class="aw-item" style="border:1px dashed #cbd5e1; padding:10px; border-radius:8px; margin-bottom:10px; position:relative;">
            <button type="button" onclick="this.parentElement.remove(); updateLivePreview();" style="position:absolute; top:10px; right:10px; background:#fee2e2; color:#ef4444; border:none; padding:2px 5px; border-radius:4px; cursor:pointer;" title="Hapus"><i class="fa-solid fa-trash"></i></button>
            <div style="font-weight:bold; font-size:11px; margin-bottom:5px; color:#1a6f76;">Ahli Waris ${count}</div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div class="form-group" style="flex:2; margin-bottom:0;"><input type="text" name="dyn_aw_nama_${count}" class="form-control dyn-inp" placeholder="Nama Ahli Waris" oninput="updateLivePreview()"></div>
                <div class="form-group" style="flex:1; margin-bottom:0;"><input type="text" name="dyn_aw_umur_${count}" class="form-control dyn-inp" placeholder="Umur (Angka)" oninput="updateLivePreview()"></div>
            </div>
            <div class="form-group" style="margin-bottom:0;"><input type="text" name="dyn_aw_hubungan_${count}" class="form-control dyn-inp" placeholder="Hubungan (Cth: Anak Kandung)" oninput="updateLivePreview()"></div>
        </div>
    `;
    wadah.insertAdjacentHTML('beforeend', htmlBaru);
    wadah.lastElementChild.querySelectorAll('.dyn-inp').forEach(inp => {
        inp.addEventListener('input', () => { clearTimeout(timeoutId); timeoutId = setTimeout(updateLivePreview, 300); });
    });
    updateLivePreview();
}

function hapusAhliWaris(btn) {
    btn.closest('.waris-item').remove();
    let items = document.querySelectorAll('#wadahAhliWaris .waris-item');
    document.getElementById('jumlahWaris').value = items.length;
    
    items.forEach((item, index) => {
        let i = index + 1;
        item.querySelector('.title-waris').innerText = 'Ahli Waris ' + i;
        item.querySelector('.dyn-nama').name = 'dyn_nama_waris_' + i;
    });
    updateLivePreview();
}

function hapusPembeli(btn) {
    btn.closest('.penerima-item').remove();
    let items = document.querySelectorAll('#wadahPenerima .penerima-item');
    document.getElementById('jumlahPenerima').value = items.length;
    
    let isJualBeli = document.getElementById('inputJenis').options[document.getElementById('inputJenis').selectedIndex].getAttribute('data-nama') === 'Surat Pernyataan Jual Beli';
    let labelOrang = isJualBeli ? 'Pembeli' : 'Penerima';
    
    items.forEach((item, index) => {
        let i = index + 1;
        item.querySelector('.title-penerima').innerText = labelOrang + ' ' + i;
        item.querySelector('.dyn-nik').name = 'dyn_nik_kedua_' + i;
        item.querySelector('.dyn-nama').name = 'dyn_nama_kedua_' + i;
        item.querySelector('.dyn-umur').name = 'dyn_umur_kedua_' + i;
        item.querySelector('.dyn-kerja').name = 'dyn_kerja_kedua_' + i;
        item.querySelector('.dyn-alamat').name = 'dyn_alamat_kedua_' + i;
    });
    updateLivePreview();
}

// ================= FUNGSI UTAMA LIVE PREVIEW =================
function updateLivePreview() {
    let nik = inputNik.value;
    let jenis = inputJenis.value;
    let isManual = modeManual.checked;

    if ((nik.length >= 15 || isManual) && jenis !== "") {
        if (framePreview.style.display === 'block') {
            previewOverlay.style.display = 'flex';
            framePreview.style.opacity = '0.5';
        } else {
            previewPlaceholder.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="font-size:45px; color:#cbd5e1;"></i><br><br>Membangun pratinjau...';
        }

        let formData = new FormData(document.getElementById('formSurat'));
        formData.append('is_preview', 'true');

        fetch('cetak_surat.php', { method: 'POST', body: formData })
        .then(response => response.text())
        .then(html => {
            previewPlaceholder.style.display = 'none';
            previewOverlay.style.display = 'none';
            framePreview.style.display = 'block';
            framePreview.style.opacity = '1';
            framePreview.srcdoc = html;
        }).catch(err => {
            console.error("Gagal memuat preview:", err);
            previewOverlay.style.display = 'none';
            framePreview.style.opacity = '1';
        });
    } else {
        framePreview.style.display = 'none';
        previewPlaceholder.style.display = 'block';
    }
}
</script>
<?php require_once 'includes/admin_footer.php'; ?>