<?php
// admin/proses_mutasi.php
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifikasi CSRF Token (menggunakan fungsi bawaan dari functions.php Anda)
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die(json_encode(['status' => 'error', 'pesan' => 'Token keamanan tidak valid!']));
    }

    $penduduk_id   = (int)$_POST['penduduk_id'];
    $jenis_mutasi  = $_POST['jenis_mutasi'];
    $tgl_mutasi    = $_POST['tanggal_mutasi'];
    $keterangan    = $koneksi->real_escape_string($_POST['keterangan']);
    $admin_id      = $_SESSION['admin_id'];
    $dokumen       = null;

    // Logika Upload File Aman (Menggunakan fungsi upload_file_aman dari functions.php Anda)
    if (!empty($_FILES['dokumen']['name'])) {
        $upload = upload_file_aman('dokumen', '../assets/uploads/mutasi/', ['pdf', 'jpg', 'jpeg', 'png']);
        if ($upload['status']) {
            $dokumen = $upload['nama_file'];
        } else {
            die(json_encode(['status' => 'error', 'pesan' => $upload['pesan']]));
        }
    }

    // MULAI TRANSAKSI DATABASE (Penting untuk Mutasi Data!)
    $koneksi->begin_transaction();

    try {
        // 1. Insert ke tabel mutasi
        $stmt_mutasi = $koneksi->prepare("INSERT INTO mutasi_penduduk (penduduk_id, jenis_mutasi, tanggal_mutasi, keterangan, dokumen_pendukung, admin_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_mutasi->bind_param("issssi", $penduduk_id, $jenis_mutasi, $tgl_mutasi, $keterangan, $dokumen, $admin_id);
        $stmt_mutasi->execute();
        $stmt_mutasi->close();

        // 2. Update status di tabel penduduk (Jika mutasi berupa Mati atau Pindah)
        if (in_array($jenis_mutasi, ['Mati', 'Pindah'])) {
            $status_baru = ($jenis_mutasi == 'Mati') ? 'Meninggal' : 'Pindah';
            $stmt_update = $koneksi->prepare("UPDATE penduduk SET status_kependudukan = ? WHERE id = ?");
            $stmt_update->bind_param("si", $status_baru, $penduduk_id);
            $stmt_update->execute();
            $stmt_update->close();
        }

        // 3. Catat ke Log Aktivitas (Fungsi dari functions.php)
        catat_log($koneksi, $admin_id, "Mutasi Data", "Melakukan mutasi $jenis_mutasi untuk ID Warga: $penduduk_id");

        // 4. Bersihkan Cache Statistik Warga (Agar infografis di halaman publik langsung update)
        bersihkan_cache_statistik();

        // COMMIT TRANSAKSI JIKA SEMUA BERHASIL
        $koneksi->commit();
        echo json_encode(['status' => 'success', 'pesan' => 'Data mutasi berhasil disimpan. Infografis telah diperbarui.']);

    } catch (Exception $e) {
        // ROLLBACK JIKA TERJADI ERROR (Data tidak akan rusak/setengah tersimpan)
        $koneksi->rollback();
        echo json_encode(['status' => 'error', 'pesan' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
    }
}
?>