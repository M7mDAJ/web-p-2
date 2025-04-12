<?php

// Include the database connection file
include '../components/connect.php';

// Check if tutor_id is set in cookies, otherwise redirect to login page
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];  // Get tutor_id from cookie
}else{
   $tutor_id = '';  // Initialize tutor_id as empty if not found
   header('location:login.php');  // Redirect to login page if no tutor_id found
}

// Check if the form has been submitted
if(isset($_POST['submit'])){

   // Generate a unique ID for the new playlist
   $id = unique_id();
   
   // Sanitize and assign form input values
   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);  // Sanitize title
   
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);  // Sanitize description
   
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);  // Sanitize status

   // Handle the thumbnail image upload
   $image = $_FILES['image']['name'];  // Get the image file name
   $image = filter_var($image, FILTER_SANITIZE_STRING);  // Sanitize the image name
   $ext = pathinfo($image, PATHINFO_EXTENSION);  // Get file extension of the image
   $rename = unique_id().'.'.$ext;  // Generate a unique name for the image
   $image_size = $_FILES['image']['size'];  // Get the image file size
   $image_tmp_name = $_FILES['image']['tmp_name'];  // Get the temporary path of the image
   $image_folder = '../uploaded_files/'.$rename;  // Define the destination path for the image

   // Prepare SQL query to insert the new playlist into the database
   $add_playlist = $conn->prepare("INSERT INTO `playlist`(id, tutor_id, title, description, thumb, status) VALUES(?,?,?,?,?,?)");
   $add_playlist->execute([$id, $tutor_id, $title, $description, $rename, $status]);
   
   // Move the uploaded image to the target folder
   move_uploaded_file($image_tmp_name, $image_folder);

   // Success message after playlist is created
   $message[] = 'new playlist created!';  

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Add Playlist</title>

   <!-- Font Awesome CDN link for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<!-- Include the admin header -->
<?php include '../components/admin_header.php'; ?>
   
<!-- Section for creating a new playlist -->
<section class="playlist-form">

   <h1 class="heading">Create playlist</h1>

   <!-- Form for submitting playlist information -->
   <form action="" method="post" enctype="multipart/form-data">
      
      <!-- Playlist status dropdown -->
      <p>Playlist status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="" selected disabled>-- Select status</option>
         <option value="active">Active</option>
         <option value="deactive">Deactive</option>
      </select>

      <!-- Playlist title input -->
      <p>Playlist title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="Enter playlist title" class="box">

      <!-- Playlist description textarea -->
      <p>Playlist description <span>*</span></p>
      <textarea name="description" class="box" required placeholder="Write description" maxlength="1000" cols="30" rows="10"></textarea>

      <!-- Thumbnail image upload input -->
      <p>Playlist thumbnail <span>*</span></p>
      <input type="file" name="image" accept="image/*" required class="box">

      <!-- Submit button for creating playlist -->
      <input type="submit" value="Create Playlist" name="submit" class="btn">
   </form>

</section>

<!-- Include the footer -->
<?php include '../components/footer.php'; ?>

<!-- Custom script for admin -->
<script src="../js/admin_script.js"></script>

</body>
</html>
