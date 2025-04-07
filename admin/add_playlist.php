<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
} else {
   $tutor_id = '';
   header('location:login.php');
   exit;
}

if(isset($_POST['submit'])){

   $id = unique_id();
   $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
   $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
   $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);

   // معالجة الصورة
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
   $allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];

   $rename = unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_files/'.$rename;

   if(!in_array($ext, $allowed_image_types)){
      $message[] = '❌ Invalid image format!';
   } elseif($image_size > 2000000){
      $message[] = '❌ Image size is too large!';
   } else {
      $add_playlist = $conn->prepare("INSERT INTO `playlist`(id, tutor_id, title, description, thumb, status) VALUES(?,?,?,?,?,?)");
      $add_playlist->execute([$id, $tutor_id, $title, $description, $rename, $status]);
      move_uploaded_file($image_tmp_name, $image_folder);
      $message[] = '✅ New playlist created!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Add Playlist</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="playlist-form">

   <h1 class="heading">Create playlist</h1>

   <?php
   if(!empty($message)){
      foreach($message as $msg){
         echo '<p class="message">'.$msg.'</p>';
      }
   }
   ?>

   <form action="" method="post" enctype="multipart/form-data">
      <p>Playlist status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="" disabled <?= !isset($_POST['status']) ? 'selected' : '' ?>>-- Select status</option>
         <option value="active" <?= (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : '' ?>>Active</option>
         <option value="deactive" <?= (isset($_POST['status']) && $_POST['status'] === 'deactive') ? 'selected' : '' ?>>Deactive</option>
      </select>

      <p>Playlist title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="Enter playlist title" class="box" value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">

      <p>Playlist description <span>*</span></p>
      <textarea name="description" class="box" required placeholder="Write description" maxlength="1000" cols="30" rows="10"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>

      <p>Playlist thumbnail <span>*</span></p>
      <input type="file" name="image" accept="image/*" required class="box">

      <input type="submit" value="Create playlist" name="submit" class="btn">
   </form>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
