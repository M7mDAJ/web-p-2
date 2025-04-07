<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

$message = [];

if(isset($_POST['submit'])){

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $msg = filter_var($_POST['msg'], FILTER_SANITIZE_STRING);

   $select_contact = $conn->prepare("SELECT * FROM `contact` WHERE name = ? AND email = ? AND number = ? AND message = ? LIMIT 1");
   $select_contact->execute([$name, $email, $number, $msg]);

   if($select_contact->rowCount() > 0){
      $message[] = 'Message has already been sent!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `contact` (name, email, number, message) VALUES (?, ?, ?, ?)");
      $insert_message->execute([$name, $email, $number, $msg]);
      $message[] = 'Message sent successfully!';
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

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<?php
   if(!empty($message)){
      foreach($message as $msg){
         echo '<div class="message"><span>' . htmlspecialchars($msg) . '</span></div>';
      }
   }
?>

<!-- contact section starts  -->
<section class="contact">

   <div class="row">

      <div class="image">
         <img src="images/contact-img.svg" alt="">
      </div>

      <form action="" method="post">
         <h3>Get in touch</h3>
         <input type="text" placeholder="Enter your name" required maxlength="100" name="name" class="box">
         <input type="email" placeholder="Enter your email" required maxlength="100" name="email" class="box">
         <input type="text" pattern="\d{10}" title="Enter 10-digit phone number" placeholder="Enter your number" required maxlength="10" name="number" class="box">
         <textarea name="msg" class="box" placeholder="Enter your message" required cols="30" rows="10" maxlength="1000"></textarea>
         <input type="submit" value="Send message" class="inline-btn" name="submit">
      </form>

   </div>

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
         <a href="mailto:shaikhanas@gmail.com">shaikhanas@gmail.com</a>
         <a href="mailto:anasbhai@gmail.com">anasbhai@gmail.com</a>
      </div>

      <div class="box">
         <i class="fas fa-map-marker-alt"></i>
         <h3>Office address</h3>
         <a href="#">Flat no. 1, A-1 Building, Jogeshwari, Mumbai, India - 400104</a>
      </div>

   </div>

</section>
<!-- contact section ends -->

<?php include 'components/footer.php'; ?>

<!-- JS -->
<script src="js/script.js"></script>

</body>
</html>
