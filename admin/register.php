<?php

include '../components/connect.php'; // Includes the database connection file

// Check if the form is submitted
if(isset($_POST['submit'])){

   // Generate a unique ID for the tutor
   $id = unique_id();

   // Sanitize input data
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   
   $profession = $_POST['profession'];
   $profession = filter_var($profession, FILTER_SANITIZE_STRING);

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   // Hash the password and confirm password using sha1
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   // Handle the uploaded image
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION); // Get the file extension of the image
   $rename = unique_id().'.'.$ext; // Create a new unique name for the image
   $image_size = $_FILES['image']['size']; // Get the size of the uploaded image
   $image_tmp_name = $_FILES['image']['tmp_name']; // Temporary location of the uploaded image
   $image_folder = '../uploaded_files/'.$rename; // Path where the image will be saved

   // Check if the email already exists in the database
   $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE email = ?");
   $select_tutor->execute([$email]);

   // If email is already taken, show a message
   if($select_tutor->rowCount() > 0){
      $message[] = 'email already taken!';
   }else{
      // Check if passwords match
      if($pass != $cpass){
         $message[] = 'confirm password not matched!';
      }else{
         // Insert the tutor's data into the database
         $insert_tutor = $conn->prepare("INSERT INTO `tutors`(id, name, profession, email, password, image) VALUES(?,?,?,?,?,?)");
         $insert_tutor->execute([$id, $name, $profession, $email, $cpass, $rename]);

         // Move the uploaded image to the desired folder
         move_uploaded_file($image_tmp_name, $image_folder);
         
         // Show success message
         $message[] = 'new tutor registered! please login now';
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
   <title>Register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body style="padding-left: 0;">

<?php
// Display messages (errors or success)
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message form">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<!-- Registration form -->
<section class="form-container">

   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3>Register new</h3>
      <div class="flex">
         <div class="col">
            <p>Full name<span>*</span></p>
            <input type="text" name="name" placeholder="enter your name" maxlength="50" required class="box">
            <p>Your profession <span>*</span></p>
            <select name="profession" class="box" required>
               <option value="" disabled selected>-- Select your profession</option>
               <!-- Various profession options -->
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
            <p>Email address<span>*</span></p>
            <input type="email" name="email" placeholder="Enter your email" maxlength="20" required class="box">
         </div>
         <div class="col">
            <p>Password <span>*</span></p>
            <input type="password" name="pass" placeholder="Enter your password" maxlength="20" required class="box">
            <p>Confirm password <span>*</span></p>
            <input type="password" name="cpass" placeholder="Confirm your password" maxlength="20" required class="box">
            <p>Select avatar <span>*</span></p>
            <input type="file" name="image" accept="image/*" required class="box">
         </div>
      </div>
      <p class="link">Already have an account? <a href="login.php">Login now</a></p>
      <input type="submit" name="submit" value="register now" class="btn">
   </form>

</section>

<script>
// Dark mode script
let darkMode = localStorage.getItem('dark-mode');
let body = document.body;

const enableDarkMode = () => {
   body.classList.add('dark');
   localStorage.setItem('dark-mode', 'enabled');
}

const disableDarkMode = () => {
   body.classList.remove('dark');
   localStorage.setItem('dark-mode', 'disabled');
}

if(darkMode === 'enabled'){
   enableDarkMode();
}else{
   disableDarkMode();
}

</script>
   
</body>
</html>
