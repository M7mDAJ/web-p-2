<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
   exit();
}

if(isset($_POST['submit'])){

   $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
   $select_tutor->execute([$tutor_id]);
   $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

   $prev_pass = $fetch_tutor['password'];
   $prev_image = $fetch_tutor['image'];

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $profession = filter_var($_POST['profession'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

   if(!empty($name)){
      $update_name = $conn->prepare("UPDATE `tutors` SET name = ? WHERE id = ?");
      $update_name->execute([$name, $tutor_id]);
      $message[] = 'Username updated successfully!';
   }

   if(!empty($profession)){
      $update_profession = $conn->prepare("UPDATE `tutors` SET profession = ? WHERE id = ?");
      $update_profession->execute([$profession, $tutor_id]);
      $message[] = 'Profession updated successfully!';
   }

   if(!empty($email)){
      $select_email = $conn->prepare("SELECT email FROM `tutors` WHERE email = ? AND id != ?");
      $select_email->execute([$email, $tutor_id]);
      if($select_email->rowCount() > 0){
         $message[] = 'Email already taken!';
      }else{
         $update_email = $conn->prepare("UPDATE `tutors` SET email = ? WHERE id = ?");
         $update_email->execute([$email, $tutor_id]);
         $message[] = 'Email updated successfully!';
      }
   }

   if(!empty($_FILES['image']['name'])){
      $image = filter_var($_FILES['image']['name'], FILTER_SANITIZE_STRING);
      $ext = pathinfo($image, PATHINFO_EXTENSION);
      $rename = uniqid().'.'.$ext;
      $image_size = $_FILES['image']['size'];
      $image_tmp_name = $_FILES['image']['tmp_name'];
      $image_folder = '../uploaded_files/'.$rename;
      
      if($image_size > 2000000){
         $message[] = 'Image size too large!';
      }else{
         move_uploaded_file($image_tmp_name, $image_folder);
         $update_image = $conn->prepare("UPDATE `tutors` SET image = ? WHERE id = ?");
         $update_image->execute([$rename, $tutor_id]);
         if($prev_image && file_exists('../uploaded_files/'.$prev_image)){
            unlink('../uploaded_files/'.$prev_image);
         }
         $message[] = 'Image updated successfully!';
      }
   }

   $empty_pass = sha1('');
   $old_pass = sha1($_POST['old_pass']);
   $new_pass = sha1($_POST['new_pass']);
   $cpass = sha1($_POST['cpass']);

   if($old_pass !== $empty_pass){
      if($old_pass !== $prev_pass){
         $message[] = 'Current password not matched!';
      }elseif($new_pass !== $cpass){
         $message[] = 'Confirm password not matched!';
      }else{
         if($new_pass !== $empty_pass){
            $update_pass = $conn->prepare("UPDATE `tutors` SET password = ? WHERE id = ?");
            $update_pass->execute([$new_pass, $tutor_id]);
            $message[] = 'Password updated successfully!';
         }else{
            $message[] = 'Please enter a new password!';
         }
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Profile</title>
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="form-container">
   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3>Update profile</h3>
      <p>Full name</p>
      <input type="text" name="name" placeholder="<?= htmlspecialchars($fetch_tutor['name']); ?>" class="box">
      <p>Your profession</p>
      <select name="profession" class="box">
         <option value="" selected><?= htmlspecialchars($fetch_tutor['profession']); ?></option>
         <option value="developer">Developer</option>
         <option value="designer">Designer</option>
         <option value="musician">Musician</option>
         <option value="biologist">Biologist</option>
         <option value="teacher">Teacher</option>
      </select>
      <p>Email address</p>
      <input type="email" name="email" placeholder="<?= htmlspecialchars($fetch_tutor['email']); ?>" class="box">
      <p>Current password</p>
      <input type="password" name="old_pass" placeholder="Enter your old password" class="box">
      <p>New password</p>
      <input type="password" name="new_pass" placeholder="Enter your new password" class="box">
      <p>Confirm password</p>
      <input type="password" name="cpass" placeholder="Confirm your new password" class="box">
      <p>Update avatar</p>
      <input type="file" name="image" accept="image/*" class="box">
      <input type="submit" name="submit" value="Update now" class="btn">
   </form>
</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>
</body>
</html>
