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
   <title>Teachers</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- teachers section starts  -->

<section class="teachers">

   <?php
      $count_tutors = $conn->query("SELECT COUNT(*) FROM `tutors`")->fetchColumn();
   ?>
   <h1 class="heading">Expert tutors (<?= $count_tutors; ?>)</h1>

   <form action="search_tutor.php" method="post" class="search-tutor">
      <input type="text" name="search_tutor" maxlength="100" placeholder="search tutor..." required>
      <button type="submit" name="search_tutor_btn" class="fas fa-search"></button>
   </form>

   <div class="box-container">

      <div class="box offer">
         <h3>Become a tutor</h3>
         <p>Join our platform and start sharing your knowledge today!</p>
         <a href="admin/register.php" class="inline-btn">get started</a>
      </div>

      <?php
         $select_tutors = $conn->prepare("SELECT * FROM `tutors`");
         $select_tutors->execute();
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
            <?php if (!empty($fetch_tutor['image']) && file_exists('uploaded_files/' . $fetch_tutor['image'])): ?>
               <img src="uploaded_files/<?= htmlspecialchars($fetch_tutor['image']); ?>" alt="">
            <?php endif; ?>
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
            <input type="submit" value="view profile" name="tutor_fetch" class="inline-btn">
         </form>
      </div>
      <?php
            }
         }else{
            echo '<p class="empty">No tutors found. Why not be the first to join?</p>';
         }
      ?>

   </div>

</section>

<!-- teachers section ends -->

<?php include 'components/footer.php'; ?>    

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
