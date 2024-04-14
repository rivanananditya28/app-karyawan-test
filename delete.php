<?php
// delete_karyawan.php

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

// Pastikan NIK dikirimkan melalui metode POST
if (isset($_GET['id'])) {
    $nik = $_GET["id"];

    // Buat query untuk menghapus rekaman dengan NIK tertentu
    $query = "DELETE FROM karyawan WHERE nik='$nik'";

    // Eksekusi query
    if ($conn->query($query) === TRUE) {
        // Jika berhasil menghapus, redirect kembali ke halaman index.php atau halaman lainnya
        header("Location: index.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    // Jika NIK tidak dikirimkan, tampilkan pesan error
    echo "NIK is required";
}
?>