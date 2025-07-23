<!DOCTYPE html>
<html>
<head>
    <title>Đăng ký</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #3e8e41;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #4CAF50;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Đăng ký</h2>
        <form method="post">
            <label for="username">Tên đăng nhập:</label>
            <input type="text" id="username" name="username" required>
            <span class="error"><?php if(isset($username_error)) echo $username_error; ?></span>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <span class="error"><?php if(isset($email_error)) echo $email_error; ?></span>

            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" required>
            <span class="error"><?php if(isset($password_error)) echo $password_error; ?></span>

            <label for="confirm_password">Xác nhận mật khẩu:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <span class="error"><?php if(isset($confirm_password_error)) echo $confirm_password_error; ?></span>

            <label for="mobile">Số điện thoại:</label>
            <input type="tel" id="mobile" name="mobile" required>

            <button type="submit" name="sbm">Đăng ký</button>
        </form>
        <div class="login-link">
            Already have an account? <a href="dangnhap.php">Đăng nhập</a>
        </div>
    </div>
</body>
</html>
<?php
// Kết nối CSDL
$sv = "localhost";
$us = "root";
$pw = "";
$db = "bt1";

$conn = mysqli_connect($sv, $us, $pw, $db);

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Khởi tạo biến lỗi
$username_error = $email_error = $confirm_password_error = '';

// Xử lý form đăng ký
if (isset($_POST['sbm'])) {
    try {
        $tdn = $_POST['username'];
        $mk = $_POST['password'];
        $nlmk = $_POST['confirm_password'];
        $el = $_POST['email'];
        $sdt = $_POST['mobile'];

        // Kiểm tra tên đăng nhập
        $sql1 = "SELECT username FROM dangky WHERE username = ?";
        $stmt1 = mysqli_prepare($conn, $sql1);
        mysqli_stmt_bind_param($stmt1, "s", $tdn);
        mysqli_stmt_execute($stmt1);
        mysqli_stmt_store_result($stmt1);

        // Kiểm tra email
        $sql4 = "SELECT email FROM dangky WHERE email = ?";
        $stmt4 = mysqli_prepare($conn, $sql4);
        mysqli_stmt_bind_param($stmt4, "s", $el);
        mysqli_stmt_execute($stmt4);
        mysqli_stmt_store_result($stmt4);

        if (mysqli_stmt_num_rows($stmt1) > 0) {
            $username_error = "Tên đăng nhập đã tồn tại";
        }

        if (mysqli_stmt_num_rows($stmt4) > 0) {
            $email_error = "Email đã tồn tại";
        }

        if ($mk != $nlmk) {
            $confirm_password_error = "Mật khẩu không trùng khớp";
        }

        if (empty($username_error) && empty($email_error) && empty($confirm_password_error)) {
            $hashed_password = password_hash($mk, PASSWORD_DEFAULT);
            
            $sql2 = "INSERT INTO dangky (username, password, email, mobile) VALUES (?, ?, ?, ?)";
            $stmt2 = mysqli_prepare($conn, $sql2);
            
            if ($stmt2) {
                mysqli_stmt_bind_param($stmt2, "ssss", $tdn, $hashed_password, $el, $sdt);
                $result2 = mysqli_stmt_execute($stmt2);
                
                if ($result2) {
                    echo "<script>alert('Đăng ký thành công'); window.location.href='dangnhap.php';</script>";
                    exit();
                } else {
                    die("Đăng ký thất bại: " . mysqli_error($conn));
                }
            }
        }
    } finally {
        if (isset($stmt1)) mysqli_stmt_close($stmt1);
        if (isset($stmt4)) mysqli_stmt_close($stmt4);
        if (isset($stmt2)) mysqli_stmt_close($stmt2);
        mysqli_close($conn);
    }
}
?>