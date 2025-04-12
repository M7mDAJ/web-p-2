<?php

// Include the connection to the database
include '../components/connect.php';

// Check if the tutor_id cookie is set, indicating the tutor is logged in
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id']; // Set the tutor_id from the cookie
}else{
   $tutor_id = ''; // If no tutor_id is found, set it to empty
   header('location:login.php'); // Redirect to login page
}

// Retrieve the playlist ID from the URL, if set
if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id']; // Get the playlist ID from the URL parameter
}else{
   $get_id = ''; // If no ID is provided, set it to empty
   header('location:playlist.php'); // Redirect to the playlist page if no ID
}

// Check if the form is submitted
if(isset($_POST['submit'])){

   // Retrieve and sanitize the playlist title, description, and status
   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);

   // Update the playlist in the database with the new data
   $update_playlist = $conn->prepare("UPDATE `playlist` SET title = ?, description = ?, status = ? WHERE id = ?");
   $update_playlist->execute([$title, $description, $status, $get_id]);

   // Handle the playlist thumbnail upload
   $old_image = $_POST['old_image'];
   $old_image = filter_var($old_image, FILTER_SANITIZE_STRING); // Sanitize old image filename
   $image = $_FILES['image']['name']; // Get the name of the uploaded image
   $image = filter_var($image, FILTER_SANITIZE_STRING); // Sanitize the image filename
   $ext = pathinfo($image, PATHINFO_EXTENSION); // Get the file extension
   $rename = unique_id().'.'.$ext; // Generate a unique name for the image
   $image_size = $_FILES['image']['size']; // Get the image size
   $image_tmp_name = $_FILES['image']['tmp_name']; // Get the temporary file name
   $image_folder = '../uploaded_files/'.$rename; // Define the folder where the image will be uploaded

   // If an image is uploaded
   if(!empty($image)){
      if($image_size > 2000000){ // Check if the image size is too large
         $message[] = 'image size is too large!'; // Display error message if too large
      }else{
         // Update the playlist thumbnail in the database
         $update_image = $conn->prepare("UPDATE `playlist` SET thumb = ? WHERE id = ?");
         $update_image->execute([$rename, $get_id]);
         // Move the uploaded image to the specified folder
         move_uploaded_file($image_tmp_name, $image_folder);
         // Delete the old image if it's not the same as the new one
         if($old_image != '' AND $old_image != $rename){
            unlink('../uploaded_files/'.$old_image); // Delete the old image file
         }
      }
   } 

   $message[] = 'playlist updated!';  // Display success message
}

// Check if the delete button is pressed
if(isset($_POST['delete'])){
   $delete_id = $_POST['playlist_id']; // Get the playlist ID to delete
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING); // Sanitize the playlist ID
   // Prepare a query to fetch the current thumbnail of the playlist
   $delete_playlist_thumb = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? LIMIT 1");
   $delete_playlist_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files/'.$fetch_thumb['thumb']); // Delete the playlist thumbnail

   // Delete related bookmarks for the playlist
   $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?");
   $delete_bookmark->execute([$delete_id]);

   // Finally, delete the playlist from the database
   $delete_playlist = $conn->prepare("DELETE FROM `playlist` WHERE id = ?");
   $delete_playlist->execute([$delete_id]);
   header('location:playlists.php'); // Redirect to the playlists page
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Playlist</title>

   <!-- Font Awesome CDN link for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="playlist-form">

   <h1 class="heading">Update playlist</h1>

   <?php
         // Fetch the playlist data based on the playlist ID
         $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ?");
         $select_playlist->execute([$get_id]);
         if($select_playlist->rowCount() > 0){
         while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
            $playlist_id = $fetch_playlist['id'];
            // Count how many videos are in the playlist
            $count_videos = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
            $count_videos->execute([$playlist_id]);
            $total_videos = $count_videos->rowCount();
      ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= $fetch_playlist['thumb']; ?>">
      <p>Playlist status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="<?= $fetch_playlist['status']; ?>" selected><?= $fetch_playlist['status']; ?></option>
         <option value="active">Active</option>
         <option value="deactive">Deactive</option>
      </select>
      <p>Playlist title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="enter playlist title" value="<?= $fetch_playlist['title']; ?>" class="box">
      <p>Playlist description <span>*</span></p>
      <textarea name="description" class="box" required placeholder="write description" maxlength="1000" cols="30" rows="10"><?= $fetch_playlist['description']; ?></textarea>
      <p>Playlist thumbnail <span>*</span></p>
      <div class="thumb">
         <span><?= $total_videos; ?></span>
         <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
      </div>
      <input type="file" name="image" accept="image/*" class="box">
      <input type="submit" value="update playlist" name="submit" class="btn">
      <div class="flex-btn">
         <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this playlist?');" name="delete">
         <a href="view_playlist.php?get_id=<?= $playlist_id; ?>" class="option-btn">View playlist</a>
      </div>
   </form>
   <?php
      } 
   }else{
      echo '<p class="empty">No playlist added yet!</p>';
   }
   ?>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
