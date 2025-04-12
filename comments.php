<?php

// Include database connection
include 'components/connect.php';

// Check if user is logged in
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   // Redirect to home if not logged in
   $user_id = '';
   header('location:home.php');
}

// Handle comment deletion
if(isset($_POST['delete_comment'])){

   // Sanitize comment ID
   $delete_id = $_POST['comment_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Check if comment exists
   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
   $verify_comment->execute([$delete_id]);

   if($verify_comment->rowCount() > 0){
      // Delete comment if found
      $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
      $delete_comment->execute([$delete_id]);
      $message[] = 'comment deleted successfully!';
   }else{
      $message[] = 'comment already deleted!';
   }
}

// Handle comment update
if(isset($_POST['update_now'])){

   // Sanitize input
   $update_id = $_POST['update_id'];
   $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);
   $update_box = $_POST['update_box'];
   $update_box = filter_var($update_box, FILTER_SANITIZE_STRING);

   // Check if comment is already the same
   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? AND comment = ? ORDER BY date DESC");
   $verify_comment->execute([$update_id, $update_box]);

   if($verify_comment->rowCount() > 0){
      $message[] = 'comment already added!';
   }else{
      // Update comment content
      $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
      $update_comment->execute([$update_box, $update_id]);
      $message[] = 'comment edited successfully!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Meta tags -->
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User comments</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file -->
   <link rel="stylesheet" href="css/style1.css">

</head>
<body>

<!-- Include user header -->
<?php include 'components/user_header.php'; ?>

<?php
// Display edit form if user clicked "Edit comment"
if(isset($_POST['edit_comment'])){
   $edit_id = $_POST['comment_id'];
   $edit_id = filter_var($edit_id, FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? LIMIT 1");
   $verify_comment->execute([$edit_id]);

   if($verify_comment->rowCount() > 0){
      $fetch_edit_comment = $verify_comment->fetch(PDO::FETCH_ASSOC);
?>
<!-- Edit comment form -->
<section class="edit-comment">
   <h1 class="heading">Edit comment</h1>
   <form action="" method="post">
      <input type="hidden" name="update_id" value="<?= $fetch_edit_comment['id']; ?>">
      <textarea name="update_box" class="box" maxlength="1000" required placeholder="Please enter your comment" cols="30" rows="10"><?= $fetch_edit_comment['comment']; ?></textarea>
      <div class="flex">
         <a href="comments.php" class="inline-option-btn">cancel edit</a>
         <input type="submit" value="update now" name="update_now" class="inline-btn">
      </div>
   </form>
</section>
<?php
   }else{
      $message[] = 'Comment was not found!';
   }
}
?>

<!-- Display all user comments -->
<section class="comments">

   <h1 class="heading">Your comments</h1>

   <div class="show-comments">
      <?php
         // Fetch all comments made by the user
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
         $select_comments->execute([$user_id]);

         if($select_comments->rowCount() > 0){
            while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){
               // Fetch related content info for each comment
               $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ?");
               $select_content->execute([$fetch_comment['content_id']]);
               $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);
      ?>
      <!-- Individual comment card -->
      <div class="box" style="<?php if($fetch_comment['user_id'] == $user_id){echo 'order:-1;';} ?>">
         <div class="content">
            <span><?= $fetch_comment['date']; ?></span>
            <p> - <?= $fetch_content['title']; ?> - </p>
            <a href="watch_video.php?get_id=<?= $fetch_content['id']; ?>">View content</a>
         </div>
         <p class="text"><?= $fetch_comment['comment']; ?></p>

         <?php if($fetch_comment['user_id'] == $user_id){ ?>
         <!-- Buttons to edit/delete comment -->
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
            <button type="submit" name="edit_comment" class="inline-option-btn">Edit comment</button>
            <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm('Delete this comment?');">Delete comment</button>
         </form>
         <?php } ?>
      </div>
      <?php
            }
         }else{
            echo '<p class="empty">No comments added yet!</p>';
         }
      ?>
   </div>

</section>

<!-- Include footer -->
<?php include 'components/footer.php'; ?>

<!-- Custom JS -->
<script src="js/script.js"></script>
   
</body>
</html>
