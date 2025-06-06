<?php

// Include the database connection
include 'components/connect.php';

// Check for user session via cookie
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   // Redirect to home if not logged in
   header('location:home.php');
}

// Handle removing a like
if(isset($_POST['remove'])){

   if($user_id != ''){
      $content_id = $_POST['content_id'];
      $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

      // Check if the content is actually liked by this user
      $verify_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND content_id = ?");
      $verify_likes->execute([$user_id, $content_id]);

      if($verify_likes->rowCount() > 0){
         // Remove like if found
         $remove_likes = $conn->prepare("DELETE FROM `likes` WHERE user_id = ? AND content_id = ?");
         $remove_likes->execute([$user_id, $content_id]);
         $message[] = 'Removed from likes!';
      }
   }else{
      $message[] = 'Please login first!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Liked videos</title>

   <!-- Font Awesome for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file -->
   <link rel="stylesheet" href="css/style1.css">

</head>
<body>

<!-- Include the user header -->
<?php include 'components/user_header.php'; ?>

<!-- Liked videos section starts -->
<section class="liked-videos">

   <h1 class="heading">Liked videos</h1>

   <div class="box-container">

   <?php
      // Fetch liked content by user
      $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
      $select_likes->execute([$user_id]);

      if($select_likes->rowCount() > 0){
         while($fetch_likes = $select_likes->fetch(PDO::FETCH_ASSOC)){

            // Fetch content details
            $select_contents = $conn->prepare("SELECT * FROM `content` WHERE id = ? ORDER BY date DESC");
            $select_contents->execute([$fetch_likes['content_id']]);

            if($select_contents->rowCount() > 0){
               while($fetch_contents = $select_contents->fetch(PDO::FETCH_ASSOC)){

               // Fetch tutor info
               $select_tutors = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
               $select_tutors->execute([$fetch_contents['tutor_id']]);
               $fetch_tutor = $select_tutors->fetch(PDO::FETCH_ASSOC);
   ?>
   <!-- Display a liked video box -->
   <div class="box">
      <div class="tutor">
         <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
         <div>
            <h3><?= $fetch_tutor['name']; ?></h3>
            <span><?= $fetch_contents['date']; ?></span>
         </div>
      </div>
      <img src="uploaded_files/<?= $fetch_contents['thumb']; ?>" alt="" class="thumb">
      <h3 class="title"><?= $fetch_contents['title']; ?></h3>
      <form action="" method="post" class="flex-btn">
         <input type="hidden" name="content_id" value="<?= $fetch_contents['id']; ?>">
         <a href="watch_video.php?get_id=<?= $fetch_contents['id']; ?>" class="inline-btn">Watch video</a>
         <input type="submit" value="Remove" class="inline-delete-btn" name="remove">
      </form>
   </div>
   <?php
               }
            }else{
               echo '<p class="empty">Content was not found!</p>';         
            }
         }
      }else{
         echo '<p class="empty">Nothing added to likes yet!</p>';
      }
   ?>

   </div>

</section>
<!-- Liked videos section ends -->

<!-- Include the footer -->
<?php include 'components/footer.php'; ?>

<!-- Custom JavaScript -->
<script src="js/script.js"></script>
   
</body>
</html>
