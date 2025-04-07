<?php

include '../components/connect.php';

if(isset($_POST['submit'])){

   $id = unique_id();
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $profession = filter_var($_POST['profession'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $password = $_POST['pass'];
   $confirm_password = $_POST['cpass'];

   // ✅ التحقق من صحة البريد الإلكتروني
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $message[] = 'Invalid email format!';
   }
   // ✅ التحقق من تطابق كلمة المرور
   elseif ($password !== $confirm_password) {
      $message[] = 'Confirm password does not match!';
   }
   else {
      // ✅ التحقق من عدم تكرار البريد الإلكتروني
      $select_tutor = $conn->prepare("SELECT COUNT(*) FROM `tutors` WHERE email = ?");
      $select_tutor->execute([$email]);
      
      if($select_tutor->fetchColumn() > 0){
         $message[] = 'Email already taken!';
      } else {
         // ✅ تأمين كلمة المرور
         $hashed_password = password_hash($password, PASSWORD_DEFAULT);

         // ✅ التحقق من تحميل الصورة
         if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image_name = $_FILES['image']['name'];
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_size = $_FILES['image']['size'];
            $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
            $rename = unique_id().'.'.$image_ext;
            $image_folder = '../uploaded_files/'.$rename;

            // ✅ التحقق من صيغة الصورة
            if (!in_array($image_ext, $allowed_exts)) {
               $message[] = 'Invalid image format! Only JPG, JPEG, PNG, GIF allowed.';
            }
            // ✅ التحقق من حجم الصورة (مثلاً، لا يزيد عن 2MB)
            elseif ($image_size > 2 * 1024 * 1024) {
               $message[] = 'Image size is too large! Max 2MB allowed.';
            }
            // ✅ التأكد من أن الملف هو صورة حقيقية
            elseif (!in_array(mime_content_type($image_tmp_name), ['image/jpeg', 'image/png', 'image/gif'])) {
               $message[] = 'Invalid image type!';
            }
            else {
               // ✅ رفع الصورة
               if(move_uploaded_file($image_tmp_name, $image_folder)) {
                  // ✅ إدخال البيانات في قاعدة البيانات
                  $insert_tutor = $conn->prepare("INSERT INTO `tutors`(id, name, profession, email, password, image) VALUES(?,?,?,?,?,?)");
                  $insert_tutor->execute([$id, $name, $profession, $email, $hashed_password, $rename]);

                  $message[] = 'New tutor registered! Please login now';
               } else {
                  $message[] = 'Failed to upload image!';
               }
            }
         } else {
            $message[] = 'Please select a valid avatar!';
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
   <title>Register</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body style="padding-left: 0;">

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '
      <div class="message form">
         <span>'.$msg.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<!-- Register section starts -->
<section class="form-container">
   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3>Register New</h3>
      <div class="flex">
         <div class="col">
            <p>Full Name <span>*</span></p>
            <input type="text" name="name" placeholder="Enter your name" maxlength="50" required class="box">
            <p>Your Profession <span>*</span></p>
            <select name="profession" class="box" required>
               <option value="" disabled selected>-- Select your profession --</option>
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
            <p>Email Address <span>*</span></p>
            <input type="email" name="email" placeholder="Enter your email" maxlength="50" required class="box">
         </div>
         <div class="col">
            <p>Password <span>*</span></p>
            <input type="password" name="pass" placeholder="Enter your password" maxlength="50" required class="box">
            <p>Confirm Password <span>*</span></p>
            <input type="password" name="cpass" placeholder="Confirm your password" maxlength="50" required class="box">
            <p>Select Avatar <span>*</span></p>
            <input type="file" name="image" accept="image/*" required class="box">
         </div>
      </div>
      <p class="link">Already have an account? <a href="login.php">Login now</a></p>
      <input type="submit" name="submit" value="Register Now" class="btn">
   </form>
</section>

</body>
</html>
