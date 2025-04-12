<?php

include '../components/connect.php'; // Includes the database connection file

// Check if the tutor is logged in by verifying the cookie
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = ''; // If no cookie is set, redirect to login page
   header('location:login.php');
}

// Delete video process
if(isset($_POST['delete_video'])){
   $delete_id = $_POST['video_id']; // Get the video ID to delete
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING); // Sanitize the ID to prevent malicious input

   // Check if the video exists in the database
   $verify_video = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
   $verify_video->execute([$delete_id]);
   if($verify_video->rowCount() > 0){
      // Fetch the thumbnail and video file details
      $delete_video_thumb = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $delete_video_thumb->execute([$delete_id]);
      $fetch_thumb = $delete_video_thumb->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files/'.$fetch_thumb['thumb']); // Delete thumbnail file from the server

      // Fetch the video file details and delete it from the server
      $delete_video = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $delete_video->execute([$delete_id]);
      $fetch_video = $delete_video->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files/'.$fetch_video['video']); // Delete video file from the server

      // Delete related records from other tables (likes and comments)
      $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE content_id = ?");
      $delete_likes->execute([$delete_id]);

      $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE content_id = ?");
      $delete_comments->execute([$delete_id]);

      // Finally, delete the video content itself from the database
      $delete_content = $conn->prepare("DELETE FROM `content` WHERE id = ?");
      $delete_content->execute([$delete_id]);

      // Message for success
      $message[] = 'video deleted!';
   }else{
      // Message if the video doesn't exist or is already deleted
      $message[] = 'video already deleted!';
   }
}

// Delete playlist process
if(isset($_POST['delete_playlist'])){
   $delete_id = $_POST['playlist_id']; // Get the playlist ID to delete
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING); // Sanitize the ID

   // Verify if the playlist exists and belongs to the current tutor
   $verify_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? AND tutor_id = ? LIMIT 1");
   $verify_playlist->execute([$delete_id, $tutor_id]);

   if($verify_playlist->rowCount() > 0){
      // Fetch and delete the playlist thumbnail
      $delete_playlist_thumb = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? LIMIT 1");
      $delete_playlist_thumb->execute([$delete_id]);
      $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files/'.$fetch_thumb['thumb']); // Delete thumbnail file from the server

      // Delete related bookmark records
      $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?");
      $delete_bookmark->execute([$delete_id]);

      // Delete the playlist itself from the database
      $delete_playlist = $conn->prepare("DELETE FROM `playlist` WHERE id = ?");
      $delete_playlist->execute([$delete_id]);

      // Message for success
      $message[] = 'Playlist deleted!';
   }else{
      // Message if the playlist doesn't exist or is already deleted
      $message[] = 'Playlist already deleted!';
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

   <!-- font awesome cdn link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="contents">

   <h1 class="heading">Contents</h1>

   <div class="box-container">

   <?php
      // Check if the user has searched for content
      if(isset($_POST['search']) or isset($_POST['search_btn'])){
         $search = $_POST['search']; // Get the search term
         // Search for videos in the content table that match the search term and belong to the tutor
         $select_videos = $conn->prepare("SELECT * FROM `content` WHERE title LIKE '%{$search}%' AND tutor_id = ? ORDER BY date DESC");
         $select_videos->execute([$tutor_id]);

         if($select_videos->rowCount() > 0){
            // Loop through and display matching videos
            while($fecth_videos = $select_videos->fetch(PDO::FETCH_ASSOC)){ 
               $video_id = $fecth_videos['id'];
   ?>
      <div class="box">
         <div class="flex">
            <div><i class="fas fa-dot-circle" style="<?php if($fecth_videos['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fecth_videos['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fecth_videos['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fecth_videos['date']; ?></span></div>
         </div>
         <img src="../uploaded_files/<?= $fecth_videos['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fecth_videos['title']; ?></h3>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="video_id" value="<?= $video_id; ?>">
            <a href="update_content.php?get_id=<?= $video_id; ?>" class="option-btn">Update</a>
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this video?');" name="delete_video">
         </form>
         <a href="view_content.php?get_id=<?= $video_id; ?>" class="btn">View content</a>
      </div>
   <?php
         }
      }else{
         echo '<p class="empty">no contents found!</p>';
      }
   }else{
      echo '<p class="empty">please search something!</p>';
   }
   ?>

   </div>

</section>

<section class="playlists">

   <h1 class="heading">Playlists</h1>

   <div class="box-container">

      <?php
      // Check if the user has searched for playlists
      if(isset($_POST['search']) or isset($_POST['search_btn'])){
         $search = $_POST['search']; // Get the search term
         // Search for playlists in the playlist table that match the search term and belong to the tutor
         $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE title LIKE '%{$search}%' AND tutor_id = ? ORDER BY date DESC");
         $select_playlist->execute([$tutor_id]);

         if($select_playlist->rowCount() > 0){
            // Loop through and display matching playlists
            while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
               $playlist_id = $fetch_playlist['id'];
               // Count the number of videos in each playlist
               $count_videos = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
               $count_videos->execute([$playlist_id]);
               $total_videos = $count_videos->rowCount();
      ?>
      <div class="box">
         <div class="flex">
            <div><i class="fas fa-circle-dot" style="<?php if($fetch_playlist['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fetch_playlist['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fetch_playlist['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fetch_playlist['date']; ?></span></div>
         </div>
         <div class="thumb">
            <span><?= $total_videos; ?></span>
            <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
         </div>
         <h3 class="title"><?= $fetch_playlist['title']; ?></h3>
         <p class="description"><?= $fetch_playlist['description']; ?></p>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="playlist_id" value="<?= $playlist_id; ?>">
            <a href="update_playlist.php?get_id=<?= $playlist_id; ?>" class="option-btn">update</a>
            <input type="submit" value="delete_playlist" class="delete-btn" onclick="return confirm('delete this playlist?');" name="delete">
         </form>
         <a href="view_playlist.php?get_id=<?= $playlist_id; ?>" class="btn">View playlist</a>
      </div>
      <?php
         } 
      }else{
         echo '<p class="empty">no playlists found!</p>';
      }}else{
         echo '<p class="empty">please search something!</p>';
      }
      ?>

   </div>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

<script>
   // Ensure that descriptions are limited to 100 characters in the playlist section
   document.querySelectorAll('.playlists .box-container .box .description').forEach(content => {
      if(content.innerHTML.length > 100) content.innerHTML = content.innerHTML.slice(0, 100);
   });
</script>

</body>
</html>
