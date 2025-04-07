<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
   $user_id = $_COOKIE['user_id'];
} else {
   $user_id = '';
}

if (isset($_POST['submit'])) {

   $id = unique_id();
   $name = htmlspecialchars(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
   $email = htmlspecialchars(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];

   $image = $_FILES['image']['name'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_size = $_FILES['image']['size'];
   $image_error = $_FILES['image']['error'];

   if ($image_error === 0 && $image_size > 0) {
      $image = filter_var($image, FILTER_SANITIZE_STRING);
      $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
      $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

      if (!in_array($ext, $allowed_exts)) {
         $message[] = 'Invalid image format. Allowed: jpg, jpeg, png, webp';
      } elseif ($image_size > 2 * 1024 * 1024) {
         $message[] = 'Image size is too large. Max 2MB allowed.';
      } else {
         $rename = unique_id() . '.' . $ext;
         $image_folder = 'uploaded_files/' . $rename;

         $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
         $select_user->execute([$email]);

         if ($select_user->rowCount() > 0) {
            $message[] = 'Email already taken!';
         } else {
            if ($pass !== $cpass) {
               $message[] = 'Confirm password does not match!';
            } else {
               $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
               $insert_user = $conn->prepare("INSERT INTO `users`(id, name, email, password, image) VALUES(?,?,?,?,?)");
               $insert_user->execute([$id, $name, $email, $hashed_password, $rename]);
               move_uploaded_file($image_tmp_name, $image_folder);

               $verify_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? LIMIT 1");
               $verify_user->execute([$email]);
               $row = $verify_user->fetch(PDO::FETCH_ASSOC);

               if ($verify_user->rowCount() > 0 && password_verify($pass, $row['password'])) {
                  setcookie('user_id', $row['id'], time() + 60 * 60 * 24 * 30, '/');
                  header('location:home.php');
                  exit;
               }
            }
         }
      }
   } else {
      $message[] = 'Please upload an image.';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container">

   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3>Create account</h3>
      <div class="flex">
         <div class="col">
            <p>Full name<span>*</span></p>
            <input type="text" name="name" placeholder="Enter your name" maxlength="50" required class="box">
            <p>Email address<span>*</span></p>
            <input type="email" name="email" placeholder="Enter your email" maxlength="50" required class="box">
         </div>
         <div class="col">
            <p>Password <span>*</span></p>
            <input type="password" name="pass" placeholder="Enter your password" maxlength="50" required class="box">
            <p>Confirm password <span>*</span></p>
            <input type="password" name="cpass" placeholder="Confirm your password" maxlength="50" required class="box">
         </div>
      </div>
      <p>Select avatar<span>*</span></p>
      <input type="file" name="image" accept="image/*" required class="box">
      <p class="link">Already have an account? <a href="login.php">Login now</a></p>
      <input type="submit" name="submit" value="Register now" class="btn">
   </form>

</section>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
