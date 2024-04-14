<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "db_karyawan";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Hitung jumlah total entri (tanpa filter)
$sql_total = "SELECT COUNT(*) AS count FROM karyawan";
$result_total = mysqli_query($conn, $sql_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_count = $row_total['count'];

// Hitung jumlah entri setelah diterapkan filter (jika ada)
$sql_filtered = "SELECT COUNT(*) AS count FROM karyawan";
if (isset($_GET['tanggal']) && $_GET['tanggal'] != '') {
    $tanggal = date('Y-m-d', strtotime($_GET['tanggal']));
    $sql_filtered .= " WHERE (tanggal_masuk <= '$tanggal' AND (tanggal_keluar > '$tanggal' OR tanggal_keluar IS NULL))";
}
$result_filtered = mysqli_query($conn, $sql_filtered);
$row_filtered = mysqli_fetch_assoc($result_filtered);
$filtered_count = $row_filtered['count'];

// Menghitung total entri yang akan ditampilkan dalam respons saat ini
$start = isset($_GET['start']) ? $_GET['start'] : 0;
$length = isset($_GET['length']) ? $_GET['length'] : 10;
$current_count = min($start + $length, $filtered_count);

// Buat query untuk mengambil data dari database
$query = "SELECT a.*, b.nama_gelar, 
    CASE 
    WHEN a.jenis_kelamin = 'l' THEN 'Laki - Laki'  
    WHEN a.jenis_kelamin = 'p' THEN 'Perempuan'  
    ELSE ''  
    END AS jenis_kelamin_label 
    FROM karyawan a 
    INNER JOIN gelar b ON a.gelar = b.id";

// Tambahkan filter jika tanggal disediakan
if (isset($_POST['tanggal']) || $_POST['tanggal'] != '') {
    $tanggal = date('Y-m-d', strtotime($_POST['tanggal']));
    $query .= " WHERE (tanggal_masuk <= '$tanggal' AND (tanggal_keluar > '$tanggal' OR tanggal_keluar IS NULL))";
    var_dump( $_POST['tanggal']);
}

// Tambahkan paginasi ke dalam query
$query .= " LIMIT $start, $length";

// Eksekusi query dan ambil data
$sql_data = mysqli_query($conn, $query);

// Inisialisasi array untuk menyimpan data yang akan dikirimkan kembali
$data = array();

// Ambil data hasil query
while ($row = mysqli_fetch_assoc($sql_data)) {
    // Format setiap baris data sesuai dengan struktur yang diharapkan oleh DataTables
    $formatted_row = array(
        $row['nik'],
        $row['tanggal_masuk'],
        $row['nama'],
        $row['jenis_kelamin_label'],
        $row['alamat'],
        $row['kota'],
        $row['nama_gelar'],
        $row['tanggal_keluar'],
        $row['nik'] // Kolom aksi, bisa disesuaikan dengan kebutuhan
    );

    // Tambahkan data yang sudah diformat ke dalam array data
    $data[] = $formatted_row;
}

// Mengembalikan data dalam format JSON
header('Content-Type: application/json');
// var_dump($data);
echo json_encode(array(
    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    'recordsTotal' => intval($total_count),
    'recordsFiltered' => intval($filtered_count),
    'data' => $data
));
die();
?>