<?php
// admin/admin_potensi.php
require_once '../config/koneksi.php';
// Pastikan path fungsi upload sesuai dengan struktur Anda
// require_once '../functions.php'; 
require_once 'includes/admin_header.php';

$pesan = "";

// --- LOGIKA MANAJEMEN POTENSI DESA ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan_potensi'])) {$judul = $koneksi->real_escape_string($_POST['judul_potensi']);
    $deskripsi =$koneksi->real_escape_string($_POST['deskripsi_potensi']);$gambar_baru = null;
    if (isset($_FILES['gambar_potensi']) && $_FILES['gambar_potensi']['error'] == 0) {
        if (function_exists('upload_file_aman')) {
            // Mengizinkan mp4 dan webm, batas maksimal 50MB (51200 KB)
            $upload = upload_file_aman("gambar_potensi", "../assets/uploads/potensi/", ["jpg", "jpeg", "png", "mp4", "webm"], 51200);

            if ($upload['status']) {
                $gambar_baru = $upload['nama_file'];
            } else {
                $pesan .= "<div class='alert-error'><i class='fa-solid fa-circle-exclamation'></i> Gagal unggah media: " . $upload['pesan'] . "</div>";
            }
        } else {
            $pesan .= "<div class='alert-error'><i class='fa-solid fa-circle-exclamation'></i> Fungsi upload media tidak tersedia.</div>";
        }
    }

    if (empty($pesan) || strpos($pesan, 'alert-success') !== false) {
        if (!empty($_POST['id_potensi'])) {
            // Mode Edit
            $id_p = (int)$_POST['id_potensi'];
            if ($gambar_baru) {
                $lama =$koneksi->query("SELECT gambar FROM potensi_desa WHERE id=$id_p")->fetch_assoc();
                if($lama['gambar'] && file_exists("../assets/uploads/potensi/".$lama['gambar'])) {
                    unlink("../assets/uploads/potensi/".$lama['gambar']);
                }
                $koneksi->query("UPDATE potensi_desa SET judul='$judul', deskripsi='$deskripsi', gambar='$gambar_baru' WHERE id=$id_p");
            } else {
                $koneksi->query("UPDATE potensi_desa SET judul='$judul', deskripsi='$deskripsi' WHERE id=$id_p");
            }
            $pesan .= "<div class='alert-success'><i class='fa-solid fa-check-circle'></i> Potensi Desa berhasil diperbarui!</div>";
        } else {
            // Mode Tambah
            if($gambar_baru) {$koneksi->query("INSERT INTO potensi_desa (judul, deskripsi, gambar) VALUES ('$judul', '$deskripsi', '$gambar_baru')");
                $pesan .= "<div class='alert-success'><i class='fa-solid fa-check-circle'></i> Potensi Desa berhasil ditambahkan!</div>";
            } else {
                $pesan .= "<div class='alert-error'><i class='fa-solid fa-circle-exclamation'></i> Media wajib diunggah untuk potensi baru!</div>";
            }
        }
    }
}

// Mode Hapus
if (isset($_GET['hapus_potensi'])) {
    $id_p = (int)$_GET['hapus_potensi'];
    $lama =$koneksi->query("SELECT gambar FROM potensi_desa WHERE id=$id_p")->fetch_assoc();
    if($lama['gambar'] && file_exists("../assets/uploads/potensi/".$lama['gambar'])) {
        unlink("../assets/uploads/potensi/".$lama['gambar']);
    }
    $koneksi->query("DELETE FROM potensi_desa WHERE id = $id_p");
    echo "<script>window.location.href='admin_potensi.php';</script>";
    exit;
}
?>

<style>
    /* --- DESAIN UI POTENSI MODERN --- */
    :root {
        --sidera-primary: #0d9488; 
        --sidera-hover: #0f766e;
        --text-main: #1e293b;
        --text-muted: #64748b;
    }

    /* Header Section (Judul & Tombol Tambah) */
    .admin-card-header { background: #ffffff; padding: 20px 25px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-left: 5px solid var(--sidera-primary); border-top: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
    .admin-card-header h3 { margin: 0; font-size: 18px; color: var(--text-main); font-weight: 700; }
    .btn-tambah { background: var(--sidera-primary); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.3s ease; display: inline-flex; align-items: center; gap: 8px; }
    .btn-tambah:hover { background: var(--sidera-hover); transform: translateY(-2px); box-shadow: 0 4px 6px rgba(13, 148, 136, 0.25); }

    /* Grid & Card Potensi */
    .potensi-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
    .potensi-card { background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; transition: all 0.3s ease; display: flex; flex-direction: column; }
    .potensi-card:hover { transform: translateY(-5px); box-shadow: 0 12px 20px -3px rgba(0,0,0,0.1); border-color: #cbd5e0; }
    .potensi-media { position: relative; width: 100%; height: 200px; background: #f8fafc; }
    .potensi-media img, .potensi-media video { width: 100%; height: 100%; object-fit: cover; }
    .badge-media { position: absolute; top: 12px; right: 12px; background: rgba(15, 23, 42, 0.7); color: #ffffff; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; backdrop-filter: blur(4px); z-index: 10; }
    .potensi-content { padding: 20px; display: flex; flex-direction: column; flex-grow: 1; }
    .potensi-title { font-size: 16px; font-weight: 700; color: var(--text-main); margin: 0 0 10px 0; line-height: 1.4; text-transform: capitalize; }
    .potensi-desc { font-size: 13px; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    
    /* Tombol Aksi Bawah */
    .potensi-actions { display: flex; gap: 10px; margin-top: auto; border-top: 1px solid #f1f5f9; padding-top: 15px; }
    .btn-action { flex: 1; padding: 10px; border-radius: 8px; font-size: 12px; font-weight: 600; text-align: center; cursor: pointer; border: none; transition: 0.2s; text-decoration: none; display: inline-flex; justify-content: center; align-items: center; gap: 6px; }
    .btn-edit { background: #fef3c7; color: #d97706; }
    .btn-edit:hover { background: #fde68a; color: #b45309; }
    .btn-delete { background: #fee2e2; color: #dc2626; }
    .btn-delete:hover { background: #fecaca; color: #b91c1c; }

    /* Responsif Mobile */
    @media (max-width: 768px) { .admin-card-header { flex-direction: column; gap: 15px; align-items: flex-start; } .btn-tambah { width: 100%; justify-content: center; } }

    /* --- CSS GLOBAL & ALERT --- */
    .alert-success { background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #22c55e; font-size: 14px; font-weight: 500; }
    .alert-error { background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ef4444; font-size: 14px; font-weight: 500; }
    .form-control { width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 8px; margin-bottom: 15px; font-family: sans-serif; font-size:13px; transition: 0.3s; }
    .form-control:focus { border-color: var(--sidera-primary); outline: none; box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.2); }
    
    /* --- CSS POPUP MODAL --- */
    .modal-overlay { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(5px); align-items: center; justify-content: center; }
    .modal-container { background-color: #ffffff; width: 90%; max-width: 550px; border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: modalFadeIn 0.3s ease-out forwards; overflow: hidden; }
    @keyframes modalFadeIn { from { opacity: 0; transform: translateY(-30px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .modal-header { padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
    .modal-title { margin: 0; font-size: 17px; color: var(--sidera-primary); font-weight: 700; }
    .close-btn { background: none; border: none; font-size: 22px; cursor: pointer; color: #64748b; transition: 0.2s; display: flex; align-items: center; justify-content: center; height: 30px; width: 30px; border-radius: 50%; }
    .close-btn:hover { background: #fee2e2; color: #ef4444; }
    .modal-body { padding: 25px 20px; }
    .modal-footer { padding: 15px 20px; border-top: 1px solid #e2e8f0; text-align: right; background: #f8fafc; display: flex; justify-content: flex-end; gap: 10px; }
</style>

<div class="page-header">
    <h1 class="page-title">Manajemen Potensi Desa</h1>
</div>
<?= $pesan ?>

<div class="admin-card" style="background: transparent; box-shadow: none; padding: 0;">
    
    <!-- Header Daftar Potensi -->
    <div class="admin-card-header">
        <h3><i class="fa-solid fa-mountain-sun"></i> Daftar Potensi Saat Ini</h3>
        <button type="button" class="btn-tambah" onclick="tambahPotensi()">
            <i class="fa-solid fa-plus"></i> Tambah Potensi Baru
        </button>
    </div>

    <!-- STRUKTUR POPUP MODAL (Formulir yang sebelumnya hilang) -->
    <div id="modalPotensi" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h4 id="modalTitle" class="modal-title">Tambah Potensi Desa</h4>
                <button type="button" class="close-btn" onclick="tutupModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <form action="" method="POST" enctype="multipart/form-data" id="formPotensi">
                <div class="modal-body">
                    <input type="hidden" name="id_potensi" id="id_potensi">
                    
                    <div class="mb-3">
                        <label style="font-size:13px; font-weight:600; color:#1e293b; display:block; margin-bottom:8px;">Judul Potensi</label>
                        <input type="text" name="judul_potensi" id="judul_potensi" class="form-control" placeholder="Contoh: Air Terjun Bidadari..." required style="margin-bottom: 0;">
                    </div>
                    
                    <div class="mb-3" style="margin-top: 20px;">
                        <label style="font-size:13px; font-weight:600; color:#1e293b; display:block; margin-bottom:8px;">Media Potensi (Foto/Video MP4, Max 50MB)</label>
                        <input type="file" name="gambar_potensi" id="gambar_potensi" class="form-control" accept="image/*,video/mp4,video/webm" style="background:#fff; margin-bottom: 0;">
                        <div id="info_edit_gambar" style="display:none; margin-top:8px; background:#fffbeb; padding:8px 12px; border-radius:6px; border-left:3px solid #f59e0b;">
                            <small style="color:#d97706; font-size:11px; font-weight:600;">
                                <i class="fa-solid fa-info-circle"></i> Biarkan kosong jika tidak ingin mengubah media yang sudah ada.
                            </small>
                        </div>
                    </div>
                    
                    <div class="mb-3" style="margin-top: 20px;">
                        <label style="font-size:13px; font-weight:600; color:#1e293b; display:block; margin-bottom:8px;">Deskripsi Singkat</label>
                        <textarea name="deskripsi_potensi" id="deskripsi_potensi" class="form-control" rows="5" placeholder="Tuliskan daya tarik dan informasi singkat mengenai potensi ini..." required style="margin-bottom: 0; resize: vertical;"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-action" style="background: #64748b; color: white;" onclick="tutupModal()">Batal</button>
                    <button type="submit" name="simpan_potensi" class="btn-tambah"><i class="fa-solid fa-save"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- GRID DAFTAR CARD POTENSI -->
    <div class="potensi-grid">
        <?php 
        $q_pot =$koneksi->query("SELECT * FROM potensi_desa ORDER BY id DESC");
        
        if ($q_pot->num_rows > 0):
            while($pot =$q_pot->fetch_assoc()): 
                $ext = strtolower(pathinfo($pot['gambar'], PATHINFO_EXTENSION));
                $is_video = in_array($ext, ['mp4', 'webm']);
        ?>
        <div class="potensi-card">
            <!-- Bagian Atas: Media Gambar/Video -->
            <div class="potensi-media">
                <span class="badge-media">
                    <?= $is_video ? '<i class="fa-solid fa-video"></i> Video' : '<i class="fa-solid fa-image"></i> Gambar' ?>
                </span>
                
                <?php if ($is_video): ?>
                    <video src="../assets/uploads/potensi/<?= htmlspecialchars($pot['gambar']) ?>" muted autoplay loop playsinline></video>
                <?php else: ?>
                    <img src="../assets/uploads/potensi/<?= htmlspecialchars($pot['gambar']) ?>" alt="<?= htmlspecialchars($pot['judul']) ?>">
                <?php endif; ?>
            </div>
            
            <!-- Bagian Bawah: Teks & Aksi -->
            <div class="potensi-content">
                <h4 class="potensi-title"><?= htmlspecialchars($pot['judul']) ?></h4>
                <p class="potensi-desc"><?= htmlspecialchars($pot['deskripsi']) ?></p>
                
                <div class="potensi-actions">
                    <button type="button" class="btn-action btn-edit" onclick="editPotensi(<?= $pot['id'] ?>, '<?= htmlspecialchars(addslashes($pot['judul'])) ?>', `<?= htmlspecialchars(addslashes($pot['deskripsi'])) ?>`)">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </button>
                    <a href="?hapus_potensi=<?= $pot['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Hapus permanen potensi <?= htmlspecialchars(addslashes($pot['judul'])) ?>?');">
                        <i class="fa-solid fa-trash-can"></i> Hapus
                    </a>
                </div>
            </div>
        </div>
        <?php 
            endwhile; 
        else: 
        ?>
        <!-- Tampilan Saat Data Kosong -->
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; border: 2px dashed #cbd5e0;">
            <div style="background: #f1f5f9; height: 80px; width: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                <i class="fa-solid fa-folder-open" style="font-size: 35px; color: #94a3b8;"></i>
            </div>
            <h4 style="color: #334155; margin: 0 0 8px 0; font-weight: 700; font-size: 18px;">Belum Ada Data Potensi</h4>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Klik tombol "Tambah Potensi Baru" di atas untuk memasukkan data perdana.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
    
<script>
    const modal = document.getElementById('modalPotensi');
    const form = document.getElementById('formPotensi');
    const infoGambar = document.getElementById('info_edit_gambar');
    const inputGambar = document.getElementById('gambar_potensi');

    function bukaModal() {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; 
    }

    function tutupModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; 
    }

    function tambahPotensi() {
        form.reset(); 
        document.getElementById('id_potensi').value = '';
        document.getElementById('modalTitle').innerText = 'Tambah Potensi Baru';
        
        inputGambar.required = true;
        infoGambar.style.display = 'none'; 
        
        bukaModal();
    }

    function editPotensi(id, judul, deskripsi) {
        document.getElementById('id_potensi').value = id;
        document.getElementById('judul_potensi').value = judul;
        document.getElementById('deskripsi_potensi').value = deskripsi;
        
        document.getElementById('modalTitle').innerText = 'Edit Potensi Desa';
        
        inputGambar.required = false; 
        infoGambar.style.display = 'block'; 
        
        bukaModal();
    }

    // Klik di area gelap untuk menutup popup
    window.onclick = function(event) {
        if (event.target == modal) {
            tutupModal();
        }
    }
</script>

<?php require_once 'includes/admin_footer.php'; ?>