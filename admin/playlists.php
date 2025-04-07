<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
   exit();
}

$message = [];

if(isset($_POST['delete'])){
   $delete_id = filter_var($_POST['playlist_id'], FILTER_VALIDATE_INT);

   if($delete_id) {
      $verify_playlist = $conn->prepare("SELECT thumb FROM `playlist` WHERE id = ? AND tutor_id = ? LIMIT 1");
      $verify_playlist->execute([$delete_id, $tutor_id]);

      if($verify_playlist->rowCount() > 0){
         $fetch_thumb = $verify_playlist->fetch(PDO::FETCH_ASSOC);
         $thumb_path = '../uploaded_files/'.$fetch_thumb['thumb'];

         if (!empty($fetch_thumb['thumb']) && file_exists($thumb_path)) {
            unlink($thumb_path);
         }

         $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?");
         $delete_bookmark->execute([$delete_id]);
         
         $delete_playlist = $conn->prepare("DELETE FROM `playlist` WHERE id = ? LIMIT 1");
         $delete_playlist->execute([$delete_id]);
         
         $message[] = 'Playlist deleted!';
      }else{
         $message[] = 'Playlist already deleted or not found!';
      }
   } else {
      $message[] = 'Invalid playlist ID!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Playlists</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="playlists">
   <h1 class="heading">Added playlists</h1>
   
   <?php
   if (!empty($message)) {
      foreach ($message as $msg) {
         echo '<div class="message form">
                 <span>' . htmlspecialchars($msg) . '</span>
                 <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
               </div>';
      }
   }
   ?>
   
   <div class="box-container">
      <div class="box" style="text-align: center;">
         <h3 class="title" style="margin-bottom: .5rem;">Create new playlist</h3>
         <a href="add_playlist.php" class="btn">Add playlist</a>
      </div>
      <?php
         $select_playlist = $conn->prepare("SELECT id, title, thumb, description, status, date FROM `playlist` WHERE tutor_id = ? ORDER BY date DESC");
         $select_playlist->execute([$tutor_id]);

         if($select_playlist->rowCount() > 0){
            while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
               $playlist_id = $fetch_playlist['id'];

               $count_videos = $conn->prepare("SELECT COUNT(*) FROM `content` WHERE playlist_id = ?");
               $count_videos->execute([$playlist_id]);
               $total_videos = $count_videos->fetchColumn();
      ?>
      <div class="box">
         <div class="flex">
            <div><i class="fas fa-circle-dot" style="color:<?= ($fetch_playlist['status'] == 'active') ? 'limegreen' : 'red'; ?>;"></i><span style="color:<?= ($fetch_playlist['status'] == 'active') ? 'limegreen' : 'red'; ?>;"><?= $fetch_playlist['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fetch_playlist['date']; ?></span></div>
         </div>
         <div class="thumb">
            <span><?= $total_videos; ?></span>
            <img src="../uploaded_files/<?= htmlspecialchars($fetch_playlist['thumb']); ?>" alt="">
         </div>
         <h3 class="title"> <?= htmlspecialchars($fetch_playlist['title']); ?> </h3>
         <p class="description"> <?= htmlspecialchars($fetch_playlist['description']); ?> </p>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="playlist_id" value="<?= $playlist_id; ?>">
            <a href="update_playlist.php?get_id=<?= $playlist_id; ?>" class="option-btn">Update</a>
            <input type="submit" value="Delete" class="delete-btn" onclick="return confirmDelete();" name="delete">
         </form>
         <a href="view_playlist.php?get_id=<?= $playlist_id; ?>" class="btn">View playlist</a>
      </div>
      <?php
         } 
      }else{
         echo '<p class="empty">No playlists added yet!</p>';
      }
      ?>
   </div>
</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

<script>
   document.querySelectorAll('.playlists .box-container .box .description').forEach(content => {
      if(content.innerHTML.length > 100) content.innerHTML = content.innerHTML.slice(0, 100) + '...';
   });

   function confirmDelete() {
      return confirm("Are you sure you want to delete this playlist? This action cannot be undone.");
   }
</script>

</body>
</html>
