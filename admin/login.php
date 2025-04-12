<?php

// Include the database connection file
include '../components/connect.php';

// Check if the form is submitted
if(isset($_POST['submit'])){

   // Get the submitted email and sanitize it
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   // Get the submitted password, hash it with SHA1, and sanitize it
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   // Prepare SQL query to check if a tutor exists with the provided email and password
   $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE email = ? AND password = ? LIMIT 1");
   $select_tutor->execute([$email, $pass]);

   // Fetch the tutor's data
   $row = $select_tutor->fetch(PDO::FETCH_ASSOC);
   
   // If a match is found, set a cookie and redirect to dashboard
   if($select_tutor->rowCount() > 0){
     // Set a cookie named 'tutor_id' valid for 30 days
     setcookie('tutor_id', $row['id'], time() + 60*60*24*30, '/');
     // Redirect to the dashboard
     header('location:dashboard.php');
   }else{
      // Show error message if credentials are incorrect
      $message[] = 'incorrect email or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Meta tags and page setup -->
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>

   <!-- Font Awesome CDN for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom admin styles -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body style="padding-left: 0;">

<?php
// Show feedback message if one exists
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message form">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<!-- login form section starts -->
<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data" class="login">
      <h3>Welcome back!</h3>
      <!-- Email field -->
      <p>Your email <span>*</span></p>
      <input type="email" name="email" placeholder="Enter your email" maxlength="20" required class="box">
      
      <!-- Password field -->
      <p>Your password <span>*</span></p>
      <input type="password" name="pass" placeholder="Enter your password" maxlength="20" required class="box">
      
      <!-- Link to registration page -->
      <p class="link">Don't have an account? <a href="register.php">Register new</a></p>

      <!-- Submit button -->
      <input type="submit" name="submit" value="login now" class="btn">
   </form>

</section>
<!-- login form section ends -->

<!-- Dark mode toggle script -->
<script>

// Get saved dark mode preference
let darkMode = localStorage.getItem('dark-mode');
let body = document.body;

// Function to enable dark mode
const enabelDarkMode = () =>{
   body.classList.add('dark');
   localStorage.setItem('dark-mode', 'enabled');
}

// Function to disable dark mode
const disableDarkMode = () =>{
   body.classList.remove('dark');
   localStorage.setItem('dark-mode', 'disabled');
}

// Apply dark mode preference on load
if(darkMode === 'enabled'){
   enabelDarkMode();
}else{
   disableDarkMode();
}

</script>
   
</body>
</html>
