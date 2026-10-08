<?php
// admin/mutasi.php
require_once '../config/koneksi.php';
require_once 'includes/admin_header.php';

$sweetalert_script = '';

// =================================================================
// 1. LOGIKA BACKEND: PROSES SIMPAN MUTASI BARU
// =================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_mutasi'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $sweetalert_script = "Swal.fire('Gagal!', 'Token keamanan tidak valid.', 'error');";
    } else {
        $penduduk_id  = (int)$_POST['penduduk_id'];
        $jenis_mutasi = $_POST['jenis_mutasi'];
        $tgl_mutasi   = $_POST['tanggal_mutasi'];
        $keterangan   = $koneksi->real_escape_string($_POST['keterangan']);
        $admin_id     = $_SESSION['admin_id'];
        $dokumen      = null;

        // Logika Upload Dokumen Bukti
        if (!empty($_FILES['dokumen']['name']) && $_FILES['dokumen']['error'] == 0) {
            $upload = upload_file_aman('dokumen', '../assets/uploads/mutasi/', ['pdf', 'jpg', 'jpeg', 'png'], 5120);
            if ($upload['status']) {
                $dokumen = $upload['nama_file'];
            } else {
                $sweetalert_script = "Swal.fire('Upload Gagal', '{$upload['pesan']}', 'error');";
            }
        }

        if (empty($sweetalert_script)) {
            $koneksi->begin_transaction();
            try {
                // Insert Riwayat Mutasi
                $stmt_mutasi = $koneksi->prepare("INSERT INTO mutasi_penduduk (penduduk_id, jenis_mutasi, tanggal_mutasi, keterangan, dokumen_pendukung, admin_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt_mutasi->bind_param("issssi", $penduduk_id, $jenis_mutasi, $tgl_mutasi, $keterangan, $dokumen, $admin_id);
                $stmt_mutasi->execute();
                $stmt_mutasi->close();

                // Update Status Penduduk
                $status_baru = ($jenis_mutasi == 'Mati') ? 'Meninggal' : 'Pindah';
                $stmt_update = $koneksi->prepare("UPDATE penduduk SET status_kependudukan = ? WHERE id = ?");
                $stmt_update->bind_param("si", $status_baru, $penduduk_id);
                $stmt_update->execute();
                $stmt_update->close();

                // Catat Log & Bersihkan Cache
                catat_log($koneksi, $admin_id, "Mutasi Data", "Melakukan mutasi $jenis_mutasi untuk ID Warga: $penduduk_id");
                bersihkan_cache_statistik();

                $koneksi->commit();
                $sweetalert_script = "Swal.fire({ title: 'Berhasil!', text: 'Data mutasi berhasil disimpan dan statistik telah diperbarui.', icon: 'success', confirmButtonColor: '#1a6f76' }).then(() => { window.location.href = 'mutasi.php'; });";
            } catch (Exception $e) {
                $koneksi->rollback();
                $sweetalert_script = "Swal.fire('Terjadi Kesalahan', 'Sistem gagal menyimpan data: {$e->getMessage()}', 'error');";
            }
        }
    }
}

// =================================================================
// 2. LOGIKA BACKEND: FILTER & PAGINATION RIWAYAT
// =================================================================
$where_clauses = ["1=1"];
$search = isset($_GET['search']) ? $koneksi->real_escape_string($_GET['search']) : '';
$f_jenis = isset($_GET['f_jenis']) ? $koneksi->real_escape_string($_GET['f_jenis']) : '';

if ($search != '') $where_clauses[] = "(p.nik LIKE '%$search%' OR p.nama_lengkap LIKE '%$search%')";
if ($f_jenis != '') $where_clauses[] = "m.jenis_mutasi = '$f_jenis'";

$where_sql = implode(' AND ', $where_clauses);
$limit = 15; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$q_total = $koneksi->query("SELECT COUNT(m.id) AS total FROM mutasi_penduduk m LEFT JOIN penduduk p ON m.penduduk_id = p.id WHERE $where_sql");
$total_data = $q_total->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

$q_mutasi = $koneksi->query("
    SELECT m.*, p.nik, p.nama_lengkap, p.foto_warga, u.nama_admin 
    FROM mutasi_penduduk m 
    LEFT JOIN penduduk p ON m.penduduk_id = p.id 
    LEFT JOIN users u ON m.admin_id = u.id 
    WHERE $where_sql 
    ORDER BY m.tanggal_mutasi DESC, m.id DESC 
    LIMIT $limit OFFSET $offset
");

$url_params = "&search=".urlencode($search)."&f_jenis=".urlencode($f_jenis);

// Ambil data warga aktif untuk dropdown form mutasi baru
$q_warga_aktif = $koneksi->query("SELECT id, nik, nama_lengkap FROM penduduk WHERE status_kependudukan = 'Aktif' ORDER BY nama_lengkap ASC");
?>

<!-- Dependencies untuk Pencarian Dropdown Cerdas (Select2) & SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .form-control { padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 13px; font-family: 'Poppins', sans-serif; width: 100%; box-sizing: border-box; }
    .form-control:focus { outline: none; border-color: #1a6f76; box-shadow: 0 0 0 3px rgba(26, 111, 118, 0.1); }
    .btn { padding: 10px 15px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.3s; color: white; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; }
    .btn-primary { background: #1a6f76; } .btn-primary:hover { background: #13555b; }
    .btn-warning { background: #f59e0b; color: #fff; } .btn-warning:hover { background: #d97706; }
    
    .data-table { width: 100%; border-collapse: collapse; background: #fff; font-size: 13px; }
    .data-table th, .data-table td { padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
    .data-table th { background: #1a6f76; color: white; white-space: nowrap; }
    .avatar-mini { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; }
    
    .badge-mutasi { padding: 5px 10px; border-radius: 50px; font-size: 11px; font-weight: 700; display: inline-block; }
    .badge-mati { background: #fee2e2; color: #ef4444; border: 1px solid #fca5a5; }
    .badge-pindah { background: #fef3c7; color: #d97706; border: 1px solid #fcd34d; }
    
    .pagination { display: flex; justify-content: flex-end; gap: 5px; margin-top: 20px; }
    .page-link { padding: 8px 12px; background: #fff; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 13px; color: #1e293b; text-decoration: none; font-weight: 600; transition: 0.2s; }
    .page-link:hover { background: #f1f5f9; }
    .page-link.active { background: #1a6f76; color: #fff; border-color: #1a6f76; }

    /* Modal Styles */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 9999; }
    .modal-box { background: #ffffff; width: 100%; max-width: 550px; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: visible; animation: slideDown 0.3s forwards; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; border-radius: 16px 16px 0 0; }
    .modal-header h3 { margin: 0; font-size: 18px; color: #334155; }
    .btn-close-modal { background: transparent; border: none; font-size: 20px; color: #94a3b8; cursor: pointer; transition: 0.2s; }
    .btn-close-modal:hover { color: #ef4444; }
    .modal-body { padding: 25px; }
    
    /* Select2 Theme Tweaks to match UI */
    .select2-container .select2-selection--single { height: 42px; border: 1px solid #cbd5e0; border-radius: 6px; padding: 5px; font-family: 'Poppins', sans-serif; font-size: 13px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
    @keyframes slideDown { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #e2e8f0; margin-bottom: 20px;">
    <h1 class="page-title" style="margin: 0;"><i class="fa-solid fa-clock-rotate-left"></i> Manajemen Mutasi Data</h1>
    <button class="btn btn-warning" onclick="bukaModalMutasi()"><i class="fa-solid fa-plus"></i> Tambah Mutasi Baru</button>
</div>

<!-- AREA TABEL RIWAYAT -->
<div class="admin-card" style="overflow-x: auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 20px;">
    
    <div style="background: #f8fafc; padding: 15px; border-radius: 12px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 20px; border: 1px solid #e2e8f0;">
        <form action="" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; flex-grow: 1;">
            <input type="text" name="search" class="form-control" placeholder="Cari NIK / Nama Warga..." value="<?= htmlspecialchars($search) ?>" style="width: 250px;">
            <select name="f_jenis" class="form-control" style="width: 180px;">
                <option value="">Semua Jenis Mutasi</option>
                <option value="Mati" <?= $f_jenis == 'Mati' ? 'selected' : '' ?>>Meninggal Dunia</option>
                <option value="Pindah" <?= $f_jenis == 'Pindah' ? 'selected' : '' ?>>Pindah Domisili</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filter Data</button>
            <a href="mutasi.php" class="btn" style="background:#cbd5e0; color:#1e293b;">Reset</a>
        </form>
    </div>

    <p style="font-size: 13px; color: #64748b; margin-bottom: 10px;">Ditemukan <b><?= $total_data ?></b> riwayat mutasi.</p>

    <table class="data-table">
        <thead>
            <tr>
                <th>Data Warga</th>
                <th>Jenis Mutasi</th>
                <th>Tgl Kejadian</th>
                <th>Keterangan</th>
                <th>Operator</th>
                <th>Lampiran</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($q_mutasi && $q_mutasi->num_rows > 0): 
                while($row = $q_mutasi->fetch_assoc()): 
                    $badge_class = ($row['jenis_mutasi'] == 'Mati') ? 'badge-mati' : 'badge-pindah';
                    $icon_class = ($row['jenis_mutasi'] == 'Mati') ? 'fa-bed' : 'fa-truck-fast';
            ?>
            <tr>
                <td style="display: flex; align-items: center; gap: 10px;">
                    <?php if($row['foto_warga']): ?>
                        <img src="../assets/uploads/warga/<?= $row['foto_warga'] ?>" class="avatar-mini">
                    <?php else: ?>
                        <div class="avatar-mini" style="background:#e2e8f0; display:flex; justify-content:center; align-items:center;"><i class="fa-solid fa-user"></i></div>
                    <?php endif; ?>
                    <div>
                        <strong style="color: #0f172a; display: block;"><?= htmlspecialchars($row['nama_lengkap'] ?? 'Data Telah Dihapus') ?></strong>
                        <span style="font-size: 11px; color: #64748b;">NIK: <?= htmlspecialchars($row['nik'] ?? '-') ?></span>
                    </div>
                </td>
                <td><span class="badge-mutasi <?= $badge_class ?>"><i class="fa-solid <?= $icon_class ?>"></i> <?= strtoupper($row['jenis_mutasi']) ?></span></td>
                <td style="font-weight: 500;"><?= date('d M Y', strtotime($row['tanggal_mutasi'])) ?></td>
                <td style="max-width: 250px; white-space: normal; line-height: 1.5; font-size: 12px;"><?= nl2br(htmlspecialchars($row['keterangan'])) ?></td>
                <td>
                    <div style="font-size: 12px;"><i class="fa-solid fa-user-shield" style="color:#94a3b8;"></i> <?= htmlspecialchars($row['nama_admin']) ?></div>
                    <div style="font-size: 11px; color:#94a3b8; margin-top:2px;"><?= date('d/m/Y', strtotime($row['created_at'])) ?></div>
                </td>
                <td>
                    <?php if (!empty($row['dokumen_pendukung'])): ?>
                        <a href="../assets/uploads/mutasi/<?= $row['dokumen_pendukung'] ?>" target="_blank" class="btn" style="background:#3b82f6; padding:6px 10px;" title="Lihat Bukti">Lihat File</a>
                    <?php else: ?>
                        <span style="font-size: 11px; color: #94a3b8; font-style: italic;">Tidak ada</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">Belum ada riwayat mutasi data.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if($total_pages > 1): ?>
    <div class="pagination">
        <?php if($page > 1): ?> <a href="?page=<?= $page-1 ?><?= $url_params ?>" class="page-link">&laquo; Prev</a> <?php endif; ?>
        <?php for($i=1; $i<=$total_pages; $i++): ?>
            <a href="?page=<?= $i ?><?= $url_params ?>" class="page-link <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if($page < $total_pages): ?> <a href="?page=<?= $page+1 ?><?= $url_params ?>" class="page-link">Next &raquo;</a> <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- ==============================================
     MODAL FORM TAMBAH MUTASI (SATU HALAMAN)
     ============================================== -->
<div class="modal-overlay" id="modalMutasi">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-right-left"></i> Form Mutasi Warga</h3>
            <button type="button" class="btn-close-modal" onclick="tutupModalMutasi()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <div class="modal-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= function_exists('generate_csrf_token') ? generate_csrf_token() : '' ?>">
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; font-size:12px; font-weight:600; color:#4b5563; margin-bottom:5px;">Cari Warga (Ketik NIK atau Nama)</label>
                    <select name="penduduk_id" id="penduduk_search" class="form-control" required style="width:100%;">
                        <option value="">-- Cari Data Warga Aktif --</option>
                        <?php 
                        if($q_warga_aktif) {
                            while($w = $q_warga_aktif->fetch_assoc()) {
                                echo "<option value='{$w['id']}'>{$w['nik']} - " . htmlspecialchars($w['nama_lengkap']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; font-size:12px; font-weight:600; color:#4b5563; margin-bottom:5px;">Jenis Mutasi Keluar</label>
                    <select name="jenis_mutasi" class="form-control" required>
                        <option value="">-- Pilih Status Baru --</option>
                        <option value="Mati">Meninggal Dunia</option>
                        <option value="Pindah">Pindah Domisili (Keluar Desa)</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; font-size:12px; font-weight:600; color:#4b5563; margin-bottom:5px;">Tanggal Kejadian / Pindah</label>
                    <input type="date" name="tanggal_mutasi" class="form-control" required>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; font-size:12px; font-weight:600; color:#4b5563; margin-bottom:5px;">Keterangan / Alasan</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Pindah ke alamat X mengikuti keluarga..." required style="resize:vertical;"></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 25px;">
                    <label style="display:block; font-size:12px; font-weight:600; color:#4b5563; margin-bottom:5px;">Unggah Dokumen (Surat Keterangan Kematian / Pindah)</label>
                    <input type="file" name="dokumen" class="form-control" accept=".pdf, .jpg, .jpeg, .png" style="background:#fff;">
                    <span style="font-size: 11px; color: #94a3b8;">Format: PDF/JPG/PNG. Maksimal 5MB. Opsional.</span>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn" style="background:#cbd5e0; color:#1e293b; flex:1; justify-content:center;" onclick="tutupModalMutasi()">Batal</button>
                    <button type="submit" name="simpan_mutasi" class="btn btn-warning" style="flex:2; justify-content:center;">
                        <i class="fa-solid fa-save"></i> Proses Mutasi Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Inisialisasi Select2 untuk pencarian dropdown saat halaman dimuat
    $(document).ready(function() {
        $('#penduduk_search').select2({
            dropdownParent: $('#modalMutasi'),
            placeholder: "Ketik NIK atau Nama Warga...",
            allowClear: true
        });
    });

    // Fungsi Buka Tutup Modal
    function bukaModalMutasi() {
        document.getElementById('modalMutasi').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function tutupModalMutasi() {
        document.getElementById('modalMutasi').style.display = 'none';
        document.body.style.overflow = '';
    }

    // Trigger SweetAlert jika ada respon PHP dari form submission
    <?= $sweetalert_script ?>
</script>

<?php require_once 'includes/admin_footer.php'; ?>