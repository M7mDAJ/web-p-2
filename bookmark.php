<?php

// Include database connection
include 'components/connect.php';

// Check if user is logged in via cookie
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   // If not logged in, redirect to home page
   $user_id = '';
   header('location:home.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Character encoding -->
   <meta charset="UTF-8">
   <!-- Compatibility with IE -->
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <!-- Responsive viewport -->
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>bookmarks</title>

   <!-- Font Awesome library for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file -->
   <link rel="stylesheet" href="css/style1.css">

</head>
<body>

<!-- Include user header layout -->
<?php include 'components/user_header.php'; ?>

<!-- Bookmarked playlists section -->
<section class="courses">

   <h1 class="heading">Bookmarked playlists</h1>

   <div class="box-container">

      <?php
         // Select bookmarks for the current user
         $select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ?");
         $select_bookmark->execute([$user_id]);

         // If bookmarks exist
         if($select_bookmark->rowCount() > 0){
            while($fetch_bookmark = $select_bookmark->fetch(PDO::FETCH_ASSOC)){
               // Select corresponding course if it's active
               $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? AND status = ? ORDER BY date DESC");
               $select_courses->execute([$fetch_bookmark['playlist_id'], 'active']);

               if($select_courses->rowCount() > 0){
                  while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){

                  $course_id = $fetch_course['id'];

                  // Fetch tutor details
                  $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
                  $select_tutor->execute([$fetch_course['tutor_id']]);
                  $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
      ?>
      <!-- Playlist card -->
      <div class="box">
         <div class="tutor">
            <!-- Tutor image -->
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <!-- Tutor name and course date -->
               <h3><?= $fetch_tutor['name']; ?></h3>
               <span><?= $fetch_course['date']; ?></span>
            </div>
         </div>
         <!-- Playlist thumbnail and title -->
         <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fetch_course['title']; ?></h3>
         <!-- Link to view full playlist -->
         <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn">View playlist</a>
      </div>
      <?php
               }
            }else{
               // If no active courses found for a bookmark
               echo '<p class="empty">no courses found!</p>';
            }
         }
      }else{
         // If user has no bookmarks
         echo '<p class="empty">nothing bookmarked yet!</p>';
      }
      ?>

   </div>

</section>

<!-- Include footer layout -->
<?php include 'components/footer.php'; ?>

<!-- Custom JavaScript file -->
<script src="js/script.js"></script>
   
</body>
</html>
