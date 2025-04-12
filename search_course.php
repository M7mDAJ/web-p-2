<?php

// Including the database connection file
include 'components/connect.php';

// Check if the user is logged in using cookies
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id']; // Retrieve user ID from cookie if available
}else{
   $user_id = ''; // Set user_id as empty if cookie is not set
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Courses</title>

   <!-- Font Awesome CDN link for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file link (changed to style1.css) -->
   <link rel="stylesheet" href="css/style1.css">

</head>
<body>

<?php include 'components/user_header.php'; ?> <!-- Including the header component -->

<!-- Courses section starts -->
<section class="courses">

   <h1 class="heading">Search results</h1>

   <div class="box-container">

      <?php
         // Check if the search course form is submitted
         if(isset($_POST['search_course']) or isset($_POST['search_course_btn'])){
         
         $search_course = $_POST['search_course']; // Get the search term from the input field
         
         // Prepare SQL query to search courses that match the search term and have active status
         $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE title LIKE '%{$search_course}%' AND status = ?");
         $select_courses->execute(['active']);
         
         // Check if any course matches the search query
         if($select_courses->rowCount() > 0){
            // Loop through each course that matches the search
            while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
               $course_id = $fetch_course['id']; // Get the course ID

               // Get the tutor's information for the current course
               $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
               $select_tutor->execute([$fetch_course['tutor_id']]);
               $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box">
         <!-- Tutor information section -->
         <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt=""> <!-- Tutor's image -->
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3> <!-- Tutor's name -->
               <span><?= $fetch_course['date']; ?></span> <!-- Course creation date -->
            </div>
         </div>

         <!-- Course thumbnail and title -->
         <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fetch_course['title']; ?></h3>

         <!-- Link to view the playlist -->
         <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn">view playlist</a>
      </div>
      <?php
         }
      }else{
         // If no course is found, show the message
         echo '<p class="empty">No courses found!</p>';
      }
      }else{
         // If no search term is provided, show this message
         echo '<p class="empty">Please search something!</p>';
      }
      ?>

   </div>

</section>
<!-- Courses section ends -->

<?php include 'components/footer.php'; ?> <!-- Including the footer component -->

<!-- Custom JS file link -->
<script src="js/script.js"></script>
   
</body>
</html>
