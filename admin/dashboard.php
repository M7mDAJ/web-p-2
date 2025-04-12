<?php

// Connect to the database
include '../components/connect.php';

// Check if the tutor is logged in via cookie
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   // Redirect to login if not logged in
   $tutor_id = '';
   header('location:login.php');
}

// Fetch total number of content created by the tutor
$select_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
$select_contents->execute([$tutor_id]);
$total_contents = $select_contents->rowCount();

// Fetch total number of playlists created by the tutor
$select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
$select_playlists->execute([$tutor_id]);
$total_playlists = $select_playlists->rowCount();

// Fetch total number of likes received on the tutor's content
$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
$select_likes->execute([$tutor_id]);
$total_likes = $select_likes->rowCount();

// Fetch total number of comments on the tutor's content
$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
$select_comments->execute([$tutor_id]);
$total_comments = $select_comments->rowCount();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Meta and page setup -->
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- Font Awesome icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom admin style -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<!-- Admin header section -->
<?php include '../components/admin_header.php'; ?>
   
<section class="dashboard">

   <h1 class="heading">Dashboard</h1>

   <div class="box-container">

      <!-- Welcome box -->
      <div class="box">
         <h3>Welcome!</h3>
         <p><?= $fetch_profile['name']; ?></p>
         <a href="profile.php" class="btn">View profile</a>
      </div>

      <!-- Total contents box -->
      <div class="box">
         <h3><?= $total_contents; ?></h3>
         <p>Total contents</p>
         <a href="add_content.php" class="btn">Add new content</a>
      </div>

      <!-- Total playlists box -->
      <div class="box">
         <h3><?= $total_playlists; ?></h3>
         <p>Total playlists</p>
         <a href="add_playlist.php" class="btn">Add new playlist</a>
      </div>

      <!-- Total likes box -->
      <div class="box">
         <h3><?= $total_likes; ?></h3>
         <p>Total likes</p>
         <a href="contents.php" class="btn">View contents</a>
      </div>

      <!-- Total comments box -->
      <div class="box">
         <h3><?= $total_comments; ?></h3>
         <p>Total comments</p>
         <a href="comments.php" class="btn">View comments</a>
      </div>

      <!-- Quick access login/register -->
      <div class="box">
         <h3>Quick select</h3>
         <p>Login or Register</p>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>
      </div>

   </div>

</section>

<!-- Footer -->
<?php include '../components/footer.php'; ?>

<!-- Admin JS -->
<script src="../js/admin_script.js"></script>

</body>
</html>
