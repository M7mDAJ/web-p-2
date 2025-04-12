<?php

// Include the database connection file
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

<!-- Teachers section starts -->
<section class="teachers">

   <h1 class="heading">Expert tutors</h1>

   <!-- Search form to search tutors -->
   <form action="" method="post" class="search-tutor">
      <input type="text" name="search_tutor" maxlength="100" placeholder="search tutor..." required>
      <button type="submit" name="search_tutor_btn" class="fas fa-search"></button>
   </form>

   <div class="box-container">

      <?php
         // Check if the search tutor form is submitted
         if(isset($_POST['search_tutor']) or isset($_POST['search_tutor_btn'])){
            $search_tutor = $_POST['search_tutor']; // Get the search term from the input field
            
            // Prepare SQL query to search tutors by name
            $select_tutors = $conn->prepare("SELECT * FROM `tutors` WHERE name LIKE '%{$search_tutor}%'");
            $select_tutors->execute();
            
            // Check if any tutor matches the search query
            if($select_tutors->rowCount() > 0){
               // Loop through each tutor that matches the search
               while($fetch_tutor = $select_tutors->fetch(PDO::FETCH_ASSOC)){

                  $tutor_id = $fetch_tutor['id']; // Get the tutor ID

                  // Get the total number of playlists, contents, likes, and comments for the tutor
                  $count_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
                  $count_playlists->execute([$tutor_id]);
                  $total_playlists = $count_playlists->rowCount();

                  $count_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
                  $count_contents->execute([$tutor_id]);
                  $total_contents = $count_contents->rowCount();

                  $count_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
                  $count_likes->execute([$tutor_id]);
                  $total_likes = $count_likes->rowCount();

                  $count_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
                  $count_comments->execute([$tutor_id]);
                  $total_comments = $count_comments->rowCount();
      ?>
      <div class="box">
         <!-- Tutor information section -->
         <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt=""> <!-- Tutor's image -->
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3> <!-- Tutor's name -->
               <span><?= $fetch_tutor['profession']; ?></span> <!-- Tutor's profession -->
            </div>
         </div>

         <!-- Tutor statistics section -->
         <p>Playlists : <span><?= $total_playlists; ?></span></p>
         <p>Total videos : <span><?= $total_contents ?></span></p>
         <p>Total likes : <span><?= $total_likes ?></span></p>
         <p>Total comments : <span><?= $total_comments ?></span></p>
         
         <!-- Form to view tutor profile -->
         <form action="tutor_profile.php" method="post">
            <input type="hidden" name="tutor_email" value="<?= $fetch_tutor['email']; ?>">
            <input type="submit" value="view profile" name="tutor_fetch" class="inline-btn">
         </form>
      </div>
      <?php
               }
            }else{
               // If no tutor matches the search, show this message
               echo '<p class="empty">No results found!</p>';
            }
         }else{
            // If no search term is provided, show this message
            echo '<p class="empty">Please search something!</p>';
         }
      ?>

   </div>

</section>
<!-- Teachers section ends -->

<?php include 'components/footer.php'; ?> <!-- Including the footer component -->

<!-- Custom JS file link -->
<script src="js/script.js"></script>
   
</body>
</html>
