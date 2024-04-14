<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <title>Form Data Karyawan</title>
</head>

<body>
    <div class="container">
        <div class="card mt-5">
            <div class="card-title ml-3">
                <h4>Form Data Karyawan</h4>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="row mt-2">
                        <div class="col-md-6 col-12">
                            <label for="nik">NIK:</label>
                            <input class="form-control" type="text" id="nik" name="nik">
                        </div>
                        <div class="col-md-6 col-12">
                            <label for="tanggal_masuk">Tanggal Masuk:</label>
                            <input class="form-control" type="date" id="tanggal_masuk" name="tanggal_masuk">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <label for="nama">Nama:</label>
                            <input class="form-control" type="text" id="nama" name="nama">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6 col-12">
                            <label for="alamat">Alamat:</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mt-1 col-12">
                            <label for="kota">Kota:</label>
                            <input class="form-control" type="text" id="kota" name="kota">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6 col-12">
                            <label for="gelar">Gelar:</label>
                            <select class="form-select" ; name="gelar">
                                <option selected disabled>--Pilih Satu--</option>
                                <option value="sma">SMA</option>
                                <option value="s1">S1</option>
                                <option value="s2">S2</option>
                                <option value="s3">S3</option>
                            </select>
                        </div>
                        <!-- <div class="col-md-6 col-12">
                            <label for="tanggal_keluar">Tanggal Keluar:</label>
                            <input class="form-control" type="date" id="tanggal_keluar" name="tanggal_keluar">
                        </div> -->

                        <!-- UpdateOrCreated -->
                        <!-- <?php if ($_POST['action'] == 'update') : ?>
                            <label for="tgl_keluar">Tanggal Keluar:</label><br>
                            <input type="date" id="tgl_keluar" name="tgl_keluar"><br>
                        <?php else : ?>
                            <input type="hidden" id="tgl_keluar" name="tgl_keluar" value=""><br>
                        <?php endif; ?>

                        <input type="hidden" name="action" value="<?php echo isset($_GET['nik']) ? 'update' : 'insert'; ?>">
                        <input type="submit" name="submit" value="Submit"> -->
                        <!-- End Of UpdateOrCreated -->

                    </div>
                    <div class="row mt-3">
                        <div class="col-10"></div>
                        <div class="col-2">
                            <input type="submit" class="btn btn-primary mt-3" value="submit" name="submit">
                            <a class="btn btn-secondary mt-3" href="index.php" role="button">Kembali</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "db_karyawan";

    //membuat koneksi
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("connection failed: " . $conn->connect_error);
    }

    function validateNik($nik)
    {
        // Validasi menggunakan regular expression
        if (preg_match("/^[a-zA-Z0-9]{12}$/", $nik)) {
            return true;
        } else {
            return false;
        }
    }

    // function validateNama($string){
    //     // return ctype_alpha($string);
    //     if (preg_match("/^[a-zA-Z\s,.]+$/", $string)) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }
    function validateNama($str)
    {
        // Validasi menggunakan regular expression
        if (preg_match("/^[a-zA-Z]+(?:\s*[.,]?\s*[a-zA-Z]+)*$/", $str)) {
            return true;
        } else {
            return false;
        }
    }

    function validateKota($str)
    {
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

    if (isset($_POST['submit'])) {
        $nik            = $_POST['nik'];
        $tanggal_masuk  = $_POST['tanggal_masuk'];
        $nama           = $_POST['nama'];
        $alamat         = $_POST['alamat'];
        $kota           = $_POST['kota'];
        $gelar          = isset($_POST['gelar']) ? $_POST['gelar'] : '';
        // $tanggal_keluar = !empty($_POST['tanggal_keluar']) ? $_POST['tanggal_keluar'] : null;

        // Lakukan validasi input kosong
        $errors = array();
        if (empty($nik) || empty($tgl_masuk) || empty($nama) || empty($alamat) || empty($kota) || empty($gelar)) {
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

        // Jika terdapat error, tampilkan pesan error
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<p class='error'>$error</p>";
            }
        } else {
            if ($_POST['action'] == '')
                //simpan data ke database
                $sql = "INSERT INTO karyawan (nik, tanggal_masuk, nama, alamat, kota, gelar, tanggal_keluar)
                VALUES ('$nik', '$tanggal_masuk', '$nama', '$alamat', '$kota', '$gelar', NULL)";

            // //UpdateOrCreated
            // if ($_POST['action'] == 'insert') {
            //     // Insert data karyawan ke dalam database
            //     $sql = "INSERT INTO karyawan (nik, tgl_masuk, nama, alamat, kota, gelar, tgl_keluar)
            // VALUES ('$nik', '$tgl_masuk', '$nama', '$alamat', '$kota', '$gelar', '$tgl_keluar')";
            // } elseif ($_POST['action'] == 'update') {
            //     // Update data karyawan ke dalam database
            //     $sql = "UPDATE karyawan SET tgl_masuk = '$tgl_masuk', nama = '$nama', alamat = '$alamat',
            // kota = '$kota', gelar = '$gelar', tgl_keluar = '$tgl_keluar' WHERE nik = '$nik'";
            // }
            // //End Of UpdateOrCreated


            if ($conn->query($sql) === TRUE) {
                echo '<script>window.location.href = "index.php";</script>';
                // header("Location : index.php");
                exit();
            } else {
                echo "error: " . $conn->error;
            }
        }
    }
    $conn->close();
    ?>
</body>

</html>