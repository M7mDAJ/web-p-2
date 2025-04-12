<?php

// Include the database connection file
include 'components/connect.php';

// Check if the user_id cookie is set
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

// Check if the tutor_fetch form was submitted
if(isset($_POST['tutor_fetch'])){

   // Sanitize the tutor email input
   $tutor_email = $_POST['tutor_email'];
   $tutor_email = filter_var($tutor_email, FILTER_SANITIZE_STRING);

   // Fetch tutor data from the database using the email
   $select_tutor = $conn->prepare('SELECT * FROM `tutors` WHERE email = ?');
   $select_tutor->execute([$tutor_email]);

   $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
   $tutor_id = $fetch_tutor['id'];

   // Count total playlists created by the tutor
   $count_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
   $count_playlists->execute([$tutor_id]);
   $total_playlists = $count_playlists->rowCount();

   // Count total contents uploaded by the tutor
   $count_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
   $count_contents->execute([$tutor_id]);
   $total_contents = $count_contents->rowCount();

   // Count total likes received by the tutor
   $count_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
   $count_likes->execute([$tutor_id]);
   $total_likes = $count_likes->rowCount();

   // Count total comments on the tutor's content
   $count_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
   $count_comments->execute([$tutor_id]);
   $total_comments = $count_comments->rowCount();

}else{
   // Redirect to teachers page if tutor_fetch is not set
   header('location:teachers.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Tutor's profile</title>

   <!-- font awesome cdn link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link (modified to style1.css as requested) -->
   <link rel="stylesheet" href="css/style1.css">

</head>
<body>

<!-- Include user header -->
<?php include 'components/user_header.php'; ?>

<!-- teachers profile section starts -->
<section class="tutor-profile">

   <h1 class="heading">Profile details</h1>

   <div class="details">
      <div class="tutor">
         <!-- Display tutor image, name and profession -->
         <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
         <h3><?= $fetch_tutor['name']; ?></h3>
         <span><?= $fetch_tutor['profession']; ?></span>
      </div>
      <div class="flex">
         <!-- Display statistics about the tutor -->
         <p>Total playlists : <span><?= $total_playlists; ?></span></p>
         <p>Total videos : <span><?= $total_contents; ?></span></p>
         <p>Total likes : <span><?= $total_likes; ?></span></p>
         <p>Total comments : <span><?= $total_comments; ?></span></p>
      </div>
   </div>

</section>
<!-- teachers profile section ends -->

<!-- tutor's courses section starts -->
<section class="courses">

   <h1 class="heading">Latest courese</h1>

   <div class="box-container">

      <?php
         // Fetch all active playlists by the tutor
         $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ? AND status = ?");
         $select_courses->execute([$tutor_id, 'active']);
         if($select_courses->rowCount() > 0){
            while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
               $course_id = $fetch_course['id'];

               // Fetch tutor info for each course (could be optimized to avoid refetching the same tutor)
               $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
               $select_tutor->execute([$fetch_course['tutor_id']]);
               $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box">
         <div class="tutor">
            <!-- Display course tutor info -->
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3>
               <span><?= $fetch_course['date']; ?></span>
            </div>
         </div>
         <!-- Display course thumbnail and title -->
         <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fetch_course['title']; ?></h3>
         <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn">View playlist</a>
      </div>
      <?php
         }
      }else{
         // Show message if no courses found
         echo '<p class="empty">No courses added yet!</p>';
      }
      ?>

   </div>

</section>
<!-- tutor's courses section ends -->

<!-- Include footer -->
<?php include 'components/footer.php'; ?>    

<!-- custom js file link -->
<script src="js/script.js"></script>
   
</body>
</html>
