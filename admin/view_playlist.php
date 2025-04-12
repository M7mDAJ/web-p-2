<?php

// Including the database connection
include '../components/connect.php';

// Checking if the tutor ID is stored in a cookie, if not, redirect to login
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php'); // Redirect to login if not set
}

// Checking if there's a valid content ID passed via URL, otherwise redirect to playlist
if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:playlist.php'); // Redirect to playlist if no content ID
}

// Deleting a playlist if the delete button is pressed
if(isset($_POST['delete_playlist'])){
   $delete_id = $_POST['playlist_id'];  // Get the playlist ID from the POST data
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING); // Sanitizing the input

   // Retrieve playlist details including thumbnail
   $delete_playlist_thumb = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? LIMIT 1");
   $delete_playlist_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);

   // Deleting the thumbnail from the server
   unlink('../uploaded_files/'.$fetch_thumb['thumb']);

   // Deleting associated bookmarks for this playlist
   $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?");
   $delete_bookmark->execute([$delete_id]);

   // Finally, deleting the playlist itself from the database
   $delete_playlist = $conn->prepare("DELETE FROM `playlist` WHERE id = ?");
   $delete_playlist->execute([$delete_id]);

   header('location:playlists.php'); // Redirect to playlists page after deletion
}

// Deleting a video if the delete button is pressed
if(isset($_POST['delete_video'])){
   $delete_id = $_POST['video_id'];  // Get the video ID from the POST data
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING); // Sanitizing the input

   // Verifying if the video exists in the database
   $verify_video = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
   $verify_video->execute([$delete_id]);

   if($verify_video->rowCount() > 0){
      // Retrieve video details including thumbnail
      $delete_video_thumb = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $delete_video_thumb->execute([$delete_id]);
      $fetch_thumb = $delete_video_thumb->fetch(PDO::FETCH_ASSOC);

      // Delete the video thumbnail from the server
      unlink('../uploaded_files/'.$fetch_thumb['thumb']);

      // Retrieve video file name and delete it
      $delete_video = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $delete_video->execute([$delete_id]);
      $fetch_video = $delete_video->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files/'.$fetch_video['video']);

      // Deleting associated likes, comments, and the video record itself
      $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE content_id = ?");
      $delete_likes->execute([$delete_id]);

      $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE content_id = ?");
      $delete_comments->execute([$delete_id]);

      $delete_content = $conn->prepare("DELETE FROM `content` WHERE id = ?");
      $delete_content->execute([$delete_id]);

      $message[] = 'Video deleted!';  // Success message
   }else{
      $message[] = 'Video already deleted!';  // Error message if the video doesn't exist
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Playlist Details</title>

   <!-- Font Awesome for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>  <!-- Including header -->

<section class="playlist-details">

   <h1 class="heading">Playlist details</h1>

   <?php
      // Fetch playlist details for the specific tutor
      $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? AND tutor_id = ?");
      $select_playlist->execute([$get_id, $tutor_id]);

      // Checking if the playlist exists
      if($select_playlist->rowCount() > 0){
         while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
            $playlist_id = $fetch_playlist['id'];
            // Fetching the number of videos in the playlist
            $count_videos = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
            $count_videos->execute([$playlist_id]);
            $total_videos = $count_videos->rowCount();
   ?>
   <div class="row">
      <div class="thumb">
         <span><?= $total_videos; ?></span> <!-- Displaying number of videos -->
         <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="Playlist Thumbnail">
      </div>
      <div class="details">
         <h3 class="title"><?= $fetch_playlist['title']; ?></h3>
         <div class="date"><i class="fas fa-calendar"></i><span><?= $fetch_playlist['date']; ?></span></div>
         <div class="description"><?= $fetch_playlist['description']; ?></div>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="playlist_id" value="<?= $playlist_id; ?>">
            <a href="update_playlist.php?get_id=<?= $playlist_id; ?>" class="option-btn">Update playlist</a>
            <input type="submit" value="delete playlist" class="delete-btn" onclick="return confirm('Delete this playlist?');" name="delete_playlist">
         </form>
      </div>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">No playlist found!</p>'; // Message if no playlist is found
      }
   ?>

</section>

<section class="contents">

   <h1 class="heading">Playlist videos</h1>

   <div class="box-container">

   <?php
      // Fetching the videos for the current playlist
      $select_videos = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ? AND playlist_id = ?");
      $select_videos->execute([$tutor_id, $playlist_id]);

      // If there are videos in the playlist, display them
      if($select_videos->rowCount() > 0){
         while($fecth_videos = $select_videos->fetch(PDO::FETCH_ASSOC)){ 
            $video_id = $fecth_videos['id'];
   ?>
      <div class="box">
         <div class="flex">
            <div><i class="fas fa-dot-circle" style="<?php if($fecth_videos['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fecth_videos['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fecth_videos['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fecth_videos['date']; ?></span></div>
         </div>
         <img src="../uploaded_files/<?= $fecth_videos['thumb']; ?>" class="thumb" alt="Video Thumbnail">
         <h3 class="title"><?= $fecth_videos['title']; ?></h3>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="video_id" value="<?= $video_id; ?>">
            <a href="update_content.php?get_id=<?= $video_id; ?>" class="option-btn">Update</a>
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this video?');" name="delete_video">
         </form>
         <a href="view_content.php?get_id=<?= $video_id; ?>" class="btn">Watch video</a>
      </div>
   <?php
         }
      }else{
         echo '<p class="empty">No videos added yet! <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">add videos</a></p>'; // Message if no videos
      }
   ?>

   </div>

</section>

<?php include '../components/footer.php'; ?>  <!-- Including footer -->

<script src="../js/admin_script.js"></script> <!-- Including custom JavaScript -->

</body>
</html>
