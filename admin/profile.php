<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = filter_var($_COOKIE['tutor_id'], FILTER_VALIDATE_INT);
} else {
   $tutor_id = '';
   header('location:login.php');
   exit();
}

// جلب بيانات المدرب
$select_profile = $conn->prepare("SELECT name, profession, image FROM `tutors` WHERE id = ? LIMIT 1");
$select_profile->execute([$tutor_id]);

if ($select_profile->rowCount() > 0) {
   $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
} else {
   header('location:logout.php'); // تسجيل الخروج إذا لم يتم العثور على بيانات
   exit();
}

// جلب الإحصائيات في استعلام واحد لتحسين الأداء
$select_stats = $conn->prepare("
   SELECT 
      (SELECT COUNT(*) FROM `playlist` WHERE tutor_id = ?) AS total_playlists,
      (SELECT COUNT(*) FROM `content` WHERE tutor_id = ?) AS total_contents,
      (SELECT COUNT(*) FROM `likes` WHERE tutor_id = ?) AS total_likes,
      (SELECT COUNT(*) FROM `comments` WHERE tutor_id = ?) AS total_comments
");
$select_stats->execute([$tutor_id, $tutor_id, $tutor_id, $tutor_id]);
$stats = $select_stats->fetch(PDO::FETCH_ASSOC);

// تخزين القيم في متغيرات
$total_playlists = number_format($stats['total_playlists']);
$total_contents = number_format($stats['total_contents']);
$total_likes = number_format($stats['total_likes']);
$total_comments = number_format($stats['total_comments']);

// تعيين صورة افتراضية في حالة عدم توفر صورة
$profile_image = !empty($fetch_profile['image']) ? "../uploaded_files/".htmlspecialchars($fetch_profile['image']) : "../images/default.png";

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Profile</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="tutor-profile" style="min-height: calc(100vh - 19rem);"> 

   <h1 class="heading">Profile Details</h1>

   <div class="details">
      <div class="tutor">
         <img src="<?= $profile_image; ?>" alt="Profile Picture">
         <h3><?= htmlspecialchars($fetch_profile['name']); ?></h3>
         <span><?= htmlspecialchars($fetch_profile['profession']); ?></span>
         <a href="update.php" class="inline-btn">Update Profile</a>
      </div>
      <div class="flex">
         <div class="box">
            <span><?= $total_playlists; ?></span>
            <p>Total Playlists</p>
            <a href="playlists.php" class="btn">View Playlists</a>
         </div>
         <div class="box">
            <span><?= $total_contents; ?></span>
            <p>Total Videos</p>
            <a href="contents.php" class="btn">View Contents</a>
         </div>
         <div class="box">
            <span><?= $total_likes; ?></span>
            <p>Total Likes</p>
            <a href="contents.php" class="btn">View Contents</a>
         </div>
         <div class="box">
            <span><?= $total_comments; ?></span>
            <p>Total Comments</p>
            <a href="comments.php" class="btn">View Comments</a>
         </div>
      </div>
   </div>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
