<?php

include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
   $tutor_id = $_COOKIE['tutor_id'];
} else {
   $tutor_id = '';
   header('location:login.php');
   exit;
}

if (isset($_GET['get_id'])) {
   $get_id = $_GET['get_id'];
} else {
   header('location:dashboard.php');
   exit;
}

if (isset($_POST['update'])) {

   $video_id = filter_var($_POST['video_id'], FILTER_SANITIZE_STRING);
   $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);
   $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
   $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
   $playlist = filter_var($_POST['playlist'], FILTER_SANITIZE_STRING);

   $update_content = $conn->prepare("UPDATE `content` SET title = ?, description = ?, status = ? WHERE id = ?");
   $update_content->execute([$title, $description, $status, $video_id]);

   if (!empty($playlist)) {
      $update_playlist = $conn->prepare("UPDATE `content` SET playlist_id = ? WHERE id = ?");
      $update_playlist->execute([$playlist, $video_id]);
   }

   // Update thumbnail
   $old_thumb = filter_var($_POST['old_thumb'], FILTER_SANITIZE_STRING);
   if (!empty($_FILES['thumb']['name'])) {
      $thumb = filter_var($_FILES['thumb']['name'], FILTER_SANITIZE_STRING);
      $thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION);
      $rename_thumb = unique_id() . '.' . $thumb_ext;
      $thumb_size = $_FILES['thumb']['size'];
      $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
      $thumb_folder = '../uploaded_files/' . $rename_thumb;

      if ($thumb_size <= 2 * 1024 * 1024) {
         move_uploaded_file($thumb_tmp_name, $thumb_folder);
         $update_thumb = $conn->prepare("UPDATE `content` SET thumb = ? WHERE id = ?");
         $update_thumb->execute([$rename_thumb, $video_id]);
         if (!empty($old_thumb) && $old_thumb != $rename_thumb) {
            @unlink('../uploaded_files/' . $old_thumb);
         }
      } else {
         $message[] = 'Thumbnail size is too large!';
      }
   }

   // Update video
   $old_video = filter_var($_POST['old_video'], FILTER_SANITIZE_STRING);
   if (!empty($_FILES['video']['name'])) {
      $video = filter_var($_FILES['video']['name'], FILTER_SANITIZE_STRING);
      $video_ext = pathinfo($video, PATHINFO_EXTENSION);
      $rename_video = unique_id() . '.' . $video_ext;
      $video_tmp_name = $_FILES['video']['tmp_name'];
      $video_folder = '../uploaded_files/' . $rename_video;

      move_uploaded_file($video_tmp_name, $video_folder);
      $update_video = $conn->prepare("UPDATE `content` SET video = ? WHERE id = ?");
      $update_video->execute([$rename_video, $video_id]);
      if (!empty($old_video) && $old_video != $rename_video) {
         @unlink('../uploaded_files/' . $old_video);
      }
   }

   $message[] = 'Content updated successfully!';
}

if (isset($_POST['delete_video'])) {
   $delete_id = filter_var($_POST['video_id'], FILTER_SANITIZE_STRING);

   $select = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
   $select->execute([$delete_id]);
   $fetch = $select->fetch(PDO::FETCH_ASSOC);
   if ($fetch) {
      @unlink('../uploaded_files/' . $fetch['thumb']);
      @unlink('../uploaded_files/' . $fetch['video']);
   }

   $conn->prepare("DELETE FROM `likes` WHERE content_id = ?")->execute([$delete_id]);
   $conn->prepare("DELETE FROM `comments` WHERE content_id = ?")->execute([$delete_id]);
   $conn->prepare("DELETE FROM `content` WHERE id = ?")->execute([$delete_id]);

   header('location:contents.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Update Video</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="video-form">
   <h1 class="heading">Update Content</h1>

   <?php
   $select_videos = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND tutor_id = ?");
   $select_videos->execute([$get_id, $tutor_id]);
   if ($select_videos->rowCount() > 0) {
      while ($fetch_videos = $select_videos->fetch(PDO::FETCH_ASSOC)) {
         $video_id = $fetch_videos['id'];
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="video_id" value="<?= $video_id; ?>">
      <input type="hidden" name="old_thumb" value="<?= $fetch_videos['thumb']; ?>">
      <input type="hidden" name="old_video" value="<?= $fetch_videos['video']; ?>">

      <p>Update status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="<?= $fetch_videos['status']; ?>" selected><?= ucfirst($fetch_videos['status']); ?></option>
         <option value="active">Active</option>
         <option value="deactive">Deactive</option>
      </select>

      <p>Update title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required class="box" value="<?= $fetch_videos['title']; ?>">

      <p>Update description <span>*</span></p>
      <textarea name="description" class="box" required maxlength="1000" cols="30" rows="10"><?= $fetch_videos['description']; ?></textarea>

      <p>Update playlist</p>
      <select name="playlist" class="box">
         <option value="<?= $fetch_videos['playlist_id']; ?>" selected>-- Current Playlist --</option>
         <?php
         $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
         $select_playlists->execute([$tutor_id]);
         while ($playlist = $select_playlists->fetch(PDO::FETCH_ASSOC)) {
         ?>
         <option value="<?= $playlist['id']; ?>"><?= $playlist['title']; ?></option>
         <?php } ?>
      </select>

      <p>Current Thumbnail</p>
      <img src="../uploaded_files/<?= $fetch_videos['thumb']; ?>" alt="" width="200">
      <p>Update Thumbnail</p>
      <input type="file" name="thumb" accept="image/*" class="box">

      <p>Current Video</p>
      <video src="../uploaded_files/<?= $fetch_videos['video']; ?>" controls width="300"></video>
      <p>Update Video</p>
      <input type="file" name="video" accept="video/*" class="box">

      <input type="submit" name="update" value="Update Content" class="btn">

      <div class="flex-btn">
         <a href="view_content.php?get_id=<?= $video_id; ?>" class="option-btn">View Content</a>
         <input type="submit" name="delete_video" value="Delete Content" class="delete-btn" onclick="return confirm('Are you sure you want to delete this content?');">
      </div>
   </form>
   <?php
      }
   } else {
      echo '<p class="empty">Video not found! <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">Add Videos</a></p>';
   }
   ?>
</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>