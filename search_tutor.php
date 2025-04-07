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

<section class="teachers">

   <h1 class="heading">Expert tutors</h1>

   <form action="" method="post" class="search-tutor">
      <input type="text" name="search_tutor" maxlength="100" placeholder="Search tutor..." required>
      <button type="submit" name="search_tutor_btn" class="fas fa-search"></button>
   </form>

   <div class="box-container">

      <?php
      if(isset($_POST['search_tutor']) || isset($_POST['search_tutor_btn'])){
         $search_tutor = filter_var($_POST['search_tutor'], FILTER_SANITIZE_STRING);
         $search_param = "%{$search_tutor}%";

         $select_tutors = $conn->prepare("SELECT * FROM `tutors` WHERE name LIKE ?");
         $select_tutors->execute([$search_param]);

         if($select_tutors->rowCount() > 0){
            while($fetch_tutor = $select_tutors->fetch(PDO::FETCH_ASSOC)){

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
      ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= htmlspecialchars($fetch_tutor['name']); ?></h3>
               <span><?= htmlspecialchars($fetch_tutor['profession']); ?></span>
            </div>
         </div>
         <p>Playlists : <span><?= $total_playlists; ?></span></p>
         <p>Total videos : <span><?= $total_contents ?></span></p>
         <p>Total likes : <span><?= $total_likes ?></span></p>
         <p>Total comments : <span><?= $total_comments ?></span></p>
         <form action="tutor_profile.php" method="post">
            <input type="hidden" name="tutor_email" value="<?= htmlspecialchars($fetch_tutor['email']); ?>">
            <input type="submit" value="View profile" name="tutor_fetch" class="inline-btn">
         </form>
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">No tutors found matching your search.</p>';
         }
      } else {
         echo '<p class="empty">Please enter a tutor name to search.</p>';
      }
      ?>

   </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
