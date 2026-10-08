<?php
// admin/edit_profil.php
require_once '../config/koneksi.php';
// require_once '../functions.php'; // Pastikan fungsi upload_file_aman ada

// ==========================================
// 1. FUNGSI RENDER BAGAN (Bisa dipanggil AJAX)
// ==========================================
if (!function_exists('gambar_bagan_admin')) {
    function gambar_bagan_admin(?int $parent_id, int $kategori_id, mysqli $koneksi) {
        $sql = $parent_id === NULL ? "SELECT * FROM struktur_organisasi WHERE parent_id IS NULL AND kategori_id = $kategori_id" : "SELECT * FROM struktur_organisasi WHERE parent_id = $parent_id AND kategori_id = $kategori_id";
        $res = $koneksi->query($sql);
        if ($res->num_rows > 0) {
            echo "<ul>";
            while ($r = $res->fetch_assoc()) {
                echo "<li>";
                echo "<div class='node-card-modern'>";
                    echo "<div class='node-jabatan-modern'>" . htmlspecialchars($r['jabatan']) . "</div>";
                    echo "<div class='node-foto-modern'>";
                    if ($r['foto_pejabat']) {
                        echo "<img src='../assets/uploads/sotk/".htmlspecialchars($r['foto_pejabat'])."' alt='Foto'>";
                    } else {
                        echo "<i class='fa-solid fa-user-tie'></i>";
                    }
                    echo "</div>";
                    echo "<div class='node-nama-modern'>" . htmlspecialchars($r['nama_pejabat']) . "</div>";
                    
                    // Overlay Actions Hover
                    echo "<div class='node-actions-overlay'>";
                        echo "<button type='button' onclick=\"editNode({$r['id']}, '{$r['jabatan']}', '{$r['nama_pejabat']}', '{$r['parent_id']}')\" class='btn-node-act btn-node-edit'><i class='fa-solid fa-pen'></i> Edit Data</button>";
                        echo "<button type='button' onclick=\"hapusNode({$r['id']})\" class='btn-node-act btn-node-del'><i class='fa-solid fa-trash'></i> Hapus</button>";
                    echo "</div>";
                echo "</div>";
                gambar_bagan_admin($r['id'], $kategori_id, $koneksi); 
                echo "</li>";
            }
            echo "</ul>";
        }
    }
}

// ==========================================
// 2. BLOK PENANGANAN AJAX (TANPA RELOAD)
// ==========================================
if (isset($_POST['ajax']) || isset($_GET['ajax'])) {
    
    if (isset($_GET['action']) && $_GET['action'] == 'get_tree') {
        $kat_id = (int)$_GET['kategori_id'];
        $cek_isi = $koneksi->query("SELECT id FROM struktur_organisasi WHERE kategori_id = $kat_id LIMIT 1");
        
        if ($cek_isi->num_rows > 0) {
            echo '<div class="org-tree">';
            gambar_bagan_admin(NULL, $kat_id, $koneksi);
            echo '</div>';
        } else {
            echo '<div style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                    <div style="background: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <i class="fa-solid fa-diagram-project" style="font-size: 35px; color: #cbd5e0;"></i>
                    </div>
                    <h4 style="color: #475569; font-weight: 700; margin-bottom: 5px;">Bagan Masih Kosong</h4>
                    <p style="font-size: 14px;">Silakan tambahkan struktur pertama.</p>
                  </div>';
        }
        exit;
    }
    
    if (isset($_GET['action']) && $_GET['action'] == 'get_parents') {
        $kat_id = (int)$_GET['kategori_id'];
        $calon_atasan = $koneksi->query("SELECT id, jabatan, nama_pejabat FROM struktur_organisasi WHERE kategori_id = $kat_id");
        echo '<option value="">-- Posisi Puncak --</option>';
        while($ca = $calon_atasan->fetch_assoc()){
            echo "<option value='{$ca['id']}'>{$ca['jabatan']} - {$ca['nama_pejabat']}</option>";
        }
        exit;
    }

    header('Content-Type: application/json');

    if (isset($_POST['hapus_kategori_bagan'])) {
        $kat_id = (int)$_POST['id_bagan'];
        $q_foto = $koneksi->query("SELECT foto_pejabat FROM struktur_organisasi WHERE kategori_id = $kat_id AND foto_pejabat IS NOT NULL AND foto_pejabat != ''");
        while ($rf = $q_foto->fetch_assoc()) {
            $file_path = "../assets/uploads/sotk/" . $rf['foto_pejabat'];
            if (file_exists($file_path)) unlink($file_path);
        }
        $koneksi->query("DELETE FROM struktur_organisasi WHERE kategori_id = $kat_id");
        $koneksi->query("DELETE FROM kategori_bagan WHERE id = $kat_id");
        echo json_encode(['status' => 'success', 'msg' => 'Bagan struktural dan seluruh anggotanya telah dihapus secara permanen.']); exit;
    }

    if (isset($_POST['hapus_node'])) {
        $id_s = (int)$_POST['hapus_node'];
        $lama = $koneksi->query("SELECT foto_pejabat FROM struktur_organisasi WHERE id=$id_s")->fetch_assoc();
        if($lama['foto_pejabat'] && file_exists("../assets/uploads/sotk/".$lama['foto_pejabat'])) unlink("../assets/uploads/sotk/".$lama['foto_pejabat']);
        $koneksi->query("DELETE FROM struktur_organisasi WHERE id = $id_s");
        echo json_encode(['status' => 'success', 'msg' => 'Anggota SOTK berhasil dihapus!']); exit;
    }

    if (isset($_POST['simpan_profil'])) {
        // Data teks kaya dari CKEditor diproses secara aman
        $visi = $koneksi->real_escape_string($_POST['visi']);
        $misi = $koneksi->real_escape_string($_POST['misi']);
        $sejarah = $koneksi->real_escape_string($_POST['sejarah']);
        $tentang = $koneksi->real_escape_string($_POST['tentang_desa']);
        
        $msg_logo = "";
        if (isset($_FILES['logo_desa']) && $_FILES['logo_desa']['error'] == 0) {
            $upload = upload_file_aman("logo_desa", "../assets/img/", ["png", "jpg", "jpeg"], 2048);
            if ($upload['status']) {
                $logo_baru = $upload['nama_file'];
                $old_data = $koneksi->query("SELECT logo_desa FROM profil_desa WHERE id = 1")->fetch_assoc();
                if(!empty($old_data['logo_desa']) && file_exists("../assets/img/".$old_data['logo_desa'])){
                    unlink("../assets/img/".$old_data['logo_desa']);
                }
                $koneksi->query("UPDATE profil_desa SET logo_desa='$logo_baru' WHERE id=1");
                $msg_logo = " & Logo diperbarui.";
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Gagal unggah logo: ' . $upload['pesan']]); exit;
            }
        }
        
        $koneksi->query("UPDATE profil_desa SET visi='$visi', misi='$misi', sejarah='$sejarah', tentang_desa='$tentang' WHERE id=1");
        echo json_encode(['status' => 'success', 'msg' => 'Teks Profil berhasil disimpan!' . $msg_logo]); exit;
    }

    if (isset($_POST['tambah_kategori_bagan'])) {
        $nama_bagan = $koneksi->real_escape_string($_POST['nama_bagan_baru']);
        $koneksi->query("INSERT INTO kategori_bagan (nama_bagan) VALUES ('$nama_bagan')");
        $id_baru = $koneksi->insert_id;
        echo json_encode(['status' => 'success', 'msg' => "Kategori bagan '$nama_bagan' dibuat!", 'new_id' => $id_baru, 'new_name' => $nama_bagan]); exit;
    }

    if (isset($_POST['simpan_node'])) {
        $mode = $_POST['sotk_mode'] ?? 'tambah';
        $kategori_id = (int)$_POST['kategori_id'];
        $jabatan = $koneksi->real_escape_string($_POST['jabatan']);
        $nama = $koneksi->real_escape_string($_POST['nama_pejabat']);
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : "NULL";
        
        $foto_baru = null;
        if (isset($_FILES['foto_pejabat']) && $_FILES['foto_pejabat']['error'] == 0) {
            $upload = upload_file_aman("foto_pejabat", "../assets/uploads/sotk/", ["jpg", "jpeg", "png"], 2048);
            if ($upload['status']) {
                $foto_baru = $upload['nama_file'];
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Gagal unggah foto: ' . $upload['pesan']]); exit;
            }
        }

        if ($mode == 'tambah') {
            if ($foto_baru) {
                $koneksi->query("INSERT INTO struktur_organisasi (kategori_id, parent_id, jabatan, nama_pejabat, foto_pejabat) VALUES ($kategori_id, $parent_id, '$jabatan', '$nama', '$foto_baru')");
            } else {
                $koneksi->query("INSERT INTO struktur_organisasi (kategori_id, parent_id, jabatan, nama_pejabat) VALUES ($kategori_id, $parent_id, '$jabatan', '$nama')");
            }
            echo json_encode(['status' => 'success', 'msg' => 'Anggota bagan ditambahkan!']); exit;
        } elseif ($mode == 'edit') {
            $sotk_id = (int)$_POST['sotk_id'];
            if ($foto_baru) {
                $qf = $koneksi->query("SELECT foto_pejabat FROM struktur_organisasi WHERE id=$sotk_id")->fetch_assoc();
                if($qf['foto_pejabat'] && file_exists("../assets/uploads/sotk/".$qf['foto_pejabat'])) unlink("../assets/uploads/sotk/".$qf['foto_pejabat']);
                $koneksi->query("UPDATE struktur_organisasi SET parent_id=$parent_id, jabatan='$jabatan', nama_pejabat='$nama', foto_pejabat='$foto_baru' WHERE id=$sotk_id");
            } else {
                $koneksi->query("UPDATE struktur_organisasi SET parent_id=$parent_id, jabatan='$jabatan', nama_pejabat='$nama' WHERE id=$sotk_id");
            }
            echo json_encode(['status' => 'success', 'msg' => 'Data anggota diperbarui!']); exit;
        }
    }
}

// ==========================================
// 3. TAMPILAN HALAMAN ADMIN UTAMA
// ==========================================
require_once 'includes/admin_header.php';

$profil = $koneksi->query("SELECT * FROM profil_desa WHERE id = 1")->fetch_assoc();
$kategori_bagan = $koneksi->query("SELECT * FROM kategori_bagan ORDER BY id ASC");
$q_aktif = $koneksi->query("SELECT * FROM kategori_bagan ORDER BY id ASC LIMIT 1");
$aktif_bagan = $q_aktif->fetch_assoc();
$aktif_bagan_id = $aktif_bagan ? $aktif_bagan['id'] : 0;
$aktif_bagan_nama = $aktif_bagan ? $aktif_bagan['nama_bagan'] : "Belum ada bagan";
?>

<!-- Memuat pustaka CKEditor 5 (Rich Text Editor) -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<style>
    /* --- ROOT & ALERTS --- */
    :root { --primary: #0d9488; --primary-hover: #0f766e; --surface: #ffffff; --background: #f8fafc; --text-main: #1e293b; --border-color: #e2e8f0; }
    
    #toast-container { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
    .alert-toast { padding: 12px 25px; border-radius: 50px; font-size: 14px; font-weight: 600; color: white; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); opacity: 0; transform: translateY(-20px); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .alert-toast.show { opacity: 1; transform: translateY(0); }
    .alert-toast.success { background: #10b981; }
    .alert-toast.error { background: #ef4444; }

    /* --- UI MODERN --- */
    .admin-card-modern { background: var(--surface); border-radius: 16px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid var(--border-color); margin-bottom: 30px; }
    .card-title-modern { font-size: 18px; color: var(--text-main); font-weight: 700; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; padding-bottom: 15px; border-bottom: 2px solid var(--background); }
    .card-title-modern i { color: var(--primary); background: #ccfbf1; padding: 8px; border-radius: 8px; }

    .form-group-modern { margin-bottom: 18px; }
    .form-group-modern label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px; }
    .form-control-modern { width: 100%; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: 10px; font-size: 14px; background: var(--background); transition: all 0.3s ease; }
    .form-control-modern:focus { background: var(--surface); border-color: var(--primary); outline: none; box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.15); }
    
    .btn-modern { padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s; border: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-primary-modern { background: var(--primary); color: white; box-shadow: 0 4px 6px rgba(13, 148, 136, 0.2); }
    .btn-primary-modern:hover:not(:disabled) { background: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 6px 12px rgba(13, 148, 136, 0.3); }
    
    .btn-secondary-modern { background: #64748b; color: white; }
    .btn-secondary-modern:hover:not(:disabled) { background: #475569; }
    
    .btn-danger-modern { background: #fee2e2; color: #dc2626; padding: 12px 16px; border-radius: 10px; border: none; cursor: pointer; transition: 0.2s; }
    .btn-danger-modern:hover { background: #ef4444; color: white; }

    .grid-2-col { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
    @media (max-width: 768px) { .grid-2-col { grid-template-columns: 1fr; } }

    /* --- STYLING KHUSUS CKEDITOR AGAR MATCH DENGAN UI --- */
    .ck-editor__editable_inline { min-height: 180px; font-family: inherit; font-size: 14px; }
    .ck.ck-editor { width: 100%; }
    .ck.ck-toolbar { border-radius: 10px 10px 0 0 !important; background: var(--background) !important; border-color: var(--border-color) !important; border-bottom: none !important; }
    .ck.ck-editor__main > .ck-editor__editable { border-radius: 0 0 10px 10px !important; border-color: var(--border-color) !important; background: var(--surface) !important; transition: all 0.3s ease; }
    .ck.ck-editor__main > .ck-editor__editable.ck-focused { border-color: var(--primary) !important; box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.15) !important; }

    /* --- IMAGE PREVIEW UTILS --- */
    .img-preview-container { position: relative; width: 65px; height: 65px; border-radius: 12px; border: 2px dashed #cbd5e0; overflow: hidden; background: #f1f5f9; display: flex; align-items: center; justify-content: center; }
    .img-preview-container img { width: 100%; height: 100%; object-fit: cover; display: none; }
    .img-preview-container.has-image img { display: block; }
    .img-preview-container.has-image i { display: none; }

    /* --- MODERN TREE (SOTK) --- */
    .org-tree-container { padding: 20px; background: var(--background); border-radius: 16px; border: 1px dashed var(--border-color); margin-top: 20px; overflow-x: auto; min-height: 200px; position: relative; }
    .org-tree { display: flex; justify-content: center; padding: 20px 0; min-width: max-content; animation: fadeIn 0.5s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    .org-tree ul { padding-top: 20px; position: relative; display: flex; justify-content: center; padding-left: 0; list-style: none; }
    .org-tree li { text-align: center; list-style-type: none; position: relative; padding: 20px 10px 0 10px; }
    .org-tree li::before, .org-tree li::after { content: ''; position: absolute; top: 0; right: 50%; border-top: 2px solid #94a3b8; width: 50%; height: 20px; }
    .org-tree li::after { right: auto; left: 50%; border-left: 2px solid #94a3b8; }
    .org-tree li:only-child::after, .org-tree li:only-child::before { display: none; }
    .org-tree li:only-child { padding-top: 0; }
    .org-tree li:first-child::before, .org-tree li:last-child::after { border: 0 none; }
    .org-tree li:last-child::before { border-right: 2px solid #94a3b8; border-radius: 0; }
    .org-tree li:first-child::after { border-radius: 0; }
    .org-tree ul ul::before { content: ''; position: absolute; top: 0; left: 50%; border-left: 2px solid #94a3b8; width: 0; height: 20px; transform: translateX(-50%); }

    /* Node Card Polished */
    .node-card-modern { background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px; width: 160px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; transition: 0.3s; position: relative; }
    .node-card-modern:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-color: var(--primary); }
    .node-jabatan-modern { font-size: 11px; font-weight: 700; color: white; background: var(--primary); padding: 8px 5px; text-transform: uppercase; min-height: 40px; display: flex; align-items: center; justify-content: center; }
    .node-foto-modern { width: 70px; height: 70px; background: #e2e8f0; border-radius: 50%; margin: 15px auto 10px auto; overflow: hidden; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; }
    .node-foto-modern img { width: 100%; height: 100%; object-fit: cover; }
    .node-foto-modern i { font-size: 30px; color: #94a3b8; }
    .node-nama-modern { font-size: 12px; color: var(--text-main); font-weight: 700; padding: 0 10px 15px 10px; line-height: 1.3; }
    
    .node-actions-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.9); display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 8px; opacity: 0; transition: 0.3s; backdrop-filter: blur(2px); }
    .node-card-modern:hover .node-actions-overlay { opacity: 1; }
    .btn-node-act { padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; border: none; cursor: pointer; display: flex; align-items: center; gap: 5px; width: 80%; justify-content: center; transition: 0.2s; }
    .btn-node-edit { background: #fef3c7; color: #d97706; }
    .btn-node-edit:hover { background: #f59e0b; color: white; }
    .btn-node-del { background: #fee2e2; color: #dc2626; }
    .btn-node-del:hover { background: #ef4444; color: white; }
    
    .loader-overlay { position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(248, 250, 252, 0.8); z-index:10; display:flex; align-items:center; justify-content:center; flex-direction:column; color:var(--primary); font-weight:600; display:none; border-radius: 16px; }
</style>

<div id="toast-container"></div>

<div class="page-header">
    <h1 class="page-title">Manajemen Profil & Multi-Bagan</h1>
</div>

<div style="display: grid; grid-template-columns: 1fr; gap: 30px;">
    
    <!-- 1. FORM 4 TEKS & LOGO (DILENGKAPI CKEDITOR) -->
    <div class="admin-card-modern">
        <h3 class="card-title-modern"><i class="fa-solid fa-file-signature"></i> Manajemen Identitas Desa</h3>
        <form id="formProfil" enctype="multipart/form-data">
            <input type="hidden" name="ajax" value="1">
            <input type="hidden" name="simpan_profil" value="1">
            
            <div class="grid-2-col">
                <div>
                    <div class="form-group-modern">
                        <label>Logo Desa (Klik untuk mengubah)</label>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <label for="uploadLogo" style="cursor: pointer; margin:0;">
                                <div class="img-preview-container <?= !empty($profil['logo_desa']) ? 'has-image' : '' ?>" id="previewLogoContainer">
                                    <i class="fa-solid fa-image text-muted" style="font-size:24px;"></i>
                                    <img id="previewLogo" src="<?= !empty($profil['logo_desa']) ? '../assets/img/'.htmlspecialchars($profil['logo_desa']) : '' ?>">
                                </div>
                            </label>
                            <input type="file" id="uploadLogo" name="logo_desa" accept="image/*" style="display:none;" onchange="previewImage(this, 'previewLogo', 'previewLogoContainer')">
                            <div style="flex:1;">
                                <span style="font-size: 12px; color: #64748b; background: var(--background); padding: 8px; border-radius: 8px; display:inline-block;">
                                    Format: JPG/PNG/JPEG. Max 2MB.
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group-modern">
                        <label>Teks Visi (Kiri Atas)</label>
                        <textarea name="visi" id="editor_visi" class="form-control-modern"><?= htmlspecialchars($profil['visi'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group-modern">
                        <label>Teks Misi (Kiri Bawah)</label>
                        <textarea name="misi" id="editor_misi" class="form-control-modern"><?= htmlspecialchars($profil['misi'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div>
                    <div class="form-group-modern">
                        <label>Sejarah Desa (Kanan Atas)</label>
                        <textarea name="sejarah" id="editor_sejarah" class="form-control-modern"><?= htmlspecialchars($profil['sejarah'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group-modern">
                        <label>Tentang Desa (Paragraf Bawah)</label>
                        <textarea name="tentang_desa" id="editor_tentang" class="form-control-modern"><?= htmlspecialchars($profil['tentang_desa'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
            <div style="margin-top: 15px; text-align: right;">
                <button type="submit" class="btn-modern btn-primary-modern" id="btnSimpanProfil">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Simpan Identitas Desa
                </button>
            </div>
        </form>
    </div>

    <!-- 2. MANAJEMEN MULTI-BAGAN -->
    <div class="admin-card-modern">
        <h3 class="card-title-modern"><i class="fa-solid fa-network-wired"></i> Manajemen Struktur SOTK</h3>
        
        <div style="display: flex; flex-wrap: wrap; gap: 20px; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 25px;">
            <div style="flex: 1; min-width: 250px; display:flex; gap:10px;">
                <select id="selectKategoriBagan" class="form-control-modern" style="margin:0;" onchange="loadTreeAndParents()">
                    <?php while($kb = $kategori_bagan->fetch_assoc()): ?>
                        <option value="<?= $kb['id'] ?>" data-name="<?= htmlspecialchars($kb['nama_bagan']) ?>" <?= $aktif_bagan_id == $kb['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kb['nama_bagan']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="button" class="btn-danger-modern" onclick="hapusBaganAktif()" title="Hapus Permanen Bagan Ini">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
            
            <form id="formKategori" style="flex: 1; min-width: 250px; display:flex; gap:10px; border-left: 2px solid #cbd5e0; padding-left: 20px;">
                <input type="hidden" name="ajax" value="1">
                <input type="hidden" name="tambah_kategori_bagan" value="1">
                <input type="text" name="nama_bagan_baru" class="form-control-modern" placeholder="Ketik nama struktur baru..." required style="margin:0;">
                <button type="submit" class="btn-modern btn-primary-modern" id="btnSimpanKat"><i class="fa-solid fa-folder-plus"></i> Buat</button>
            </form>
        </div>

        <form id="formNode" enctype="multipart/form-data" style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
            <h4 style="font-size: 15px; margin-bottom: 15px; color: var(--primary);"><i class="fa-solid fa-user-plus"></i> Form Anggota Struktur</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                
                <input type="hidden" name="ajax" value="1">
                <input type="hidden" name="simpan_node" value="1">
                <input type="hidden" name="kategori_id" id="input_kategori_id" value="<?= $aktif_bagan_id ?>">
                <input type="hidden" name="sotk_mode" id="sotk_mode" value="tambah">
                <input type="hidden" name="sotk_id" id="sotk_id">
                
                <div class="form-group-modern" style="margin:0;">
                    <label>Posisi Atasan (Parent)</label>
                    <select name="parent_id" id="parent_id" class="form-control-modern" style="margin:0;"></select>
                </div>
                <div class="form-group-modern" style="margin:0;">
                    <label>Nama Jabatan</label>
                    <input type="text" name="jabatan" id="input_jabatan" class="form-control-modern" placeholder="Cth: Kepala Desa" required style="margin:0;">
                </div>
                <div class="form-group-modern" style="margin:0;">
                    <label>Nama Pejabat</label>
                    <input type="text" name="nama_pejabat" id="input_nama" class="form-control-modern" placeholder="Cth: Budi Santoso" required style="margin:0;">
                </div>
                
                <div class="form-group-modern" style="margin:0;">
                    <label>Foto Pejabat</label>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <label for="uploadNodeImg" style="cursor: pointer; margin:0;">
                            <div class="img-preview-container" id="previewNodeContainer" style="width: 45px; height: 45px; border-radius:8px;">
                                <i class="fa-solid fa-user text-muted"></i>
                                <img id="previewNode" src="">
                            </div>
                        </label>
                        <input type="file" id="uploadNodeImg" name="foto_pejabat" accept="image/*" style="display:none;" onchange="previewImage(this, 'previewNode', 'previewNodeContainer')">
                        <small style="color:#94a3b8; font-size:11px;">Ketuk ikon utk upload</small>
                    </div>
                </div>

                <div style="display:flex; gap:10px;">
                    <button type="submit" class="btn-modern btn-primary-modern" id="btnSimpanNode" style="flex:1;"><i class="fa-solid fa-plus"></i> Simpan</button>
                    <button type="button" class="btn-modern btn-secondary-modern" id="btn_cancel_edit" style="display:none; padding:12px;" onclick="batalEdit()"><i class="fa-solid fa-xmark"></i></button>
                </div>
            </div>
        </form>

        <div style="text-align: center; margin: 25px 0 10px 0;">
            <span id="titleBaganAktif" style="background: var(--primary); color: white; padding: 6px 15px; border-radius: 20px; font-size: 13px; font-weight: 600; box-shadow: 0 4px 6px rgba(13,148,136,0.2);">
                <i class="fa-solid fa-sitemap"></i> Pratinjau: <?= htmlspecialchars($aktif_bagan_nama) ?>
            </span>
        </div>

        <div class="org-tree-container" id="treeContainer">
            <div class="loader-overlay" id="treeLoader">
                <i class="fa-solid fa-circle-notch fa-spin fa-2x" style="margin-bottom:10px;"></i> Memuat Ulang Bagan...
            </div>
            <div id="treeWrapper"></div>
        </div>

    </div>
</div>

<script>
    // ==========================================
    // INISIALISASI CKEDITOR 5
    // ==========================================
    let editors = {}; // Objek untuk menyimpan instance setiap editor
    const textAreas = ['editor_visi', 'editor_misi', 'editor_sejarah', 'editor_tentang'];
    
    textAreas.forEach(id => {
        ClassicEditor
            .create(document.querySelector('#' + id), {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo' ]
            })
            .then(editor => {
                editors[id] = editor;
            })
            .catch(error => {
                console.error('Gagal memuat CKEditor:', error);
            });
    });

    // ==========================================
    // FUNGSI UTILITIES
    // ==========================================
    function showToast(type, message) {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `alert-toast ${type}`;
        toast.innerHTML = (type === 'success' ? '<i class="fa-solid fa-check-circle"></i>' : '<i class="fa-solid fa-triangle-exclamation"></i>') + ` ${message}`;
        container.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 400); }, 3500);
    }

    function previewImage(input, imgId, containerId) {
        const preview = document.getElementById(imgId);
        const container = document.getElementById(containerId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => { preview.src = e.target.result; container.classList.add('has-image'); }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function toggleButtonLoading(buttonId, isLoading, originalText = '') {
        const btn = document.getElementById(buttonId);
        if (isLoading) {
            btn.dataset.original = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
            btn.disabled = true;
        } else {
            btn.innerHTML = btn.dataset.original || originalText;
            btn.disabled = false;
        }
    }

    // ==========================================
    // LOGIKA AJAX
    // ==========================================
    async function loadTreeAndParents() {
        const selectBagan = document.getElementById('selectKategoriBagan');
        if(!selectBagan.value) return;

        const katId = selectBagan.value;
        const katName = selectBagan.options[selectBagan.selectedIndex].dataset.name;
        
        document.getElementById('input_kategori_id').value = katId;
        document.getElementById('titleBaganAktif').innerHTML = `<i class="fa-solid fa-sitemap"></i> Pratinjau: ${katName}`;
        
        const loader = document.getElementById('treeLoader');
        loader.style.display = 'flex';
        
        try {
            const treeResponse = await fetch(`?ajax=1&action=get_tree&kategori_id=${katId}`);
            document.getElementById('treeWrapper').innerHTML = await treeResponse.text();
            
            const parentResponse = await fetch(`?ajax=1&action=get_parents&kategori_id=${katId}`);
            document.getElementById('parent_id').innerHTML = await parentResponse.text();
        } catch (error) {
            showToast('error', 'Koneksi terputus saat memuat bagan.');
        } finally {
            loader.style.display = 'none';
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        if (document.getElementById('selectKategoriBagan').options.length > 0) loadTreeAndParents();
        else document.getElementById('treeWrapper').innerHTML = '<div style="text-align:center; padding:50px; color:#94a3b8;">Belum ada bagan.</div>';
    });

    // --- SUBMIT FORM PROFIL BERSAMA DATA CKEDITOR ---
    document.getElementById('formProfil').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Wajib: Sinkronisasi data WYSIWYG Editor kembali ke Textarea original sebelum AJAX jalan
        for (let id in editors) {
            editors[id].updateSourceElement();
        }

        toggleButtonLoading('btnSimpanProfil', true);
        const formData = new FormData(this);
        try {
            const res = await fetch('', { method: 'POST', body: formData });
            const data = await res.json();
            showToast(data.status, data.msg);
        } catch (err) {
            showToast('error', 'Terjadi kesalahan sistem.');
        } finally {
            toggleButtonLoading('btnSimpanProfil', false);
        }
    });

    async function hapusBaganAktif() {
        const selectBagan = document.getElementById('selectKategoriBagan');
        if (selectBagan.options.length === 0 || !selectBagan.value) return;
        const katId = selectBagan.value;
        const katName = selectBagan.options[selectBagan.selectedIndex].dataset.name;
        
        if (!confirm(`PERINGATAN!\nApakah Anda yakin ingin menghapus struktur "${katName}"?\nSemua foto & data anggota akan hilang.`)) return;
        
        const formData = new FormData();
        formData.append('ajax', '1'); formData.append('hapus_kategori_bagan', '1'); formData.append('id_bagan', katId);

        try {
            const res = await fetch('', { method: 'POST', body: formData });
            const data = await res.json();
            showToast(data.status, data.msg);
            if (data.status === 'success') {
                selectBagan.remove(selectBagan.selectedIndex);
                if (selectBagan.options.length > 0) {
                    selectBagan.selectedIndex = 0;
                    loadTreeAndParents();
                } else {
                    document.getElementById('treeWrapper').innerHTML = '<div style="text-align:center; padding:50px; color:#94a3b8;">Struktur kosong.</div>';
                    document.getElementById('parent_id').innerHTML = '<option value="">-- Posisi Puncak --</option>';
                    document.getElementById('titleBaganAktif').innerHTML = `<i class="fa-solid fa-sitemap"></i> Pratinjau: Tidak ada bagan`;
                }
            }
        } catch (err) { showToast('error', 'Gagal menghapus bagan.'); }
    }

    async function hapusNode(id) {
        if (!confirm('Hapus anggota ini beserta seluruh bawahannya?')) return;
        const formData = new FormData();
        formData.append('ajax', '1'); formData.append('hapus_node', id);
        try {
            const res = await fetch('', { method: 'POST', body: formData });
            const data = await res.json();
            showToast(data.status, data.msg);
            if (data.status === 'success') loadTreeAndParents();
        } catch (err) { showToast('error', 'Gagal menghapus anggota.'); }
    }

    document.getElementById('formKategori').addEventListener('submit', async function(e) {
        e.preventDefault();
        toggleButtonLoading('btnSimpanKat', true);
        try {
            const res = await fetch('', { method: 'POST', body: new FormData(this) });
            const data = await res.json();
            showToast(data.status, data.msg);
            if (data.status === 'success') {
                const select = document.getElementById('selectKategoriBagan');
                const newOption = new Option(data.new_name, data.new_id);
                newOption.dataset.name = data.new_name; select.add(newOption); select.value = data.new_id; 
                this.reset(); loadTreeAndParents();
            }
        } catch (err) { showToast('error', 'Gagal membuat bagan.'); } 
        finally { toggleButtonLoading('btnSimpanKat', false); }
    });

    document.getElementById('formNode').addEventListener('submit', async function(e) {
        e.preventDefault();
        toggleButtonLoading('btnSimpanNode', true);
        try {
            const res = await fetch('', { method: 'POST', body: new FormData(this) });
            const data = await res.json();
            showToast(data.status, data.msg);
            if (data.status === 'success') { batalEdit(); loadTreeAndParents(); }
        } catch (err) { showToast('error', 'Gagal menyimpan anggota.'); } 
        finally { toggleButtonLoading('btnSimpanNode', false); }
    });

    function editNode(id, jabatan, nama, parent_id) {
        document.getElementById('sotk_mode').value = 'edit';
        document.getElementById('sotk_id').value = id;
        document.getElementById('input_jabatan').value = jabatan;
        document.getElementById('input_nama').value = nama;
        document.getElementById('parent_id').value = (parent_id && parent_id !== 'null' && parent_id !== '') ? parent_id : '';
        document.getElementById('previewNodeContainer').classList.remove('has-image');
        document.getElementById('uploadNodeImg').value = "";
        
        const btnSubmit = document.getElementById('btnSimpanNode');
        btnSubmit.innerHTML = '<i class="fa-solid fa-check-double"></i> Update Data';
        btnSubmit.style.background = '#d97706'; 
        
        document.getElementById('btn_cancel_edit').style.display = 'block';
        document.getElementById('formNode').scrollIntoView({behavior: "smooth", block: "center"});
    }

    function batalEdit() {
        document.getElementById('sotk_mode').value = 'tambah';
        document.getElementById('sotk_id').value = '';
        document.getElementById('formNode').reset();
        document.getElementById('previewNodeContainer').classList.remove('has-image');
        
        const btnSubmit = document.getElementById('btnSimpanNode');
        btnSubmit.innerHTML = '<i class="fa-solid fa-plus"></i> Simpan';
        btnSubmit.style.background = 'var(--primary)';
        
        document.getElementById('btn_cancel_edit').style.display = 'none';
    }
</script>
<?php require_once 'includes/admin_footer.php'; ?>