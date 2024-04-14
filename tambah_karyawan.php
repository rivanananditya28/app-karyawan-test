<?php
// tambah_karyawan.php

// Lakukan koneksi ke database atau ambil data dari mana pun yang diperlukan
// Misalnya, kita akan mengasumsikan koneksi ke database telah dibuat sebelumnya

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "db_karyawan";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function validateNik($nik)
{
    $str = trim($nik);
    // Validasi menggunakan regular expression
    if (preg_match("/^[a-zA-Z0-9]{12}$/", $nik)) {
        return true;
    } else {
        return false;
    }
}

function validateNama($str)
{
    $str = trim($str);
    // Validasi menggunakan regular expression
    if (preg_match("/^[a-zA-Z]+(?:\s*[.,]?\s*[a-zA-Z]+)*$/", $str)) {
        return true;
    } else {
        return false;
    }
}

function validateKota($str)
{
    $str = trim($str);
    // Validasi karakter pertama adalah huruf
    if (ctype_alpha(mb_substr($str, 0, 1))) {
        // Validasi menggunakan regular expression untuk memastikan hanya huruf dan spasi
        if (preg_match("/^[a-zA-Z\s]+$/", $str)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function validateAlamat($str)
{
    $str = trim($str);
    // Validate minimum and maximum length
    if (strlen($str) < 5 || strlen($str) > 255) {
        return false;
    }

    // Check if the address contains "jalan" or "jl" (case insensitive)
    if (stripos($str, 'jalan') === false && stripos($str, 'jl') === false) {
        return false;
    }

    // $substr =  "'";
    // return str_replace($substr, $substr . $substr, $str);
    return addslashes($str);
}

// Inisialisasi variabel default untuk form
$nik = "";
$tanggal_masuk = "";
$nama = "";
$alamat = "";
$kota = "";
$gelar = "";
$jenis_kelamin = "";
$errors = array();
$action = "insert"; // Mode default: insert

// Periksa apakah ini adalah mode edit atau bukan
if (isset($_GET['id'])) {
    // Mode edit, ambil data karyawan dari database berdasarkan NIK yang diberikan
    $nik = $_GET['id'];
    $query = "SELECT * FROM karyawan WHERE nik = '$nik'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $nik = $row['nik'];
        $tanggal_masuk = $row['tanggal_masuk'];
        $nama = $row['nama'];
        $alamat = $row['alamat'];
        $kota = $row['kota'];
        $gelar = $row['gelar'];
        $jenis_kelamin = $row['jenis_kelamin'];
        $action = "update"; // Mengubah mode ke update
    }
}

// Proses form jika ada data yang dikirimkan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nik = $_POST["nik"];
    $tanggal_masuk = date('Y-m-d', strtotime($_POST["tanggal_masuk"])); // Ubah format tanggal
    $nama = $_POST["nama"];
    $alamat = $_POST["alamat"];
    $kota = $_POST["kota"];
    $gelar = isset($_POST['gelar']) ? $_POST['gelar'] : '';
    $jenis_kelamin = isset($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : '';
    // echo $tanggal_masuk;

    if ($action == "update") {
        $tanggal_keluar = date('Y-m-d', strtotime($_POST["tanggal_keluar"]));
    }

    // Lakukan validasi input kosong
    if (empty($nik) || empty($tanggal_masuk) || empty($nama) || empty($alamat) || empty($kota) || empty($gelar) || empty($jenis_kelamin)) {
        // var_dump(empty($nik),empty($tgl_masuk),empty($nama),empty($alamat),empty($kota),empty($gelar));
        $errors[] = "Semua kolom harus diisi.";
    }

    // Lakukan validasi NIK
    if (!validateNik($nik)) {
        $errors[] = "NIK harus terdiri dari 12 digit dan hanya berisi huruf dan angka.";
    }

    // Lakukan validasi Nama
    if (!validateNama($nama)) {
        $errors[] = "Nama tidak boleh mengandung angka";
    }

    // Lakukan validasi Kota
    if (!validateKota($kota)) {
        $errors[] = "Kota hanya boleh mengandung huruf dan spasi";
    }


    // $alamat = validateAlamat($alamat);
    if (validateAlamat($alamat) == false) {
        $errors[] = "Alamat harus mengandung kata : “jalan” atau ‘jl’. Minimal 5 karakter";
    } else {
        $alamat = validateAlamat($alamat);
    }

    // Jika terdapat error, tampilkan pesan error
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p class='error'>$error</p>";
        }
    } else {
        // Lakukan validasi dan proses sesuai dengan mode tambah atau edit
        if ($action == "insert") {
            // Mode tambah, lakukan penyisipan data baru ke database
            $query = "INSERT INTO karyawan (nik, tanggal_masuk, nama, alamat, kota, gelar, jenis_kelamin) VALUES ('$nik', '$tanggal_masuk', '$nama', '$alamat', '$kota', '$gelar', '$jenis_kelamin')";
            // Eksekusi query
            if ($conn->query($query) === TRUE) {

                // $result = mysqli_query($conn, $query);

                // Redirect ke halaman lain setelah proses selesai
                // echo '<div class="alert alert-success alert-dismissible fade in" role="alert"></div><script> window.location.href = "index.php"; </script>';
                session_start();
                // ketika di framework sudah ada fitur flash data message (session)
                // alert hanya muncul sekali ketika di refresh halaman alertnya hilang, tetapi kalau di php native belum ada fitur tersebut (buat sendiri)
                $_SESSION['message'] = '<div class="alert alert-primary" role="alert">Berhasil disimpan!</div>';
                header("Location: index.php");
                exit();
            } else {
                if (strpos($conn->error, "Duplicate entry") !== false) {
                    echo "Terjadi duplikasi data atau data sudah ada";
                } else {
                    echo "Terjadi kesalahan saat memproses data : " . $conn->error;
                }
            }
        } elseif ($action == "update") {
            if (!empty($_POST["tanggal_keluar"])) {
                // Mode edit, lakukan pembaruan data karyawan yang sudah ada di database
                $query = "UPDATE karyawan SET nik='$nik', tanggal_masuk='$tanggal_masuk', nama='$nama', alamat='$alamat', kota='$kota', gelar='$gelar', tanggal_keluar='$tanggal_keluar', jenis_kelamin='$jenis_kelamin' WHERE nik='$nik'";
                // Eksekusi query
                if ($conn->query($query) === TRUE) {

                    // $result = mysqli_query($conn, $query);

                    // Redirect ke halaman lain setelah proses selesai
                    echo '<script> window.location.href = "index.php"; showSuccessMessage();</script>';
                    // header("Location: index.php");
                    exit();
                } else {
                    echo "error: " . $conn->error;
                }
            } else {
                $query = "UPDATE karyawan SET nik='$nik', tanggal_masuk='$tanggal_masuk', nama='$nama', alamat='$alamat', kota='$kota', gelar='$gelar' WHERE nik='$nik'";
                if ($conn->query($query) === TRUE) {

                    // $result = mysqli_query($conn, $query);

                    // Redirect ke halaman lain setelah proses selesai
                    echo '<script> window.location.href = "index.php"; showSuccessMessage();</script>';
                    // header("Location: index.php");
                    exit();
                } else {
                    echo "error: " . $conn->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $action == "insert" ? 'Tambah Karyawan' : 'Edit Karyawan'; ?></title>
    <link rel="stylesheet" href="datepicker/css/datepicker.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="card mt-5">
            <div class="card-title ml-2">
                <h4>Form Data Karyawan</h4>
            </div>
            <div class="card-body">
                <form method="post" onsubmit="return validateForm();">
                    <div class="row mt-2">
                        <div class="col-md-6 col-12">
                            <label for="nik">NIK:</label>
                            <input class="form-control" type="text" id="nik" name="nik" value="<?php echo $nik; ?>" required>
                        </div>
                        <div class="col-md-6 col-12">
                            <label for="tanggal_masuk">Tanggal Masuk:</label>
                            <input class="form-control datepicker" type="text" id="tanggal_masuk" name="tanggal_masuk" value="<?php if ($tanggal_masuk != NULL) echo date('d-m-Y', strtotime($tanggal_masuk)); ?>" readonly required>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6 col-12">
                                <label for="nama">Nama:</label>
                                <input class="form-control" type="text" id="nama" name="nama" value="<?php echo $nama; ?>" required>
                            </div>
                            <!-- Tambahkan kolom untuk jenis kelamin -->
                            <div class="col-md-6 col-12">
                                <label for="jenis_kelamin">Jenis Kelamin:</label>
                                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option selected disabled>--Pilih Salah Satu--</option>
                                    <option value="l" <?php echo ($jenis_kelamin == 'l') ? 'selected' : ''; ?>>Laki-Laki</option>
                                    <option value="p" <?php echo ($jenis_kelamin == 'p') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6 col-12">
                                <label for="alamat">Alamat:</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="2" required><?php echo $alamat; ?></textarea>
                            </div>
                            <div class="col-md-6 mt-1 col-12">
                                <label for="kota">Kota:</label>
                                <input class="form-control" type="text" id="kota" name="kota" value="<?php echo $kota; ?>" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6 col-12">
                                <label for="gelar">Gelar:</label>
                                <select class="form-select" name="gelar" required>
                                    <option selected disabled>--Pilih Satu--</option>
                                    <?php $sql = "SELECT * FROM gelar";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row["id"] . '">' . $row['nama_gelar'] . '</option>';
                                    }
                                    ?>
                                    <!-- <option value="S1" <?php echo ($gelar == 'S1') ? 'selected' : ''; ?>>S1</option>
                                    <option value="S2" <?php echo ($gelar == 'S2') ? 'selected' : ''; ?>>S2</option>
                                    <option value="S3" <?php echo ($gelar == 'S3') ? 'selected' : ''; ?>>S3</option> -->
                                </select>
                            </div>
                            <!-- </div> -->
                            <!-- UpdateOrCreated -->
                            <?php if ($action == 'update') : ?>
                                <!-- <div class="row mt-2"> -->
                                <div class="col-md-6 col-12">
                                    <label for="tanggal_keluar">Tanggal Keluar:</label>
                                    <input class="form-control datepicker" type="text" id="tanggal_keluar" name="tanggal_keluar" readonly>
                                </div>
                        </div>
                    <?php endif; ?>
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <!-- End Of UpdateOrCreated -->
                    <div class="row mt-3">
                        <div class="col-10"></div>
                        <div class="col-2">
                            <input type="submit" class="btn btn-primary mt-3" value="Submit" name="submit">
                            <a class="btn btn-secondary mt-3" href="index.php" role="button">Kembali</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="datepicker/js/bootstrap-datepicker.js"></script>
    <script>
        $(function() {
            $(".datepicker").datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true,
            });

        });
    </script>
    <script>
        function validateForm() {
            var action = document.getElementById('action').value;
            if (action == 'update') {
                var tanggal_keluar = document.getElementById('tanggal_keluar').value;
                if (tanggal_keluar == '') {
                    alert('tanggal keluar harus diisi');
                    return false;
                }
            }
            return true;
        }

        function showSuccessMessage() {
            alert("Data Berhasil Disimpan!");
        }
    </script>
</body>

</html>