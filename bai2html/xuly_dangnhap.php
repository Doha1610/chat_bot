<?php
session_start(); // Bắt đầu session

// Kết nối CSDL
$sv = "localhost";
$us = "root";
$pw = "";
$db = "bt1";

$conn = mysqli_connect($sv, $us, $pw, $db);

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Lấy dữ liệu từ form
$username = $_POST['username'];
$password = $_POST['password'];

// Truy vấn CSDL để tìm người dùng
$sql = "SELECT user_id, username, password FROM dangky WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // Kiểm tra mật khẩu đã băm
    if (password_verify($password, $row['password'])) {
        // Đăng nhập thành công
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];

        echo "<script>alert('Đăng nhập thành công'); window.location.href='trangchu.php';</script>";
        exit();
    } else {
        // Mật khẩu không đúng
        echo "<script>alert('Mật khẩu không đúng'); window.location.href='dangnhap.php';</script>";
    }
} else {
    // Người dùng không tồn tại
    echo "<script>alert('Người dùng không tồn tại'); window.location.href='dangnhap.php';</script>";
}

// Đóng kết nối
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>