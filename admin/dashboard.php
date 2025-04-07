<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
   exit();
}

// جلب بيانات المعلم
$select_tutor = $conn->prepare("SELECT name FROM `tutors` WHERE id = ? LIMIT 1");
$select_tutor->execute([$tutor_id]);
$fetch_profile = $select_tutor->fetch(PDO::FETCH_ASSOC);

// حساب المحتويات
$select_contents = $conn->prepare("SELECT COUNT(*) FROM `content` WHERE tutor_id = ?");
$select_contents->execute([$tutor_id]);
$total_contents = $select_contents->fetchColumn();

// حساب القوائم التشغيلية
$select_playlists = $conn->prepare("SELECT COUNT(*) FROM `playlist` WHERE tutor_id = ?");
$select_playlists->execute([$tutor_id]);
$total_playlists = $select_playlists->fetchColumn();

// حساب الإعجابات
$select_likes = $conn->prepare("SELECT COUNT(*) FROM `likes` WHERE tutor_id = ?");
$select_likes->execute([$tutor_id]);
$total_likes = $select_likes->fetchColumn();

// حساب التعليقات
$select_comments = $conn->prepare("SELECT COUNT(*) FROM `comments` WHERE tutor_id = ?");
$select_comments->execute([$tutor_id]);
$total_comments = $select_comments->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="dashboard">
   <h1 class="heading">Dashboard</h1>

   <div class="box-container">
      <div class="box">
         <h3>Welcome!</h3>
         <p><?= isset($fetch_profile['name']) ? $fetch_profile['name'] : 'Unknown'; ?></p>
         <a href="profile.php" class="btn">View profile</a>
      </div>

      <div class="box">
         <h3><?= $total_contents; ?></h3>
         <p>Total contents</p>
         <a href="add_content.php" class="btn">Add new content</a>
      </div>

      <div class="box">
         <h3><?= $total_playlists; ?></h3>
         <p>Total playlists</p>
         <a href="add_playlist.php" class="btn">Add new playlist</a>
      </div>

      <div class="box">
         <h3><?= $total_likes; ?></h3>
         <p>Total likes</p>
         <a href="contents.php" class="btn">View contents</a>
      </div>

      <div class="box">
         <h3><?= $total_comments; ?></h3>
         <p>Total comments</p>
         <a href="comments.php" class="btn">View comments</a>
      </div>

      <div class="box">
         <h3>Quick select</h3>
         <p>Login or Register</p>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>
      </div>
   </div>
</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
