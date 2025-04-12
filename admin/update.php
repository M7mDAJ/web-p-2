<?php

// Include the database connection file
include '../components/connect.php';

// Check if the tutor_id cookie is set, meaning the tutor is logged in
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id']; // Get the tutor_id from the cookie
}else{
   $tutor_id = ''; // If no tutor_id is found, set it to empty
   header('location:login.php'); // Redirect to login page if not logged in
}

// Check if the form is submitted
if(isset($_POST['submit'])){

   // Fetch tutor details from the database using the tutor_id
   $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
   $select_tutor->execute([$tutor_id]);
   $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

   // Store previous password and image for comparison or deletion later
   $prev_pass = $fetch_tutor['password'];
   $prev_image = $fetch_tutor['image'];

   // Get the submitted form data and sanitize it
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $profession = $_POST['profession'];
   $profession = filter_var($profession, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   // If the name is not empty, update the tutor's name in the database
   if(!empty($name)){
      $update_name = $conn->prepare("UPDATE `tutors` SET name = ? WHERE id = ?");
      $update_name->execute([$name, $tutor_id]);
      $message[] = 'Username updated successfully!'; // Success message
   }

   // If the profession is not empty, update the tutor's profession in the database
   if(!empty($profession)){
      $update_profession = $conn->prepare("UPDATE `tutors` SET profession = ? WHERE id = ?");
      $update_profession->execute([$profession, $tutor_id]);
      $message[] = 'Profession updated successfully!'; // Success message
   }

   // If the email is not empty, check if the email already exists in the database
   if(!empty($email)){
      // Check if the new email is already taken by the tutor with the same ID
      $select_email = $conn->prepare("SELECT email FROM `tutors` WHERE id = ? AND email = ?");
      $select_email->execute([$tutor_id, $email]);
      if($select_email->rowCount() > 0){
         $message[] = 'Email already taken!'; // Error message if email exists
      }else{
         // Update the email if it is not taken
         $update_email = $conn->prepare("UPDATE `tutors` SET email = ? WHERE id = ?");
         $update_email->execute([$email, $tutor_id]);
         $message[] = 'Email updated successfully!'; // Success message
      }
   }

   // Handle the uploaded image
   $image = $_FILES['image']['name']; // Get the image name
   $image = filter_var($image, FILTER_SANITIZE_STRING); // Sanitize the image filename
   $ext = pathinfo($image, PATHINFO_EXTENSION); // Get the file extension
   $rename = unique_id().'.'.$ext; // Generate a unique name for the image
   $image_size = $_FILES['image']['size']; // Get the image size
   $image_tmp_name = $_FILES['image']['tmp_name']; // Get the temporary image filename
   $image_folder = '../uploaded_files/'.$rename; // Define the folder to store the image

   // If an image is uploaded
   if(!empty($image)){
      if($image_size > 2000000){ // Check if the image size exceeds the limit (2MB)
         $message[] = 'Image size too large!'; // Error message
      }else{
         // Update the tutor's image in the database
         $update_image = $conn->prepare("UPDATE `tutors` SET `image` = ? WHERE id = ?");
         $update_image->execute([$rename, $tutor_id]);
         move_uploaded_file($image_tmp_name, $image_folder); // Move the image to the desired folder
         if($prev_image != '' AND $prev_image != $rename){
            unlink('../uploaded_files/'.$prev_image); // Delete the old image if it's different from the new one
         }
         $message[] = 'Image updated successfully!'; // Success message
      }
   }

   // Password update section
   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709'; // Empty password hash value (SHA1 of an empty string)
   $old_pass = sha1($_POST['old_pass']); // Hash the old password entered by the user
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING); // Sanitize the old password
   $new_pass = sha1($_POST['new_pass']); // Hash the new password entered by the user
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING); // Sanitize the new password
   $cpass = sha1($_POST['cpass']); // Hash the confirmation password entered by the user
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING); // Sanitize the confirmation password

   // If the old password is not empty (i.e., the tutor wants to change the password)
   if($old_pass != $empty_pass){
      if($old_pass != $prev_pass){ // Check if the old password matches the current one
         $message[] = 'Current password not matched!'; // Error message
      }elseif($new_pass != $cpass){ // Check if the new password and confirmation match
         $message[] = 'Confirm password not matched!'; // Error message
      }else{
         // If the new password is not empty, update the password in the database
         if($new_pass != $empty_pass){
            $update_pass = $conn->prepare("UPDATE `tutors` SET password = ? WHERE id = ?");
            $update_pass->execute([$cpass, $tutor_id]);
            $message[] = 'Password updated successfully!'; // Success message
         }else{
            $message[] = 'Please enter a new password!'; // Error message if no new password is entered
         }
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Profile</title>

   <!-- Font Awesome CDN link for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<!-- Profile update section -->
<section class="form-container" style="min-height: calc(100vh - 19rem);">

   <!-- Profile update form -->
   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3>Update profile</h3>
      <div class="flex">
         <div class="col">
            <p>Full name</p>
            <input type="text" name="name" placeholder="<?= $fetch_tutor['name']; ?>" maxlength="50" class="box">
            <p>Your profession</p>
            <select name="profession" class="box">
               <option value="" selected><?= $fetch_tutor['profession']; ?></option>
               <option value="developer">Developer</option>
               <option value="designer">Designer</option>
               <option value="musician">Musician</option>
               <option value="biologist">Biologist</option>
               <option value="teacher">Teacher</option>
               <option value="engineer">Engineer</option>
               <option value="lawyer">Lawyer</option>
               <option value="accountant">Accountant</option>
               <option value="doctor">Doctor</option>
               <option value="journalist">Journalist</option>
               <option value="photographer">Photographer</option>
            </select>
            <p>Email address</p>
            <input type="email" name="email" placeholder="<?= $fetch_tutor['email']; ?>" maxlength="100" class="box">
         </div>
         <div class="col">
            <p>Current password:</p>
            <input type="password" name="old_pass" placeholder="Enter your old password" maxlength="20" class="box">
            <p>New password:</p>
            <input type="password" name="new_pass" placeholder="Enter your new password" maxlength="20" class="box">
            <p>Confirm password:</p>
            <input type="password" name="cpass" placeholder="Confirm your new password" maxlength="20" class="box">
         </div>
      </div>
      <p>Update avatar:</p>
      <input type="file" name="image" accept="image/*" class="box">
      <input type="submit" name="submit" value="Update now" class="btn">
   </form>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>
   
</body>
</html>
