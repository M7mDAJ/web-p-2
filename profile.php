<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('location:login.php');
   exit;
}

// جلب بيانات المستخدم
$select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
$select_likes->execute([$user_id]);
$total_likes = $select_likes->rowCount();

$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
$select_comments->execute([$user_id]);
$total_comments = $select_comments->rowCount();

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

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="profile">

   <h1 class="heading">Profile details</h1>

   <div class="details">

      <div class="user">
         <img src="uploaded_files/<?= htmlspecialchars($fetch_profile['image']); ?>" alt="">
         <h3><?= htmlspecialchars($fetch_profile['name']); ?></h3>
         <p>Student</p>
         <a href="update.php" class="inline-btn">Update profile</a>
      </div>

      <div class="box-container">

         <div class="box">
            <div class="flex">
               <i class="fas fa-bookmark"></i>
               <div>
                  <h3><?= $total_bookmarked; ?></h3>
                  <span>Saved playlists</span>
               </div>
            </div>
            <a href="bookmark.php" class="inline-btn">View playlists</a>
         </div>

         <div class="box">
            <div class="flex">
               <i class="fas fa-heart"></i>
               <div>
                  <h3><?= $total_likes; ?></h3>
                  <span>Liked tutorials</span>
               </div>
            </div>
            <a href="liked.php" class="inline-btn">View liked</a>
         </div>

         <div class="box">
            <div class="flex">
               <i class="fas fa-comment"></i>
               <div>
                  <h3><?= $total_comments; ?></h3>
                  <span>Video comments</span>
               </div>
            </div>
            <a href="comments.php" class="inline-btn">View comments</a>
         </div>

      </div>

   </div>

</section>

<footer class="footer">
   &copy; <?= date("Y"); ?> by <span>Mr. Web Designer</span> | All rights reserved!
</footer>

<script src="js/script.js"></script>

</body>
</html>
