<?php

include '../components/connect.php'; // Includes the database connection file

// Verify if the tutor is logged in using a cookie
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = ''; // If not logged in, redirect to login page
   header('location:login.php');
}

// Verify if the video ID is passed via the URL
if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = ''; // If no video ID is passed, redirect to dashboard
   header('location:dashboard.php');
}

// Update video content process
if(isset($_POST['update'])){
   $video_id = $_POST['video_id']; // Get video ID from form input
   $video_id = filter_var($video_id, FILTER_SANITIZE_STRING); // Sanitize video ID input

   // Get other inputs (status, title, description, playlist) and sanitize them
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);
   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   $playlist = $_POST['playlist'];
   $playlist = filter_var($playlist, FILTER_SANITIZE_STRING);

   // Update the content details (title, description, and status)
   $update_content = $conn->prepare("UPDATE `content` SET title = ?, description = ?, status = ? WHERE id = ?");
   $update_content->execute([$title, $description, $status, $video_id]);

   // If a playlist is selected, update the playlist ID for the video
   if(!empty($playlist)){
      $update_playlist = $conn->prepare("UPDATE `content` SET playlist_id = ? WHERE id = ?");
      $update_playlist->execute([$playlist, $video_id]);
   }

   // Handle thumbnail image update
   $old_thumb = $_POST['old_thumb']; // Store the old thumbnail name
   $old_thumb = filter_var($old_thumb, FILTER_SANITIZE_STRING);
   $thumb = $_FILES['thumb']['name']; // Get new thumbnail image file name
   $thumb = filter_var($thumb, FILTER_SANITIZE_STRING);
   $thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION); // Get the file extension
   $rename_thumb = unique_id().'.'.$thumb_ext; // Generate a new unique file name
   $thumb_size = $_FILES['thumb']['size']; // Get the file size of the uploaded thumbnail
   $thumb_tmp_name = $_FILES['thumb']['tmp_name']; // Get the temporary name of the uploaded file
   $thumb_folder = '../uploaded_files/'.$rename_thumb; // Path to save the thumbnail

   // If a new thumbnail is uploaded, validate and move it to the target folder
   if(!empty($thumb)){
      if($thumb_size > 2000000){ // Check if the image size exceeds the limit
         $message[] = 'image size is too large!';
      }else{
         $update_thumb = $conn->prepare("UPDATE `content` SET thumb = ? WHERE id = ?");
         $update_thumb->execute([$rename_thumb, $video_id]);
         move_uploaded_file($thumb_tmp_name, $thumb_folder); // Move the uploaded thumbnail to the target folder
         if($old_thumb != '' AND $old_thumb != $rename_thumb){
            unlink('../uploaded_files/'.$old_thumb); // Delete the old thumbnail if it’s not the same as the new one
         }
      }
   }

   // Handle video file update
   $old_video = $_POST['old_video']; // Store the old video file name
   $old_video = filter_var($old_video, FILTER_SANITIZE_STRING);
   $video = $_FILES['video']['name']; // Get new video file name
   $video = filter_var($video, FILTER_SANITIZE_STRING);
   $video_ext = pathinfo($video, PATHINFO_EXTENSION); // Get the file extension
   $rename_video = unique_id().'.'.$video_ext; // Generate a new unique file name for the video
   $video_tmp_name = $_FILES['video']['tmp_name']; // Get the temporary name of the uploaded video
   $video_folder = '../uploaded_files/'.$rename_video; // Path to save the video

   // If a new video is uploaded, update the content and move the file to the target folder
   if(!empty($video)){
      $update_video = $conn->prepare("UPDATE `content` SET video = ? WHERE id = ?");
      $update_video->execute([$rename_video, $video_id]);
      move_uploaded_file($video_tmp_name, $video_folder); // Move the uploaded video to the target folder
      if($old_video != '' AND $old_video != $rename_video){
         unlink('../uploaded_files/'.$old_video); // Delete the old video if it’s not the same as the new one
      }
   }

   $message[] = 'content updated!'; // Success message
}

// Delete video process
if(isset($_POST['delete_video'])){
   $delete_id = $_POST['video_id']; // Get the video ID to delete
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING); // Sanitize the ID

   // Delete associated thumbnail and video files from the server
   $delete_video_thumb = $conn->prepare("SELECT thumb FROM `content` WHERE id = ? LIMIT 1");
   $delete_video_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_video_thumb->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files/'.$fetch_thumb['thumb']); // Delete thumbnail

   $delete_video = $conn->prepare("SELECT video FROM `content` WHERE id = ? LIMIT 1");
   $delete_video->execute([$delete_id]);
   $fetch_video = $delete_video->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files/'.$fetch_video['video']); // Delete video

   // Remove related likes and comments for the video
   $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE content_id = ?");
   $delete_likes->execute([$delete_id]);
   $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE content_id = ?");
   $delete_comments->execute([$delete_id]);

   // Finally, delete the video content record from the database
   $delete_content = $conn->prepare("DELETE FROM `content` WHERE id = ?");
   $delete_content->execute([$delete_id]);
   header('location:contents.php'); // Redirect to content management page
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update video</title>

   <!-- Font awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="video-form">

   <h1 class="heading">Update content</h1>

   <?php
      // Select the video based on the video ID passed in the URL
      $select_videos = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND tutor_id = ?");
      $select_videos->execute([$get_id, $tutor_id]);
      if($select_videos->rowCount() > 0){
         while($fecth_videos = $select_videos->fetch(PDO::FETCH_ASSOC)){
            $video_id = $fecth_videos['id']; // Store video ID
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="video_id" value="<?= $fecth_videos['id']; ?>">
      <input type="hidden" name="old_thumb" value="<?= $fecth_videos['thumb']; ?>">
      <input type="hidden" name="old_video" value="<?= $fecth_videos['video']; ?>">
      
      <!-- Video content update form fields -->
      <p>Update status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="<?= $fecth_videos['status']; ?>" selected><?= $fecth_videos['status']; ?></option>
         <option value="active">Active</option>
         <option value="deactive">Deactive</option>
      </select>

      <p>Update title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="Enter video title" class="box" value="<?= $fecth_videos['title']; ?>">

      <p>Update description <span>*</span></p>
      <textarea name="description" class="box" required placeholder="Write description" maxlength="1000" cols="30" rows="10"><?= $fecth_videos['description']; ?></textarea>

      <p>Update playlist</p>
      <select name="playlist" class="box">
         <option value="<?= $fecth_videos['playlist_id']; ?>" selected>--select playlist</option>
         <?php
         // Select playlists created by the current tutor
         $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
         $select_playlists->execute([$tutor_id]);
         if($select_playlists->rowCount() > 0){
            while($fetch_playlist = $select_playlists->fetch(PDO::FETCH_ASSOC)){
         ?>
         <option value="<?= $fetch_playlist['id']; ?>"><?= $fetch_playlist['title']; ?></option>
         <?php
            }
         }else{
            echo '<option value="" disabled>No playlist created yet!</option>';
         }
         ?>
      </select>

      <!-- Thumbnail and video file inputs -->
      <img src="../uploaded_files/<?= $fecth_videos['thumb']; ?>" alt="">
      <p>Update thumbnail</p>
      <input type="file" name="thumb" accept="image/*" class="box">

      <video src="../uploaded_files/<?= $fecth_videos['video']; ?>" controls></video>
      <p>Update video</p>
      <input type="file" name="video" accept="video/*" class="box">

      <input type="submit" value="Update content" name="update" class="btn">
      
      <div class="flex-btn">
         <a href="view_content.php?get_id=<?= $video_id; ?>" class="option-btn">View content</a>
         <input type="submit" value="Delete content" name="delete_video" class="delete-btn">
      </div>
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">Video not found! <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">Add videos</a></p>';
      }
   ?>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
