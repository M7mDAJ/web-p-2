<?php

// Include the database connection
include '../components/connect.php';

// Check if tutor is logged in using a cookie; otherwise redirect to login page
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

// Handle comment deletion when the form is submitted
if(isset($_POST['delete_comment'])){

   // Get and sanitize the comment ID from the form
   $delete_id = $_POST['comment_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Verify if the comment exists in the database
   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
   $verify_comment->execute([$delete_id]);

   if($verify_comment->rowCount() > 0){
      // Delete the comment if found
      $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
      $delete_comment->execute([$delete_id]);
      $message[] = 'comment deleted successfully!';
   }else{
      // Inform that the comment is already deleted
      $message[] = 'comment already deleted!';
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

   <!-- Font Awesome CDN link for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<!-- Include the admin header -->
<?php include '../components/admin_header.php'; ?>
   

<section class="comments">

   <h1 class="heading">User comments</h1>

   <div class="show-comments">
      <?php
         // Fetch all comments associated with this tutor
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
         $select_comments->execute([$tutor_id]);
         
         if($select_comments->rowCount() > 0){
            while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){
               // Fetch the content associated with the comment
               $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ?");
               $select_content->execute([$fetch_comment['content_id']]);
               $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);
      ?>
      <!-- Display each comment -->
      <div class="box" style="<?php if($fetch_comment['tutor_id'] == $tutor_id){echo 'order:-1;';} ?>">
         <div class="content">
            <span><?= $fetch_comment['date']; ?></span>
            <p> - <?= $fetch_content['title']; ?> - </p>
            <a href="view_content.php?get_id=<?= $fetch_content['id']; ?>">View content</a>
         </div>
         <p class="text"><?= $fetch_comment['comment']; ?></p>
         
         <!-- Form to delete a comment -->
         <form action="" method="post">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
            <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm('delete this comment?');">Delete comment</button>
         </form>
      </div>
      <?php
            }
         }else{
            // Display message when no comments exist
            echo '<p class="empty">no comments added yet!</p>';
         }
      ?>
   </div>
   
</section>

<!-- Include the footer -->
<?php include '../components/footer.php'; ?>

<!-- Admin script -->
<script src="../js/admin_script.js"></script>

</body>
</html>
