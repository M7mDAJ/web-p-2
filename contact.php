<?php

// Connect to the database
include 'components/connect.php';

// Check if the user is logged in using a cookie
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

// Check if the contact form is submitted
if(isset($_POST['submit'])){

   // Get and sanitize form inputs
   $name = $_POST['name']; 
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email']; 
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number']; 
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $msg = $_POST['msg']; 
   $msg = filter_var($msg, FILTER_SANITIZE_STRING);

   // Check if the same message has already been sent
   $select_contact = $conn->prepare("SELECT * FROM `contact` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $select_contact->execute([$name, $email, $number, $msg]);

   if($select_contact->rowCount() > 0){
      // Message already exists
      $message[] = 'message sent already!';
   }else{
      // Insert new message into the contact table
      $insert_message = $conn->prepare("INSERT INTO `contact`(name, email, number, message) VALUES(?,?,?,?)");
      $insert_message->execute([$name, $email, $number, $msg]);
      $message[] = 'message sent successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Contact</title>

   <!-- Font Awesome CDN link for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file link (filename changed as requested) -->
   <link rel="stylesheet" href="css/style1.css">

</head>
<body>

<!-- Include the user header -->
<?php include 'components/user_header.php'; ?>

<!-- contact section starts  -->
<section class="contact">

   <div class="row">

      <div class="image">
         <!-- Contact image -->
         <img src="images/contact-img.svg" alt="">
      </div>

      <!-- Contact form -->
      <form action="" method="post">
         <h3>Get in touch</h3>
         <input type="text" placeholder="Enter your name" required maxlength="100" name="name" class="box">
         <input type="email" placeholder="Enter your email" required maxlength="100" name="email" class="box">
         <input type="number" min="0" max="9999999999" placeholder="Enter your number" required maxlength="10" name="number" class="box">
         <textarea name="msg" class="box" placeholder="Enter your message" required cols="30" rows="10" maxlength="1000"></textarea>
         <input type="submit" value="send message" class="inline-btn" name="submit">
      </form>

   </div>

   <!-- Contact information boxes -->
   <div class="box-container">

      <div class="box">
         <i class="fas fa-phone"></i>
         <h3>Phone number</h3>
         <a href="tel:1234567890">123-456-7890</a>
         <a href="tel:1112223333">111-222-3333</a>
      </div>

      <div class="box">
         <i class="fas fa-envelope"></i>
         <h3>Email address</h3>
         <a href="mailto:example@gmail.com">example@gmail.come</a>
         <a href="mailto:example@gmail.com">example@gmail.come</a>
      </div>

      <div class="box">
         <i class="fas fa-map-marker-alt"></i>
         <h3>Office address</h3>
         <a href="#">Al-Mansour District, Street 14  
         Baghdad, Iraq – 10013</a>
      </div>

   </div>

</section>
<!-- contact section ends -->

<!-- Include the footer -->
<?php include 'components/footer.php'; ?>  

<!-- Custom JS file link -->
<script src="js/script.js"></script>
   
</body>
</html>
