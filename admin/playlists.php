<?php

// Include the database connection file
include '../components/connect.php';

// Check if the tutor's ID is stored in the cookie
if(isset($_COOKIE['tutor_id'])){
   // Retrieve tutor ID from cookie
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   // If not logged in, redirect to login page
   $tutor_id = '';
   header('location:login.php');
}

// Check if the delete button was pressed
if(isset($_POST['delete'])){
   // Get the playlist ID from the form and sanitize it
   $delete_id = $_POST['playlist_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Verify if the playlist belongs to the logged-in tutor and exists in the database
   $verify_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? AND tutor_id = ? LIMIT 1");
   $verify_playlist->execute([$delete_id, $tutor_id]);

   if($verify_playlist->rowCount() > 0){
      // If the playlist is found, fetch its thumbnail
      $delete_playlist_thumb = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? LIMIT 1");
      $delete_playlist_thumb->execute([$delete_id]);
      $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);
      
      // Delete the playlist thumbnail from the server
      unlink('../uploaded_files/'.$fetch_thumb['thumb']);

      // Delete any bookmarks associated with the playlist
      $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?");
      $delete_bookmark->execute([$delete_id]);

      // Delete the playlist itself from the database
      $delete_playlist = $conn->prepare("DELETE FROM `playlist` WHERE id = ?");
      $delete_playlist->execute([$delete_id]);

      // Show a success message
      $message[] = 'playlist deleted!';
   }else{
      // If the playlist is not found or already deleted, show a message
      $message[] = 'playlist already deleted!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Playlists</title>

   <!-- Font Awesome CDN link for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS link for styling -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<!-- Include the header for the admin dashboard -->
<?php include '../components/admin_header.php'; ?>

<section class="playlists">

   <h1 class="heading">Added playlists</h1>

   <div class="box-container">

      <!-- Button to create a new playlist -->
      <div class="box" style="text-align: center;">
         <h3 class="title" style="margin-bottom: .5rem;">Create new playlist</h3>
         <a href="add_playlist.php" class="btn">Add playlist</a>
      </div>

      <?php
         // Query to fetch all playlists added by the logged-in tutor
         $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ? ORDER BY date DESC");
         $select_playlist->execute([$tutor_id]);
         
         // Check if the tutor has any playlists
         if($select_playlist->rowCount() > 0){
            // Loop through each playlist and display its information
            while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
               $playlist_id = $fetch_playlist['id'];
               
               // Count the number of videos in this playlist
               $count_videos = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
               $count_videos->execute([$playlist_id]);
               $total_videos = $count_videos->rowCount();
      ?>
      <!-- Display each playlist -->
      <div class="box">
         <div class="flex">
            <!-- Display playlist status with color-coding (active or inactive) -->
            <div><i class="fas fa-circle-dot" style="<?php if($fetch_playlist['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fetch_playlist['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fetch_playlist['status']; ?></span></div>
            <!-- Display the playlist creation date -->
            <div><i class="fas fa-calendar"></i><span><?= $fetch_playlist['date']; ?></span></div>
         </div>
         <div class="thumb">
            <!-- Display the total number of videos in the playlist -->
            <span><?= $total_videos; ?></span>
            <!-- Display the playlist thumbnail -->
            <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
         </div>
         <!-- Display playlist title -->
         <h3 class="title"><?= $fetch_playlist['title']; ?></h3>
         <!-- Display playlist description -->
         <p class="description"><?= $fetch_playlist['description']; ?></p>
         <form action="" method="post" class="flex-btn">
            <!-- Hidden input for playlist ID -->
            <input type="hidden" name="playlist_id" value="<?= $playlist_id; ?>">
            <!-- Link to update the playlist -->
            <a href="update_playlist.php?get_id=<?= $playlist_id; ?>" class="option-btn">Update</a>
            <!-- Button to delete the playlist -->
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this playlist?');" name="delete">
         </form>
         <!-- Link to view the playlist -->
         <a href="view_playlist.php?get_id=<?= $playlist_id; ?>" class="btn">View playlist</a>
      </div>
      <?php
         } 
      }else{
         // Show message if no playlists are found
         echo '<p class="empty">no playlist added yet!</p>';
      }
      ?>

   </div>

</section>

<!-- Include the footer -->
<?php include '../components/footer.php'; ?>

<!-- Custom script for truncating descriptions if they exceed 100 characters -->
<script>
   document.querySelectorAll('.playlists .box-container .box .description').forEach(content => {
      if(content.innerHTML.length > 100) content.innerHTML = content.innerHTML.slice(0, 100);
   });
</script>

</body>
</html>
