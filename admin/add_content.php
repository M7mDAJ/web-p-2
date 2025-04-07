<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
   exit;
}

if(isset($_POST['submit'])){

   $id = unique_id();
   $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);
   $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
   $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
   $playlist = filter_var($_POST['playlist'], FILTER_SANITIZE_STRING);

   // صورة المصغّر
   $thumb = $_FILES['thumb']['name'];
   $thumb = filter_var($thumb, FILTER_SANITIZE_STRING);
   $thumb_ext = strtolower(pathinfo($thumb, PATHINFO_EXTENSION));
   $rename_thumb = unique_id() . '.' . $thumb_ext;
   $thumb_size = $_FILES['thumb']['size'];
   $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
   $thumb_folder = '../uploaded_files/' . $rename_thumb;

   // ملف الفيديو
   $video = $_FILES['video']['name'];
   $video = filter_var($video, FILTER_SANITIZE_STRING);
   $video_ext = strtolower(pathinfo($video, PATHINFO_EXTENSION));
   $rename_video = unique_id() . '.' . $video_ext;
   $video_tmp_name = $_FILES['video']['tmp_name'];
   $video_folder = '../uploaded_files/' . $rename_video;

   $allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];
   $allowed_video_types = ['mp4', 'mov', 'avi', 'mkv'];

   if(!in_array($thumb_ext, $allowed_image_types)){
      $message[] = '❌ Invalid image format!';
   }elseif(!in_array($video_ext, $allowed_video_types)){
      $message[] = '❌ Invalid video format!';
   }elseif($thumb_size > 2000000){
      $message[] = '❌ Image size is too large!';
   }else{
      $add_playlist = $conn->prepare("INSERT INTO `content` (id, tutor_id, playlist_id, title, description, video, thumb, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $add_playlist->execute([$id, $tutor_id, $playlist, $title, $description, $rename_video, $rename_thumb, $status]);
      move_uploaded_file($thumb_tmp_name, $thumb_folder);
      move_uploaded_file($video_tmp_name, $video_folder);
      $message[] = '✅ New course uploaded successfully!';
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

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="video-form">

   <h1 class="heading">Upload Content</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <p>Video status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="" disabled <?= !isset($_POST['status']) ? 'selected' : '' ?>>-- Select status</option>
         <option value="active" <?= (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : '' ?>>Active</option>
         <option value="deactive" <?= (isset($_POST['status']) && $_POST['status'] === 'deactive') ? 'selected' : '' ?>>Deactive</option>
      </select>

      <p>Video title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="Enter video title" class="box" value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">

      <p>Video description <span>*</span></p>
      <textarea name="description" class="box" required placeholder="Write description" maxlength="1000" cols="30" rows="10"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>

      <p>Video playlist <span>*</span></p>
      <select name="playlist" class="box" required>
         <option value="" disabled <?= !isset($_POST['playlist']) ? 'selected' : '' ?>>--Select playlist</option>
         <?php
         $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
         $select_playlists->execute([$tutor_id]);
         if($select_playlists->rowCount() > 0){
            while($fetch_playlist = $select_playlists->fetch(PDO::FETCH_ASSOC)){
               $selected = (isset($_POST['playlist']) && $_POST['playlist'] == $fetch_playlist['id']) ? 'selected' : '';
               echo '<option value="' . $fetch_playlist['id'] . '" ' . $selected . '>' . htmlspecialchars($fetch_playlist['title']) . '</option>';
            }
         }else{
            echo '<option value="" disabled>no playlist created yet!</option>';
         }
         ?>
      </select>

      <p>Select thumbnail <span>*</span></p>
      <input type="file" name="thumb" accept="image/*" required class="box">

      <p>Select video <span>*</span></p>
      <input type="file" name="video" accept="video/*" required class="box">

      <input type="submit" value="Upload Video" name="submit" class="btn">
   </form>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
