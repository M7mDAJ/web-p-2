<?php

include '../components/connect.php';

$message = [];

if (isset($_POST['submit'])) {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $pass = $_POST['pass'];

    if ($conn) {
        $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE email = ? LIMIT 1");
        $select_tutor->execute([$email]);
        $row = $select_tutor->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($pass, $row['password'])) {
            setcookie('tutor_id', $row['id'], time() + 60 * 60 * 24 * 30, '/', '', true, true);
            header('location:dashboard.php');
            exit();
        } else {
            $message[] = 'Incorrect email or password!';
        }
    } else {
        $message[] = 'Database connection error!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body style="padding-left: 0;">

<?php
if (!empty($message)) {
    foreach ($message as $msg) {
        echo '<div class="message form">
                <span>' . htmlspecialchars($msg) . '</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
              </div>';
    }
}
?>

<!-- Login Section Starts -->

<section class="form-container">
    <form action="" method="post" class="login">
        <h3>Welcome back!</h3>
        <p>Your email <span>*</span></p>
        <input type="email" name="email" placeholder="Enter your email" maxlength="50" required class="box">
        <p>Your password <span>*</span></p>
        <input type="password" name="pass" placeholder="Enter your password" maxlength="50" required class="box">
        <p class="link">Don't have an account? <a href="register.php">Register new</a></p>
        <input type="submit" name="submit" value="Login Now" class="btn">
    </form>
</section>

<!-- Login Section Ends -->

<script>
let darkMode = localStorage.getItem('dark-mode');
let body = document.body;

const enableDarkMode = () => {
    body.classList.add('dark');
    localStorage.setItem('dark-mode', 'enabled');
};

const disableDarkMode = () => {
    body.classList.remove('dark');
    localStorage.setItem('dark-mode', 'disabled');
};

if (darkMode === 'enabled') {
    enableDarkMode();
} else {
    disableDarkMode();
}
</script>

</body>
</html>
