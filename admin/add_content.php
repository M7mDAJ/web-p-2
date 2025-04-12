<?php

// Including the connection file for database access
include '../components/connect.php';

// Check if tutor_id is set in cookies, otherwise redirect to login page
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];  // Assign tutor_id from cookie
}else{
   $tutor_id = '';  // If no tutor_id found, initialize it as empty
   header('location:login.php');  // Redirect to login page if tutor is not authenticated
}

// Check if form has been submitted
if(isset($_POST['submit'])){

   // Generate a unique ID for the content being uploaded
   $id = unique_id();
   
   // Sanitize and assign form input values
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);
   
   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   
   $playlist = $_POST['playlist'];
   $playlist = filter_var($playlist, FILTER_SANITIZE_STRING);

   // Handle thumbnail image file upload
   $thumb = $_FILES['thumb']['name'];  // Get thumbnail file name
   $thumb = filter_var($thumb, FILTER_SANITIZE_STRING);  // Sanitize thumbnail file name
   $thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION);  // Get file extension of thumbnail
   $rename_thumb = unique_id().'.'.$thumb_ext;  // Generate a unique name for the thumbnail
   $thumb_size = $_FILES['thumb']['size'];  // Get file size of the thumbnail
   $thumb_tmp_name = $_FILES['thumb']['tmp_name'];  // Get temporary path of the thumbnail
   $thumb_folder = '../uploaded_files/'.$rename_thumb;  // Define the destination path for the thumbnail

   // Handle video file upload
   $video = $_FILES['video']['name'];  // Get video file name
   $video = filter_var($video, FILTER_SANITIZE_STRING);  // Sanitize video file name
   $video_ext = pathinfo($video, PATHINFO_EXTENSION);  // Get file extension of video
   $rename_video = unique_id().'.'.$video_ext;  // Generate a unique name for the video
   $video_tmp_name = $_FILES['video']['tmp_name'];  // Get temporary path of the video
   $video_folder = '../uploaded_files/'.$rename_video;  // Define the destination path for the video

   // Check if the thumbnail image size is too large (limit: 2MB)
   if($thumb_size > 2000000){
      $message[] = 'image size is too large!';  // Error message if image size exceeds 2MB
   }else{
      // Prepare and execute SQL query to insert content into the database
      $add_playlist = $conn->prepare("INSERT INTO `content`(id, tutor_id, playlist_id, title, description, video, thumb, status) VALUES(?,?,?,?,?,?,?,?)");
      $add_playlist->execute([$id, $tutor_id, $playlist, $title, $description, $rename_video, $rename_thumb, $status]);
      
      // Move uploaded thumbnail and video files to the designated folders
      move_uploaded_file($thumb_tmp_name, $thumb_folder);
      move_uploaded_file($video_tmp_name, $video_folder);

      // Success message after uploading the video
      $message[] = 'new course uploaded!';
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

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<!-- Include the admin header -->
<?php include '../components/admin_header.php'; ?>

<!-- Section for uploading video content -->
<section class="video-form">

   <h1 class="heading">Upload content</h1>

   <!-- Form to upload new content -->
   <form action="" method="post" enctype="multipart/form-data">
      
      <!-- Video status dropdown -->
      <p>Video status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="" selected disabled>-- Select status</option>
         <option value="active">Active</option>
         <option value="deactive">Deactive</option>
      </select>

      <!-- Video title input -->
      <p>Video title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="enter video title" class="box">

      <!-- Video description textarea -->
      <p>Video description <span>*</span></p>
      <textarea name="description" class="box" required placeholder="write description" maxlength="1000" cols="30" rows="10"></textarea>

      <!-- Video playlist dropdown -->
      <p>Video playlist <span>*</span></p>
      <select name="playlist" class="box" required>
         <option value="" disabled selected>--Select playlist</option>
         <?php
         // Fetch and display playlists associated with the current tutor
         $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
         $select_playlists->execute([$tutor_id]);
         if($select_playlists->rowCount() > 0){
            while($fetch_playlist = $select_playlists->fetch(PDO::FETCH_ASSOC)){
         ?>
         <option value="<?= $fetch_playlist['id']; ?>"><?= $fetch_playlist['title']; ?></option>
         <?php
            }
         ?>
         <?php
         }else{
            echo '<option value="" disabled>no playlist created yet!</option>';  // If no playlists found, display this message
         }
         ?>
      </select>

      <!-- Thumbnail image upload input -->
      <p>Select thumbnail <span>*</span></p>
      <input type="file" name="thumb" accept="image/*" required class="box">

      <!-- Video file upload input -->
      <p>Select video <span>*</span></p>
      <input type="file" name="video" accept="video/*" required class="box">

      <!-- Submit button for form -->
      <input type="submit" value="upload video" name="submit" class="btn">
   </form>

</section>

<!-- Include the footer -->
<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
