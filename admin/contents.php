<?php

// Include the database connection
include '../components/connect.php';

// Check for tutor authentication via cookie
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php'); // Redirect if not logged in
}

// Handle video deletion
if(isset($_POST['delete_video'])){

   // Sanitize the incoming video ID
   $delete_id = $_POST['video_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Check if the video exists
   $verify_video = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
   $verify_video->execute([$delete_id]);

   if($verify_video->rowCount() > 0){

      // Fetch and delete the thumbnail image
      $delete_video_thumb = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $delete_video_thumb->execute([$delete_id]);
      $fetch_thumb = $delete_video_thumb->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files/'.$fetch_thumb['thumb']);

      // Fetch and delete the video file
      $delete_video = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $delete_video->execute([$delete_id]);
      $fetch_video = $delete_video->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files/'.$fetch_video['video']);

      // Delete likes related to this content
      $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE content_id = ?");
      $delete_likes->execute([$delete_id]);

      // Delete comments related to this content
      $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE content_id = ?");
      $delete_comments->execute([$delete_id]);

      // Delete the content entry itself
      $delete_content = $conn->prepare("DELETE FROM `content` WHERE id = ?");
      $delete_content->execute([$delete_id]);

      $message[] = 'video deleted!';
   }else{
      $message[] = 'video already deleted!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- Font Awesome for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <!-- Custom CSS -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<!-- Admin header include -->
<?php include '../components/admin_header.php'; ?>

<section class="contents">

   <h1 class="heading">Your contents</h1>

   <div class="box-container">

      <!-- Button to add new content -->
      <div class="box" style="text-align: center;">
         <h3 class="title" style="margin-bottom: .5rem;">Create new content</h3>
         <a href="add_content.php" class="btn">Add content</a>
      </div>

      <?php
         // Fetch all content by the tutor
         $select_videos = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ? ORDER BY date DESC");
         $select_videos->execute([$tutor_id]);

         if($select_videos->rowCount() > 0){
            while($fecth_videos = $select_videos->fetch(PDO::FETCH_ASSOC)){ 
               $video_id = $fecth_videos['id'];
      ?>
         <!-- Display each content -->
         <div class="box">
            <div class="flex">
               <!-- Status and date info -->
               <div>
                  <i class="fas fa-dot-circle" style="<?php if($fecth_videos['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i>
                  <span style="<?php if($fecth_videos['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>">
                     <?= $fecth_videos['status']; ?>
                  </span>
               </div>
               <div>
                  <i class="fas fa-calendar"></i>
                  <span><?= $fecth_videos['date']; ?></span>
               </div>
            </div>

            <!-- Thumbnail -->
            <img src="../uploaded_files/<?= $fecth_videos['thumb']; ?>" class="thumb" alt="">

            <!-- Title -->
            <h3 class="title"><?= $fecth_videos['title']; ?></h3>

            <!-- Update / Delete form -->
            <form action="" method="post" class="flex-btn">
               <input type="hidden" name="video_id" value="<?= $video_id; ?>">
               <a href="update_content.php?get_id=<?= $video_id; ?>" class="option-btn">Update</a>
               <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this video?');" name="delete_video">
            </form>

            <!-- View content link -->
            <a href="view_content.php?get_id=<?= $video_id; ?>" class="btn">View content</a>
         </div>
      <?php
            }
         }else{
            echo '<p class="empty">no contents added yet!</p>';
         }
      ?>

   </div>

</section>

<!-- Footer -->
<?php include '../components/footer.php'; ?>

<!-- Custom admin JS -->
<script src="../js/admin_script.js"></script>

</body>
</html>
