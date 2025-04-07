<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
} else {
   header('location:login.php');
   exit();
}

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
} else {
   header('location:playlist.php');
   exit();
}

$message = [];

// حذف قائمة تشغيل
if(isset($_POST['delete_playlist'])){
   $delete_id = filter_var($_POST['playlist_id'], FILTER_SANITIZE_STRING);

   // حذف الصورة المصغرة
   $delete_playlist_thumb = $conn->prepare("SELECT thumb FROM `playlist` WHERE id = ? LIMIT 1");
   $delete_playlist_thumb->execute([$delete_id]);
   if($delete_playlist_thumb->rowCount() > 0){
      $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);
      $thumb_path = '../uploaded_files/'.$fetch_thumb['thumb'];
      if(file_exists($thumb_path)){
         unlink($thumb_path);
      }
   }

   // حذف العلامات المرجعية
   $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?")->execute([$delete_id]);

   // حذف قائمة التشغيل
   $conn->prepare("DELETE FROM `playlist` WHERE id = ?")->execute([$delete_id]);

   header('location:playlists.php');
   exit();
}

// حذف فيديو من القائمة
if(isset($_POST['delete_video'])){
   $delete_id = filter_var($_POST['video_id'], FILTER_SANITIZE_STRING);

   $verify_video = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
   $verify_video->execute([$delete_id]);

   if($verify_video->rowCount() > 0){
      $video_data = $verify_video->fetch(PDO::FETCH_ASSOC);

      // حذف الصورة المصغرة والفيديو من السيرفر
      $thumb_path = '../uploaded_files/'.$video_data['thumb'];
      $video_path = '../uploaded_files/'.$video_data['video'];

      if(file_exists($thumb_path)) unlink($thumb_path);
      if(file_exists($video_path)) unlink($video_path);

      // حذف الإعجابات والتعليقات والفيديو نفسه
      $conn->prepare("DELETE FROM `likes` WHERE content_id = ?")->execute([$delete_id]);
      $conn->prepare("DELETE FROM `comments` WHERE content_id = ?")->execute([$delete_id]);
      $conn->prepare("DELETE FROM `content` WHERE id = ?")->execute([$delete_id]);

      $message[] = 'Video deleted!';
   } else {
      $message[] = 'Video already deleted!';
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
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<?php if(!empty($message)): ?>
   <div class="messages">
      <?php foreach($message as $msg): ?>
         <div class="message"><?= $msg; ?></div>
      <?php endforeach; ?>
   </div>
<?php endif; ?>

<section class="playlist-details">
   <h1 class="heading">Playlist Details</h1>

   <?php
      $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? AND tutor_id = ?");
      $select_playlist->execute([$get_id, $tutor_id]);

      if($select_playlist->rowCount() > 0){
         $fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC);
         $playlist_id = $fetch_playlist['id'];

         $count_videos = $conn->prepare("SELECT COUNT(*) FROM `content` WHERE playlist_id = ?");
         $count_videos->execute([$playlist_id]);
         $total_videos = $count_videos->fetchColumn();
   ?>
   <div class="row">
      <div class="thumb">
         <span><?= $total_videos; ?></span>
         <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
      </div>
      <div class="details">
         <h3 class="title"><?= $fetch_playlist['title']; ?></h3>
         <div class="date"><i class="fas fa-calendar"></i><span><?= $fetch_playlist['date']; ?></span></div>
         <div class="description"><?= $fetch_playlist['description']; ?></div>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="playlist_id" value="<?= $playlist_id; ?>">
            <a href="update_playlist.php?get_id=<?= $playlist_id; ?>" class="option-btn">Update Playlist</a>
            <input type="submit" value="Delete Playlist" class="delete-btn" onclick="return confirm('Delete this playlist?');" name="delete_playlist">
         </form>
      </div>
   </div>
   <?php } else {
      echo '<p class="empty">No playlist found!</p>';
   } ?>
</section>

<section class="contents">
   <h1 class="heading">Playlist Videos</h1>

   <div class="box-container">
   <?php
      if(isset($playlist_id)){
         $select_videos = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ? AND playlist_id = ?");
         $select_videos->execute([$tutor_id, $playlist_id]);

         if($select_videos->rowCount() > 0){
            while($video = $select_videos->fetch(PDO::FETCH_ASSOC)){
               $video_id = $video['id'];
   ?>
      <div class="box">
         <div class="flex">
            <div>
               <i class="fas fa-dot-circle" style="color:<?= $video['status'] == 'active' ? 'limegreen' : 'red'; ?>"></i>
               <span style="color:<?= $video['status'] == 'active' ? 'limegreen' : 'red'; ?>"><?= $video['status']; ?></span>
            </div>
            <div><i class="fas fa-calendar"></i><span><?= $video['date']; ?></span></div>
         </div>
         <img src="../uploaded_files/<?= $video['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $video['title']; ?></h3>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="video_id" value="<?= $video_id; ?>">
            <a href="update_content.php?get_id=<?= $video_id; ?>" class="option-btn">Update</a>
            <input type="submit" value="Delete" class="delete-btn" onclick="return confirm('Delete this video?');" name="delete_video">
         </form>
         <a href="view_content.php?get_id=<?= $video_id; ?>" class="btn">Watch Video</a>
      </div>
   <?php
            }
         } else {
            echo '<p class="empty">No videos added yet! <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">Add Videos</a></p>';
         }
      }
   ?>
   </div>
</section>

<?php include '../components/footer.php'; ?>
<script src="../js/admin_script.js"></script>
</body>
</html>
