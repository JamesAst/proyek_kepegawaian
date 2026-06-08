<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $auth = new User();
    if ($auth->login($_POST['nama_user'], $_POST['password'])) {
        header('Location: index.php?page=dashboard');
        exit;
    } else $error = "Login gagal!";
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<div class="login-form">
    <h2>Login Sistem Kepegawaian </h2>
    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="post">
        <input type="text" name="nama_user" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn btn-primary">Masuk</button>
        <p style="text-align: center; margin-top: 15px; color: #bdc3c7;">Anda harus meminta Admin/HRD untuk membuat akun.</p>
    </form>
</div>
</body>
</html>