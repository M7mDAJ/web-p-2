<?php

// Include database connection file
include 'components/connect.php';

// Check if user_id is set in the cookies (user is logged in)
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   // If not logged in, redirect to login page
   $user_id = '';
   header('location:login.php');
}

// Prepare and execute SQL query to fetch the total number of likes for the user
$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
$select_likes->execute([$user_id]);
$total_likes = $select_likes->rowCount();

// Prepare and execute SQL query to fetch the total number of comments for the user
$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
$select_comments->execute([$user_id]);
$total_comments = $select_comments->rowCount();

// Prepare and execute SQL query to fetch the total number of bookmarked playlists for the user
$select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ?");
$select_bookmark->execute([$user_id]);
$total_bookmarked = $select_bookmark->rowCount();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Profile</title>

   <!-- Font Awesome CDN link for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file link, changed to style1.css as per request -->
   <link rel="stylesheet" href="css/style1.css">

</head>
<body>

<?php include 'components/user_header.php'; // Include user header ?>

<section class="profile">

   <h1 class="heading">Profile details</h1>

   <div class="details">

      <!-- User profile section -->
      <div class="user">
         <img src="uploaded_files/<?= $fetch_profile['image']; ?>" alt="Profile Image">
         <h3><?= $fetch_profile['name']; ?></h3>
         <p>Student</p>
         <a href="update.php" class="inline-btn">Update profile</a>
      </div>

      <div class="box-container">

         <!-- Box for saved playlists -->
         <div class="box">
            <div class="flex">
               <i class="fas fa-bookmark"></i>
               <div>
                  <h3><?= $total_bookmarked; ?></h3>
                  <span>Saved playlists</span>
               </div>
            </div>
            <a href="#" class="inline-btn">View playlists</a>
         </div>

         <!-- Box for liked tutorials -->
         <div class="box">
            <div class="flex">
               <i class="fas fa-heart"></i>
               <div>
                  <h3><?= $total_likes; ?></h3>
                  <span>Liked tutorials</span>
               </div>
            </div>
            <a href="#" class="inline-btn">View liked</a>
         </div>

         <!-- Box for video comments -->
         <div class="box">
            <div class="flex">
               <i class="fas fa-comment"></i>
               <div>
                  <h3><?= $total_comments; ?></h3>
                  <span>Video comments</span>
               </div>
            </div>
            <a href="#" class="inline-btn">View comments</a>
         </div>

      </div>

   </div>

</section>

<!-- Profile section ends -->

<!-- Footer section starts -->
<footer class="footer">
   &copy; copyright @ 2022 by <span>mr. web designer</span> | all rights reserved!
</footer>
<!-- Footer section ends -->

<!-- Custom JS file link -->
<script src="js/script.js"></script>
   
</body>
</html>
