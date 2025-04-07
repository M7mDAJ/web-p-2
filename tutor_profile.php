<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

if(isset($_POST['tutor_fetch'])){

   $tutor_email = filter_var($_POST['tutor_email'], FILTER_SANITIZE_STRING);
   $select_tutor = $conn->prepare('SELECT * FROM `tutors` WHERE email = ?');
   $select_tutor->execute([$tutor_email]);

   $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
   $tutor_id = $fetch_tutor['id'];

   $count_playlists = $conn->prepare("SELECT COUNT(*) FROM `playlist` WHERE tutor_id = ?");
   $count_playlists->execute([$tutor_id]);
   $total_playlists = $count_playlists->fetchColumn();

   $count_contents = $conn->prepare("SELECT COUNT(*) FROM `content` WHERE tutor_id = ?");
   $count_contents->execute([$tutor_id]);
   $total_contents = $count_contents->fetchColumn();

   $count_likes = $conn->prepare("SELECT COUNT(*) FROM `likes` WHERE tutor_id = ?");
   $count_likes->execute([$tutor_id]);
   $total_likes = $count_likes->fetchColumn();

   $count_comments = $conn->prepare("SELECT COUNT(*) FROM `comments` WHERE tutor_id = ?");
   $count_comments->execute([$tutor_id]);
   $total_comments = $count_comments->fetchColumn();

}else{
   header('location:teachers.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Tutor's profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- teachers profile section starts  -->

<section class="tutor-profile">

   <h1 class="heading">Profile Details</h1>

   <div class="details">
      <div class="tutor">
         <img src="uploaded_files/<?= (!empty($fetch_tutor['image']) && file_exists('uploaded_files/' . $fetch_tutor['image'])) ? htmlspecialchars($fetch_tutor['image']) : 'default-user.png'; ?>" alt="">
         <h3><?= htmlspecialchars($fetch_tutor['name']); ?></h3>
         <span><?= htmlspecialchars($fetch_tutor['profession']); ?></span>
      </div>
      <div class="flex">
         <p>Total playlists : <span><?= $total_playlists; ?></span></p>
         <p>Total videos : <span><?= $total_contents; ?></span></p>
         <p>Total likes : <span><?= $total_likes; ?></span></p>
         <p>Total comments : <span><?= $total_comments; ?></span></p>
      </div>
   </div>

</section>

<!-- teachers profile section ends -->

<section class="courses">

   <h1 class="heading">Latest Courses</h1>

   <div class="box-container">

      <?php
         $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ? AND status = ?");
         $select_courses->execute([$tutor_id, 'active']);

         if($select_courses->rowCount() > 0){
            while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
               $course_id = $fetch_course['id'];
      ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= (!empty($fetch_tutor['image']) && file_exists('uploaded_files/' . $fetch_tutor['image'])) ? htmlspecialchars($fetch_tutor['image']) : 'default-user.png'; ?>" alt="">
            <div>
               <h3><?= htmlspecialchars($fetch_tutor['name']); ?></h3>
               <span><?= htmlspecialchars($fetch_course['date']); ?></span>
            </div>
         </div>
         <img src="uploaded_files/<?= htmlspecialchars($fetch_course['thumb']); ?>" class="thumb" alt="">
         <h3 class="title"><?= htmlspecialchars($fetch_course['title']); ?></h3>
         <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn">View Playlist</a>
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">No courses added yet!</p>';
         }
      ?>

   </div>

</section>

<!-- courses section ends -->

<?php include 'components/footer.php'; ?>    

<script src="js/script.js"></script>

</body>
</html>
