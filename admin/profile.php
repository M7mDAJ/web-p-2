<?php

// Include the database connection file
include '../components/connect.php';

// Check if the tutor's ID is stored in the cookie
if(isset($_COOKIE['tutor_id'])){
   // Retrieve the tutor ID from the cookie
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   // If no tutor ID is found, redirect to the login page
   $tutor_id = '';
   header('location:login.php');
}

// Query to fetch all playlists associated with the tutor
$select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
$select_playlists->execute([$tutor_id]);
$total_playlists = $select_playlists->rowCount();  // Get the total number of playlists

// Query to fetch all contents associated with the tutor
$select_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
$select_contents->execute([$tutor_id]);
$total_contents = $select_contents->rowCount();  // Get the total number of contents

// Query to fetch all likes associated with the tutor
$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
$select_likes->execute([$tutor_id]);
$total_likes = $select_likes->rowCount();  // Get the total number of likes

// Query to fetch all comments associated with the tutor
$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
$select_comments->execute([$tutor_id]);
$total_comments = $select_comments->rowCount();  // Get the total number of comments

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

   <!-- Custom CSS link for styling -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<!-- Include the admin header -->
<?php include '../components/admin_header.php'; ?>
   
<section class="tutor-profile" style="min-height: calc(100vh - 19rem);"> 

   <h1 class="heading">Profile details</h1>

   <div class="details">
      <div class="tutor">
         <!-- Display tutor's profile image -->
         <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <!-- Display tutor's name and profession -->
         <h3><?= $fetch_profile['name']; ?></h3>
         <span><?= $fetch_profile['profession']; ?></span>
         <!-- Link to update profile page -->
         <a href="update.php" class="inline-btn">Update profile</a>
      </div>
      <div class="flex">
         <!-- Display total number of playlists -->
         <div class="box">
            <span><?= $total_playlists; ?></span>
            <p>Total playlists</p>
            <a href="playlists.php" class="btn">View playlists</a>
         </div>
         <!-- Display total number of contents (videos) -->
         <div class="box">
            <span><?= $total_contents; ?></span>
            <p>Total videos</p>
            <a href="contents.php" class="btn">View contents</a>
         </div>
         <!-- Display total number of likes -->
         <div class="box">
            <span><?= $total_likes; ?></span>
            <p>Total likes</p>
            <a href="contents.php" class="btn">View contents</a>
         </div>
         <!-- Display total number of comments -->
         <div class="box">
            <span><?= $total_comments; ?></span>
            <p>Total comments</p>
            <a href="comments.php" class="btn">View comments</a>
         </div>
      </div>
   </div>

</section>

<!-- Include the footer -->
<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
