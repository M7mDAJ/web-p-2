<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
   header('location:home.php');
   exit;
}

$message = [];

if (isset($_POST['submit'])) {
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $pass = $_POST['pass'];

   // جلب المستخدم حسب الإيميل
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? LIMIT 1");
   $select_user->execute([$email]);

   if ($select_user->rowCount() > 0) {
      $row = $select_user->fetch(PDO::FETCH_ASSOC);

      // التحقق من كلمة المرور باستخدام password_verify
      if (password_verify($pass, $row['password'])) {
         setcookie('user_id', $row['id'], time() + 60*60*24*30, '/');
         header('location:home.php');
         exit;
      } else {
         $message[] = 'Incorrect password!';
      }
   } else {
      $message[] = 'Email not found!';
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

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data" class="login">
      <h3>Welcome back!</h3>

      <!-- عرض الرسائل -->
      <?php
      if (!empty($message)) {
         foreach ($message as $msg) {
            echo '<p class="message">' . htmlspecialchars($msg) . '</p>';
         }
      }
      ?>

      <p>Your email <span>*</span></p>
      <input type="email" name="email" placeholder="Enter your email" maxlength="50" required class="box">

      <p>Your password <span>*</span></p>
      <input type="password" name="pass" placeholder="Enter your password" maxlength="20" required class="box">

      <p class="link">Don't have an account? <a href="register.php">Register now</a></p>

      <input type="submit" name="submit" value="Login now" class="btn">
   </form>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
