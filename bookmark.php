<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Bookmarks</title>

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="courses">

   <h1 class="heading">Bookmarked playlists</h1>

   <div class="box-container">

   <?php
   // Improved query using JOIN
   $select_bookmarked_playlists = $conn->prepare("
      SELECT 
         p.id AS playlist_id, p.title, p.thumb, p.date,
         t.name AS tutor_name, t.image AS tutor_image
      FROM bookmark b
      INNER JOIN playlist p ON b.playlist_id = p.id
      INNER JOIN tutors t ON p.tutor_id = t.id
      WHERE b.user_id = ? AND p.status = 'active'
      ORDER BY p.date DESC
   ");
   $select_bookmarked_playlists->execute([$user_id]);

   if($select_bookmarked_playlists->rowCount() > 0){
      while($playlist = $select_bookmarked_playlists->fetch(PDO::FETCH_ASSOC)){
   ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= htmlspecialchars($playlist['tutor_image']); ?>" alt="">
            <div>
               <h3><?= htmlspecialchars($playlist['tutor_name']); ?></h3>
               <span><?= htmlspecialchars($playlist['date']); ?></span>
            </div>
         </div>
         <img src="uploaded_files/<?= htmlspecialchars($playlist['thumb']); ?>" class="thumb" alt="">
         <h3 class="title"><?= htmlspecialchars($playlist['title']); ?></h3>
         <a href="playlist.php?get_id=<?= htmlspecialchars($playlist['playlist_id']); ?>" class="inline-btn">View playlist</a>
      </div>
   <?php
      }
   } else {
      echo '<p class="empty">Nothing bookmarked yet!</p>';
   }
   ?>

   </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
