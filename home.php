<?php

// Include the database connection
include 'components/connect.php';

// Get the user ID from cookie if available
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

// Get total number of likes by the user
$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
$select_likes->execute([$user_id]);
$total_likes = $select_likes->rowCount();

// Get total number of comments by the user
$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
$select_comments->execute([$user_id]);
$total_comments = $select_comments->rowCount();

// Get total number of bookmarked items by the user
$select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ?");
$select_bookmark->execute([$user_id]);
$total_bookmarked = $select_bookmark->rowCount();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Basic page setup -->
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>

   <!-- Font Awesome for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file -->
   <link rel="stylesheet" href="css/style1.css">

</head>
<body>

<!-- Include the user header -->
<?php include 'components/user_header.php'; ?>

<!-- Quick select section starts -->
<section class="quick-select">

   <h1 class="heading">Quick options</h1>

   <div class="box-container">

      <?php if($user_id != ''){ ?>
      <!-- Show user statistics if logged in -->
      <div class="box">
         <h3 class="title">Likes and comments</h3>
         <p>Total likes : <span><?= $total_likes; ?></span></p>
         <a href="likes.php" class="inline-btn">View likes</a>
         <p>Total comments : <span><?= $total_comments; ?></span></p>
         <a href="comments.php" class="inline-btn">View comments</a>
         <p>Saved playlist : <span><?= $total_bookmarked; ?></span></p>
         <a href="bookmark.php" class="inline-btn">View bookmark</a>
      </div>
      <?php } else { ?>
      <!-- Show login/register prompt if not logged in -->
      <div class="box" style="text-align: center;">
         <h3 class="title">Please login or register</h3>
         <div class="flex-btn" style="padding-top: .5rem;">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>
      </div>
      <?php } ?>

      <!-- Top categories section -->
      <div class="box">
         <h3 class="title">Top categories</h3>
         <div class="flex">
            <a href="search_course.php?"><i class="fas fa-code"></i><span>Development</span></a>
            <a href="#"><i class="fas fa-chart-simple"></i><span>Business</span></a>
            <a href="#"><i class="fas fa-pen"></i><span>Design</span></a>
            <a href="#"><i class="fas fa-chart-line"></i><span>Marketing</span></a>
            <a href="#"><i class="fas fa-music"></i><span>Music</span></a>
            <a href="#"><i class="fas fa-camera"></i><span>Photography</span></a>
            <a href="#"><i class="fas fa-cog"></i><span>Software</span></a>
            <a href="#"><i class="fas fa-vial"></i><span>Science</span></a>
         </div>
      </div>

      <!-- Popular topics section -->
      <div class="box">
         <h3 class="title">Popular topics</h3>
         <div class="flex">
            <a href="#"><i class="fab fa-html5"></i><span>HTML</span></a>
            <a href="#"><i class="fab fa-css3"></i><span>CSS</span></a>
            <a href="#"><i class="fab fa-js"></i><span>JavaScript</span></a>
            <a href="#"><i class="fab fa-react"></i><span>React</span></a>
            <a href="#"><i class="fab fa-php"></i><span>PHP</span></a>
            <a href="#"><i class="fab fa-bootstrap"></i><span>Bootstrap</span></a>
         </div>
      </div>

      <!-- Become a tutor section -->
      <div class="box tutor">
         <h3 class="title">Become a tutor</h3>
         <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsa, laudantium.</p>
         <a href="admin/register.php" class="inline-btn">Get started</a>
      </div>

   </div>

</section>
<!-- Quick select section ends -->

<!-- Courses section starts -->
<section class="courses">

   <h1 class="heading">Latest courses</h1>

   <div class="box-container">

      <?php
         // Fetch latest 6 active courses
         $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE status = ? ORDER BY date DESC LIMIT 6");
         $select_courses->execute(['active']);
         if($select_courses->rowCount() > 0){
            while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
               $course_id = $fetch_course['id'];

               // Get tutor information
               $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
               $select_tutor->execute([$fetch_course['tutor_id']]);
               $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
      ?>
      <!-- Course card -->
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3>
               <span><?= $fetch_course['date']; ?></span>
            </div>
         </div>
         <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fetch_course['title']; ?></h3>
         <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn">View playlist</a>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">No courses added yet!</p>';
      }
      ?>

   </div>

   <!-- Button to view all courses -->
   <div class="more-btn">
      <a href="courses.php" class="inline-option-btn">View more</a>
   </div>

</section>
<!-- Courses section ends -->

<!-- Include the footer -->
<?php include 'components/footer.php'; ?>

<!-- Custom JavaScript -->
<script src="js/script.js"></script>
   
</body>
</html>
