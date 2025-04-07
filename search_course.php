<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Courses</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- courses section starts  -->

<section class="courses">

   <h1 class="heading">Search results</h1>

   <div class="box-container">

      <?php
      if(isset($_POST['search_course']) || isset($_POST['search_course_btn'])){
         $search_course = filter_var($_POST['search_course'], FILTER_SANITIZE_STRING);
         
         $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE title LIKE ? AND status = ?");
         $search_param = "%{$search_course}%";
         $select_courses->execute([$search_param, 'active']);
         
         if($select_courses->rowCount() > 0){
            while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
               $course_id = $fetch_course['id'];

               $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
               $select_tutor->execute([$fetch_course['tutor_id']]);
               $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

               // مسارات الصور الآمنة
               $tutor_image = !empty($fetch_tutor['image']) && file_exists('uploaded_files/'.$fetch_tutor['image']) 
                              ? 'uploaded_files/'.$fetch_tutor['image'] 
                              : 'images/default-user.png';

               $course_thumb = !empty($fetch_course['thumb']) && file_exists('uploaded_files/'.$fetch_course['thumb']) 
                               ? 'uploaded_files/'.$fetch_course['thumb'] 
                               : 'images/default-thumb.jpg';
      ?>
      <div class="box">
         <div class="tutor">
            <img src="<?= $tutor_image; ?>" alt="">
            <div>
               <h3><?= htmlspecialchars($fetch_tutor['name']); ?></h3>
               <span><?= htmlspecialchars($fetch_course['date']); ?></span>
            </div>
         </div>
         <img src="<?= $course_thumb; ?>" class="thumb" alt="">
         <h3 class="title"><?= htmlspecialchars($fetch_course['title']); ?></h3>
         <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn">View playlist</a>
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">No courses found matching your search.</p>';
         }
      } else {
         echo '<p class="empty">Please enter a search term.</p>';
      }
      ?>

   </div>

</section>

<!-- courses section ends -->

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
