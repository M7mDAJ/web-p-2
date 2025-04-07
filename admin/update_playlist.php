<?php

include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
   $tutor_id = $_COOKIE['tutor_id'];
} else {
   header('Location: login.php');
   exit;
}

if (isset($_GET['get_id'])) {
   $get_id = $_GET['get_id'];
} else {
   header('Location: playlist.php');
   exit;
}

if (isset($_POST['submit'])) {
   $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
   $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
   $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);

   // Update playlist info
   $update_playlist = $conn->prepare("UPDATE `playlist` SET title = ?, description = ?, status = ? WHERE id = ?");
   $update_playlist->execute([$title, $description, $status, $get_id]);

   // Handle image upload
   $old_image = filter_var($_POST['old_image'], FILTER_SANITIZE_STRING);

   if (!empty($_FILES['image']['name'])) {
      $image = filter_var($_FILES['image']['name'], FILTER_SANITIZE_STRING);
      $ext = pathinfo($image, PATHINFO_EXTENSION);
      $rename = unique_id() . '.' . $ext;
      $image_size = $_FILES['image']['size'];
      $image_tmp_name = $_FILES['image']['tmp_name'];
      $image_folder = '../uploaded_files/' . $rename;

      if ($image_size > 2 * 1024 * 1024) {
         $message[] = 'Image size is too large!';
      } else {
         move_uploaded_file($image_tmp_name, $image_folder);
         $update_image = $conn->prepare("UPDATE `playlist` SET thumb = ? WHERE id = ?");
         $update_image->execute([$rename, $get_id]);

         if (!empty($old_image) && $old_image !== $rename) {
            @unlink('../uploaded_files/' . $old_image);
         }
      }
   }

   $message[] = 'Playlist updated successfully!';
}

if (isset($_POST['delete'])) {
   $delete_id = filter_var($_POST['playlist_id'], FILTER_SANITIZE_STRING);

   $select_thumb = $conn->prepare("SELECT thumb FROM `playlist` WHERE id = ?");
   $select_thumb->execute([$delete_id]);
   $fetch = $select_thumb->fetch(PDO::FETCH_ASSOC);

   if ($fetch && !empty($fetch['thumb'])) {
      @unlink('../uploaded_files/' . $fetch['thumb']);
   }

   $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?")->execute([$delete_id]);
   $conn->prepare("DELETE FROM `playlist` WHERE id = ?")->execute([$delete_id]);

   header('Location: playlists.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Update Playlist</title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <!-- Custom CSS -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="playlist-form">
   <h1 class="heading">Update Playlist</h1>

   <?php
   $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ?");
   $select_playlist->execute([$get_id]);

   if ($select_playlist->rowCount() > 0) {
      while ($playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)) {
         $playlist_id = $playlist['id'];

         $count_videos = $conn->prepare("SELECT COUNT(*) FROM `content` WHERE playlist_id = ?");
         $count_videos->execute([$playlist_id]);
         $total_videos = $count_videos->fetchColumn();
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= htmlspecialchars($playlist['thumb']); ?>">
      <input type="hidden" name="playlist_id" value="<?= htmlspecialchars($playlist_id); ?>">

      <p>Playlist Status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="<?= $playlist['status']; ?>" selected><?= ucfirst($playlist['status']); ?></option>
         <option value="active">Active</option>
         <option value="deactive">Deactive</option>
      </select>

      <p>Playlist Title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required class="box" value="<?= htmlspecialchars($playlist['title']); ?>">

      <p>Playlist Description <span>*</span></p>
      <textarea name="description" class="box" required maxlength="1000" cols="30" rows="10"><?= htmlspecialchars($playlist['description']); ?></textarea>

      <p>Current Thumbnail <span>*</span></p>
      <div class="thumb">
         <span><?= $total_videos; ?></span>
         <img src="../uploaded_files/<?= htmlspecialchars($playlist['thumb']); ?>" alt="Playlist Thumbnail">
      </div>

      <p>Update Thumbnail</p>
      <input type="file" name="image" accept="image/*" class="box">

      <input type="submit" name="submit" value="Update Playlist" class="btn">

      <div class="flex-btn">
         <input type="submit" name="delete" value="Delete" class="delete-btn" onclick="return confirm('Are you sure you want to delete this playlist?');">
         <a href="view_playlist.php?get_id=<?= $playlist_id; ?>" class="option-btn">View Playlist</a>
      </div>
   </form>
   <?php
      }
   } else {
      echo '<p class="empty">No playlist found!</p>';
   }
   ?>
</section>

<?php include '../components/footer.php'; ?>
<script src="../js/admin_script.js"></script>
</body>
</html>
