<?php

// Include the database connection file
include '../components/connect.php';

// Check if the tutor is logged in by verifying the tutor_id cookie
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id']; // Get tutor_id from cookie
}else{
   $tutor_id = ''; // If no tutor_id is found, set to empty
   header('location:login.php'); // Redirect to login page
}

// Check if the content ID is passed via GET parameter
if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id']; // Retrieve the content ID
}else{
   $get_id = ''; // If no content ID is passed, set to empty
   header('location:contents.php'); // Redirect to the content list page
}

// Handle video deletion if the delete_video form is submitted
if(isset($_POST['delete_video'])){

   $delete_id = $_POST['video_id']; // Get video ID from the hidden input field
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING); // Sanitize the video ID

   // Fetch the thumbnail associated with the video to delete it
   $delete_video_thumb = $conn->prepare("SELECT thumb FROM `content` WHERE id = ? LIMIT 1");
   $delete_video_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_video_thumb->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files/'.$fetch_thumb['thumb']); // Delete the thumbnail from the server

   // Fetch the video file associated with the content to delete it
   $delete_video = $conn->prepare("SELECT video FROM `content` WHERE id = ? LIMIT 1");
   $delete_video->execute([$delete_id]);
   $fetch_video = $delete_video->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files/'.$fetch_video['video']); // Delete the video from the server

   // Delete the likes associated with the video
   $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE content_id = ?");
   $delete_likes->execute([$delete_id]);

   // Delete the comments associated with the video
   $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE content_id = ?");
   $delete_comments->execute([$delete_id]);

   // Finally, delete the video content from the database
   $delete_content = $conn->prepare("DELETE FROM `content` WHERE id = ?");
   $delete_content->execute([$delete_id]);

   // Redirect back to the content list page after deletion
   header('location:contents.php');
}

// Handle comment deletion if the delete_comment form is submitted
if(isset($_POST['delete_comment'])){

   $delete_id = $_POST['comment_id']; // Get comment ID from the hidden input field
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING); // Sanitize the comment ID

   // Verify if the comment exists in the database
   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
   $verify_comment->execute([$delete_id]);

   // If the comment exists, delete it
   if($verify_comment->rowCount() > 0){
      $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
      $delete_comment->execute([$delete_id]);
      $message[] = 'Comment deleted successfully!'; // Success message
   }else{
      $message[] = 'Comment already deleted!'; // Error message if comment does not exist
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>View content</title>

   <!-- Font Awesome CDN for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file for styling -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<!-- Include header for navigation -->
<?php include '../components/admin_header.php'; ?>


<section class="view-content">

   <?php
      // Fetch the content details from the database
      $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND tutor_id = ?");
      $select_content->execute([$get_id, $tutor_id]);
      
      // If the content exists
      if($select_content->rowCount() > 0){
         while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){
            $video_id = $fetch_content['id'];

            // Count the total likes for this video
            $count_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ? AND content_id = ?");
            $count_likes->execute([$tutor_id, $video_id]);
            $total_likes = $count_likes->rowCount();

            // Count the total comments for this video
            $count_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ? AND content_id = ?");
            $count_comments->execute([$tutor_id, $video_id]);
            $total_comments = $count_comments->rowCount();
   ?>
   <div class="container">
      <!-- Display the video player -->
      <video src="../uploaded_files/<?= $fetch_content['video']; ?>" autoplay controls poster="../uploaded_files/<?= $fetch_content['thumb']; ?>" class="video"></video>
      <!-- Display the content's date -->
      <div class="date"><i class="fas fa-calendar"></i><span><?= $fetch_content['date']; ?></span></div>
      <!-- Display the content title -->
      <h3 class="title"><?= $fetch_content['title']; ?></h3>
      <!-- Display the number of likes and comments -->
      <div class="flex">
         <div><i class="fas fa-heart"></i><span><?= $total_likes; ?></span></div>
         <div><i class="fas fa-comment"></i><span><?= $total_comments; ?></span></div>
      </div>
      <!-- Display the content description -->
      <div class="description"><?= $fetch_content['description']; ?></div>
      <!-- Provide options to update or delete the video -->
      <form action="" method="post">
         <div class="flex-btn">
            <input type="hidden" name="video_id" value="<?= $video_id; ?>">
            <a href="update_content.php?get_id=<?= $video_id; ?>" class="option-btn">Update</a>
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('Delete this video?');" name="delete_video">
         </div>
      </form>
   </div>
   <?php
         }
      }else{
         // If no content is found, display a message
         echo '<p class="empty">No contents added yet! <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">add videos</a></p>';
      }
   ?>

</section>

<section class="comments">

   <h1 class="heading">User comments</h1>

   <!-- Display the comments section -->
   <div class="show-comments">
      <?php
         // Fetch all comments for the current content
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE content_id = ?");
         $select_comments->execute([$get_id]);

         // If there are comments
         if($select_comments->rowCount() > 0){
            while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){   
               // Get the commenter's details
               $select_commentor = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
               $select_commentor->execute([$fetch_comment['user_id']]);
               $fetch_commentor = $select_commentor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box">
         <div class="user">
            <img src="../uploaded_files/<?= $fetch_commentor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_commentor['name']; ?></h3>
               <span><?= $fetch_comment['date']; ?></span>
            </div>
         </div>
         <!-- Display the comment text -->
         <p class="text"><?= $fetch_comment['comment']; ?></p>
         <!-- Option to delete the comment -->
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
            <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm('Delete this comment?');">delete comment</button>
         </form>
      </div>
      <?php
         }
      }else{
         // If no comments exist, display a message
         echo '<p class="empty">No comments added yet!</p>';
      }
      ?>
   </div>
   
</section>

<!-- Include footer for the page -->
<?php include '../components/footer.php'; ?>

<!-- Include custom JavaScript file -->
<script src="../js/admin_script.js"></script>

</body>
</html>
