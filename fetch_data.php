<?php
// Establish connection to your database
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_karyawan";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$currentDate = date('d-m-Y');

// Fetch data based on selected date
$inputDate = $_GET['date'];
// Fetch data based on the input date
if ($inputDate === "") {
    // If the input date is null, fetch all data
    $query = "SELECT * FROM karyawan";
} else {
    // If the input date is not null, fetch based on the input date
    $query = "SELECT * FROM karyawan WHERE tanggal_masuk <= '$inputDate' AND (tanggal_keluar > '$inputDate' OR tanggal_keluar IS NULL)";

    // // If the input date is not null, fetch based on the input date// If the input date is not null, fetch based on the input date
    $formattedInputDate = date('Y-m-d', strtotime(str_replace('-', '/', $inputDate)));
    $query = "SELECT * FROM karyawan WHERE tanggal_masuk <= '$formattedInputDate' AND (tanggal_keluar > '$formattedInputDate' OR tanggal_keluar IS NULL)";
}
$result = $conn->query($query);

$data = array();

if ($result->num_rows > 0) {
    // // Output data of each row
    // echo "<thead>
    //         <tr>
    //             <th>NIK</th>
    //             <th>Tanggal Masuk</th>
    //             <th>Nama</th>
    //             <th>Alamat</th>
    //             <th>Kota</th>
    //             <th>Gelar</th>
    //             <th>Tanggal Keluar</th>
    //             <th>Status</th>
    //             <th><center>Action</center></th>
    //         </tr>
    //     </thead>
    //     <tbody>";
    //     while ($row = $result->fetch_assoc()) {
    //         // Determine employee status
    //         $id = $row['nik'];
    //         $gelar_id = $row['gelar'];
    //         $gelar_sql = "SELECT * FROM gelar where id='$gelar_id'";
    //         $gelar_result = $conn->query($gelar_sql);
    //         $gelar_row = $gelar_result->fetch_assoc();
    //         $gelar_nama = $gelar_row['nama'];
    //         $tanggalMasuk = date_create_from_format('Y-m-d', $row['tanggal_masuk'])->format('d-m-Y');
    //         $tanggalKeluar = ($row['tanggal_keluar'] == '0000-00-00' || $row['tanggal_keluar'] == NULL) ? '' : date('d-m-Y', strtotime($row['tanggal_keluar']));

    //         $status = (strtotime($currentDate) >= strtotime($tanggalMasuk) && (empty($tanggalKeluar) || strtotime($currentDate) <= strtotime($tanggalKeluar))) ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>';

    //         // Tambahkan tombol edit dan delete hanya untuk karyawan aktif
    //         if ($status == '<span class="badge bg-success">Aktif</span>') {
    //             $actionButtons = '
    //                 <td><center>
    //                     <a href="tambah_karyawan.php?id=' . $id . '" class="btn btn-warning btn-sm">Edit</a>
    //                     <a href="delete.php?id=' . $id . '" class="btn btn-danger btn-sm">Hapus</a>
    //                 </center></td>';
    //         } else {
    //             if (strtotime($currentDate) <= strtotime($tanggalMasuk)) {
    //                 $actionButtons = '
    //                 <td><center>
    //                     <a href="tambah_karyawan.php?id=' . $id . '" class="btn btn-warning btn-sm">Edit</a>
    //                     <a href="delete.php?id=' . $id . '" class="btn btn-danger btn-sm">Hapus</a>
    //                 </center></td>';
    //             } else {
    //                 // Jika karyawan tidak aktif, tidak menampilkan tombol edit dan delete
    //                 $actionButtons = '<td></td>';
    //             }
    //         }
    //         echo "<tr>
    //                         <td>" . $row['nik'] . "</td>
    //                         <td>" . $tanggalMasuk . "</td>
    //                         <td>" . $row['nama'] . "</td>
    //                         <td>" . $row['alamat'] . "</td>
    //                         <td>" . $row['kota'] . "</td>
    //                         <td>" . $gelar_nama . "</td>
    //                         <td>" . $tanggalKeluar . "</td>
    //                         <td>" . $status . "</td>
    //                         " . $actionButtons . "
    //                     </tr>";
    //     }
    //     echo "</tbody>";
    // } else {
    //     echo "<tr><td colspan='7'>No active employees found</td></tr>";
    // }
    while ($row = $result->fetch_assoc()) {
        // Determine employee status
        $id = $row['nik'];
        $gelar_id = $row['gelar'];
        $gelar_sql = "SELECT * FROM gelar where id='$gelar_id'";
        $gelar_result = $conn->query($gelar_sql);
        $gelar_row = $gelar_result->fetch_assoc();
        $gelar_nama = $gelar_row['nama'];
        $tanggalMasuk = date_create_from_format('Y-m-d', $row['tanggal_masuk'])->format('d-m-Y');
        $tanggalKeluar = ($row['tanggal_keluar'] == '0000-00-00' || $row['tanggal_keluar'] == NULL) ? '' : date('d-m-Y', strtotime($row['tanggal_keluar']));

        $status = (strtotime($currentDate) >= strtotime($tanggalMasuk) && (empty($tanggalKeluar) || strtotime($currentDate) <= strtotime($tanggalKeluar))) ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>';

        // Tambahkan tombol edit dan delete hanya untuk karyawan aktif
        if ($status == '<span class="badge bg-success">Aktif</span>') {
            $actionButtons = '
        <td><center>
            <a href="tambah_karyawan.php?id=' . $id . '" class="btn btn-warning btn-sm">Edit</a>
            <a href="delete.php?id=' . $id . '" class="btn btn-danger btn-sm">Hapus</a>
        </center></td>';
        } else {
            if (strtotime($currentDate) <= strtotime($tanggalMasuk)) {
                $actionButtons = '
        <td><center>
            <a href="tambah_karyawan.php?id=' . $id . '" class="btn btn-warning btn-sm">Edit</a>
            <a href="delete.php?id=' . $id . '" class="btn btn-danger btn-sm">Hapus</a>
        </center></td>';
            } else {
                // Jika karyawan tidak aktif, tidak menampilkan tombol edit dan delete
                $actionButtons = '<td></td>';
            }
        }
        $data[] = array(
            'NIK' => $row['nik'],
            'Tanggal Masuk' => $tanggalMasuk,
            'Nama' => $row['nama'],
            'Alamat' => $row['alamat'],
            'Kota' => $row['kota'],
            'Gelar' => $row['gelar'],
            'Tanggal Keluar' => $tanggalKeluar,
            'Status' => $status,
            'Action' => $actionButtons
        );
    }
}

echo json_encode(array('data' => $data));

$conn->close();



//ssp.php
<!-- <?php
        // Database connection info 
        $dbDetails = array(
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '',
            'db'   => 'db_karyawan'
        );



        // DB table to use 
        $table = <<<EOT
 (
    SELECT 
      a.*,  
      b.nama_gelar,
      CASE
        WHEN a.jenis_kelamin = "l" THEN 'Laki - Laki'
        WHEN a.jenis_kelamin = "p" THEN 'Perempuan'
        ELSE ''
        END AS jenis_kelamin_label
    FROM karyawan a
    INNER JOIN gelar b ON a.gelar = b.id
 ) temp
EOT;

        // Table's primary key 
        $primaryKey = 'nik';

        // $joinQuery = "LEFT JOIN gelar ON karyawan.gelar = gelar.id";

        // Array of database columns which should be read and sent back to DataTables. 
        // The `db` parameter represents the column name in the database.  
        // The `dt` parameter represents the DataTables column identifier. 
        $columns = array(
            array('db' => 'nik', 'dt' => 0),
            array('db' => 'tanggal_masuk',  'dt' => 1),
            array('db' => 'nama',      'dt' => 2),
            array('db' => 'jenis_kelamin_label',      'dt' => 3),
            array('db' => 'alamat',     'dt' => 4),
            array('db' => 'kota',    'dt' => 5),
            array('db' => 'nama_gelar',    'dt' => 6),
            array('db' => 'tanggal_keluar',    'dt' => 7),
            array('db' => 'nik',    'dt' => 9),
        );

        // Include SQL query processing class 
        require 'ssp.php';

        $dateFilter = '';
        if (isset($_GET['tanggal']) && $_GET['tanggal'] != '') {
            var_dump($_GET['tanggal']);
            // Convert format from dd-mm-yyyy to yyyy-mm-dd
            $tanggal = date('Y-m-d', strtotime($_GET['tanggal']));
            $dateFilter = "(tanggal_masuk <= '$tanggal' AND (tanggal_keluar > '$tanggal' OR tanggal_keluar IS NULL))";
        }

        // Add custom filter to the database query
        $where = $dateFilter;

        // Output data as json format 
        echo json_encode(
            SSP::simple($_GET, $dbDetails, $table, $primaryKey, $columns, $where)
        );
        ?> -->

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
$query="";
// Buat query untuk mengambil data dari database
$query = "SELECT a.*, b.nama_gelar, CASE WHEN a.jenis_kelamin = 'l' THEN 'Laki - Laki'  WHEN a.jenis_kelamin = 'p' THEN 'Perempuan'  ELSE ''  END AS jenis_kelamin_label FROM karyawan a INNER JOIN gelar b ON a.gelar = b.id";

if (isset($_GET['tanggal']) && $_GET['tanggal'] != '') {
    var_dump($_GET['tanggal']);
    // Convert format from dd-mm-yyyy to yyyy-mm-dd
    $tanggal = date('Y-m-d', strtotime($_GET['tanggal']));
    $query = "SELECT a.*, b.nama_gelar, 
    CASE 
    WHEN a.jenis_kelamin = 'l' THEN 'Laki - Laki'  
    WHEN a.jenis_kelamin = 'p' THEN 'Perempuan'  
    ELSE ''  
    END AS jenis_kelamin_label 
    FROM karyawan a 
    INNER JOIN gelar b ON a.gelar = b.id 
    WHERE (tanggal_masuk <= '$tanggal' AND (tanggal_keluar > '$tanggal' OR tanggal_keluar IS NULL))";
}

$sql_data = mysqli_query($conn, $query); // Query untuk data yang akan di tampilkan

// $sql_count = mysqli_num_rows($query);
$data = mysqli_fetch_all($sql_data, MYSQLI_ASSOC); // Untuk mengambil data hasil query menjadi array
$dataTable = DataTable::of($data);
return $dataTable->make(true);
$callback = array(
    // 'draw'=>$_GET['draw'], // Ini dari datatablenya
    // 'recordsTotal'=>$sql_count,
    // 'recordsFiltered'=>$sql_filter_count,
    'data'=>$data
);
header('Content-Type: application/json');
echo json_encode($callback); // Convert array $callback ke json


// Gunakan DataTables untuk memformat data dan mengembalikannya sebagai respons JSON

?>

// Berhasil dengan JSON file
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

// Buat query untuk mengambil data dari database
$query = "SELECT a.*, b.nama_gelar, 
    CASE 
    WHEN a.jenis_kelamin = 'l' THEN 'Laki - Laki'  
    WHEN a.jenis_kelamin = 'p' THEN 'Perempuan'  
    ELSE ''  
    END AS jenis_kelamin_label 
    FROM karyawan a 
    INNER JOIN gelar b ON a.gelar = b.id";

echo var_dump($_GET['tanggal']);
// Tambahkan filter jika tanggal disediakan
if (isset($_GET['tanggal']) && $_GET['tanggal'] != '') {
    $tanggal = date('Y-m-d', strtotime($_GET['tanggal']));
    $query .= " WHERE (tanggal_masuk <= '$tanggal' AND (tanggal_keluar > '$tanggal' OR tanggal_keluar IS NULL))";
}

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

// Mengembalikan data sebagai respons JSON
header('Content-Type: application/json');
echo json_encode(array('data' => $data));
?>