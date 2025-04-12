<?php

// Include the database connection
include 'components/connect.php';

// Check if the user is already logged in through cookies
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

// Handle login form submission
if(isset($_POST['submit'])){

   // Sanitize and hash password
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   // Verify user credentials
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ? LIMIT 1");
   $select_user->execute([$email, $pass]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);
   
   // If user found, set cookie and redirect
   if($select_user->rowCount() > 0){
     setcookie('user_id', $row['id'], time() + 60*60*24*30, '/');
     header('location:home.php');
   }else{
      $message[] = 'Incorrect email or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>

   <!-- Font Awesome for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file -->
   <link rel="stylesheet" href="css/style1.css">

</head>
<body>

<!-- Include the user header -->
<?php include 'components/user_header.php'; ?>

<!-- Login form section starts -->
<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data" class="login">
      <h3>Welcome back!</h3>
      <p>Your email <span>*</span></p>
      <input type="email" name="email" placeholder="Enter your email" maxlength="50" required class="box">
      <p>Your password <span>*</span></p>
      <input type="password" name="pass" placeholder="Enter your password" maxlength="20" required class="box">
      <p class="link">Don't have an account? <a href="register.php">Register now</a></p>
      <input type="submit" name="submit" value="Login now" class="btn">
   </form>

</section>
<!-- Login form section ends -->

<!-- Include the footer -->
<?php include 'components/footer.php'; ?>

<!-- Custom JavaScript -->
<script src="js/script.js"></script>
   
</body>
</html>
