<!DOCTYPE html>
<html>

<head>
    <!-- DataTables Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="datepicker/css/datepicker.css">
</head>

<body>
    <div class="container">
        <div class="card mt-5">
            <div class="card-header">
                <h4>Data Karyawan</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <p>Filter</p>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="form-group mx-3">
                                        <label>Tanggal Masuk :</label>
                                        <input type="text" id="tanggal" name="tanggal" class="form-control datepicker" onchange="changeDate()">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="tambah_karyawan.php" class="btn btn-primary my-3">Tambah Karyawan</a> 
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="table-responsive">
                            <center>
                                <table id="karyawanTable" class="table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>NIK</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Nama</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Alamat</th>
                                            <th>Kota</th>
                                            <th>Gelar</th>
                                            <th>Tanggal Keluar</th>
                                            <th>Status</th>
                                            <th>
                                                <center>Action</center>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        // Lakukan koneksi ke database atau ambil data dari mana pun yang diperlukan
                                        // Misalnya, kita akan mengasumsikan koneksi ke database telah dibuat sebelumnya
                                        $servername = "localhost";
                                        $username = "root";
                                        $password = "";
                                        $dbname = "db_karyawan";

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

                                        // Tambahkan filter jika tanggal disediakan
                                        if (isset($_GET['tanggal']) && $_GET['tanggal'] != '') {
                                            $tanggal = date('Y-m-d', strtotime($_GET['tanggal']));
                                            $query .= " WHERE (tanggal_masuk <= '$tanggal' AND (tanggal_keluar > '$tanggal' OR tanggal_keluar IS NULL))";
                                        }

                                        // Eksekusi query dan ambil data
                                        $sql_data = mysqli_query($conn, $query);

                                        // Ambil data hasil query
                                        while ($row = mysqli_fetch_assoc($sql_data)) {

                                        ?>

                                            <tr>
                                                <td><?php echo $row['nik']; ?></td>
                                                <td><?php echo $row['tanggal_masuk']; ?></td>
                                                <td><?php echo $row['nama']; ?></td>
                                                <td><?php echo $row['jenis_kelamin_label']; ?></td>
                                                <td><?php echo $row['alamat']; ?></td>
                                                <td><?php echo $row['kota']; ?></td>
                                                <td><?php echo $row['nama_gelar']; ?></td>
                                                <td><?php echo $row['tanggal_keluar']; ?></td>
                                                <td>APA YA</td>
                                                <td>
                                                    <center>
                                                        <a href="edit_karyawan.php?nik=<?php echo $row['nik']; ?>" class="btn btn-warning">Edit</a>
                                                        <a href="hapus_karyawan.php?nik=<?php echo $row['nik']; ?>" class="btn btn-danger">Hapus</a>
                                                    </center>
                                                </td>
                                            </tr>

                                        <?php
                                        }
                                        ?>

                                    </tbody>
                                </table>
                            </center>
                        </div>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-12 col-12">
                        <hr>
                        <div class="table-responsive">
                            <center>
                                <table id="dataTable" class="table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Gelar</th>
                                            <th>Jumlah Laki-Laki</th>
                                            <th>Jumlah Perempuan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Lakukan koneksi ke database atau ambil data dari mana pun yang diperlukan
                                        // Misalnya, kita akan mengasumsikan koneksi ke database telah dibuat sebelumnya

                                        $servername = "localhost";
                                        $username = "root";
                                        $password = "";
                                        $dbname = "db_karyawan";

                                        // Membuat koneksi
                                        $conn = new mysqli($servername, $username, $password, $dbname);

                                        if ($conn->connect_error) {
                                            die("Connection failed: " . $conn->connect_error);
                                        }

                                        $query = "SELECT g.nama_gelar, COUNT(k.jenis_kelamin) AS jumlah_perempuan
                                        FROM gelar g
                                        LEFT JOIN karyawan k ON k.gelar = g.id AND k.jenis_kelamin ='p'
                                        WHERE g.nama_gelar ='SMA'
                                        GROUP BY g.nama_gelar";

                                        $query2 = "SELECT g.nama_gelar, COUNT(k.jenis_kelamin) AS jumlah_perempuan
                                        FROM gelar g
                                        LEFT JOIN karyawan k ON k.gelar = g.id AND k.jenis_kelamin ='p'
                                        WHERE g.nama_gelar ='S1'
                                        GROUP BY g.nama_gelar";

                                        $query3 = "SELECT g.nama_gelar, COUNT(k.jenis_kelamin) AS jumlah_perempuan
                                        FROM gelar g
                                        LEFT JOIN karyawan k ON k.gelar = g.id AND k.jenis_kelamin ='p'
                                        WHERE g.nama_gelar ='S2'
                                        GROUP BY g.nama_gelar";

                                        $query4 = "SELECT g.nama_gelar, COUNT(k.jenis_kelamin) AS jumlah_perempuan
                                        FROM gelar g
                                        LEFT JOIN karyawan k ON k.gelar = g.id AND k.jenis_kelamin ='p'
                                        WHERE g.nama_gelar ='S3'
                                        GROUP BY g.nama_gelar";

                                        $query5 = "SELECT g.nama_gelar, COUNT(k.jenis_kelamin) AS jumlah_laki_laki
                                        FROM gelar g
                                        LEFT JOIN karyawan k ON k.gelar = g.id AND k.jenis_kelamin ='l'
                                        WHERE g.nama_gelar ='SMA'
                                        GROUP BY g.nama_gelar";

                                        $query6 = "SELECT g.nama_gelar, COUNT(k.jenis_kelamin) AS jumlah_laki_laki
                                        FROM gelar g
                                        LEFT JOIN karyawan k ON k.gelar = g.id AND k.jenis_kelamin ='l'
                                        WHERE g.nama_gelar ='S1'
                                        GROUP BY g.nama_gelar";

                                        $query7 = "SELECT g.nama_gelar, COUNT(k.jenis_kelamin) AS jumlah_laki_laki
                                        FROM gelar g
                                        LEFT JOIN karyawan k ON k.gelar = g.id AND k.jenis_kelamin ='l'
                                        WHERE g.nama_gelar ='S2'
                                        GROUP BY g.nama_gelar";

                                        $query8 = "SELECT g.nama_gelar, COUNT(k.jenis_kelamin) AS jumlah_laki_laki
                                        FROM gelar g
                                        LEFT JOIN karyawan k ON k.gelar = g.id AND k.jenis_kelamin ='l'
                                        WHERE g.nama_gelar ='S3'
                                        GROUP BY g.nama_gelar";

                                        $result1 = $conn->query($query);
                                        $result2 = $conn->query($query2);
                                        $result3 = $conn->query($query3);
                                        $result4 = $conn->query($query4);
                                        $result5 = $conn->query($query5);
                                        $result6 = $conn->query($query6);
                                        $result7 = $conn->query($query7);
                                        $result8 = $conn->query($query8);
                                        // Membuat array untuk menyimpan jumlah laki-laki dan perempuan untuk setiap gelar
                                        $gelar_data = array();

                                        // Jalankan setiap kueri dan kumpulkan hasilnya
                                        $queries = array($query, $query2, $query3, $query4, $query5, $query6, $query7, $query8);

                                        foreach ($queries as $query) {
                                            $result = $conn->query($query);
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    // Menambahkan jumlah laki-laki dan perempuan untuk setiap gelar ke dalam array
                                                    $gelar = $row["nama_gelar"];
                                                    if (!isset($gelar_data[$gelar])) {
                                                        $gelar_data[$gelar] = array("jumlah_laki_laki" => 0, "jumlah_perempuan" => 0);
                                                    }
                                                    if (isset($row["jumlah_laki_laki"])) {
                                                        $gelar_data[$gelar]["jumlah_laki_laki"] += $row["jumlah_laki_laki"];
                                                    }
                                                    if (isset($row["jumlah_perempuan"])) {
                                                        $gelar_data[$gelar]["jumlah_perempuan"] += $row["jumlah_perempuan"];
                                                    }
                                                }
                                            }
                                        }

                                        // Menampilkan data dalam tabel HTML
                                        foreach ($gelar_data as $gelar => $data) {
                                            echo "<tr>";
                                            echo "<td>" . $gelar . "</td>";
                                            echo "<td>" . $data["jumlah_laki_laki"] . "</td>";
                                            echo "<td>" . $data["jumlah_perempuan"] . "</td>";
                                            echo "</tr>";
                                        }

                                        // // Query untuk mengambil data karyawan dan jumlah laki-laki dan perempuan per gelar
                                        // $query = "SELECT k.gelar, g.nama_gelar,
                                        //     SUM(CASE WHEN k.jenis_kelamin = 'L' THEN 1 ELSE 0 END) AS jumlah_laki_laki,
                                        //     SUM(CASE WHEN k.jenis_kelamin = 'P' THEN 1 ELSE 0 END) AS jumlah_perempuan
                                        //   FROM karyawan k
                                        //   INNER JOIN gelar g ON k.gelar = g.id
                                        //   GROUP BY k.gelar";

                                        // $result = $conn->query($query);

                                        // // Buat tabel HTML untuk menampilkan data karyawan
                                        // if ($result->num_rows > 0) {
                                        //     while ($row = $result->fetch_assoc()) {
                                        //         echo "<tr>";
                                        //         echo "<td>" . $row["nama_gelar"] . "</td>";
                                        //         echo "<td>" . $row["jumlah_laki_laki"] . "</td>";
                                        //         echo "<td>" . $row["jumlah_perempuan"] . "</td>";
                                        //         // echo action button here
                                        //         echo "</tr>";
                                        //     }
                                        // } else {
                                        //     echo "<tr><td colspan='9'>Tidak ada data karyawan</td></tr>";
                                        // }

                                        $conn->close();
                                        ?>
                                    </tbody>
                                </table>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
    <script src="datepicker/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript">
        var table;
        $(document).ready(function() {
            // Initialize datepicker
            $(".datepicker").datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true,
            });

            // Initialize DataTable
            table = new DataTable('#karyawanTable', {});
        });

        function changeDate() {
            var tanggal = $('#tanggal').val();
            window.location.href = 'index_2.php?tanggal=' + tanggal;
        }

    </script>
</body>


</html>