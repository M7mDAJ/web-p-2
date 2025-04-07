<?php

include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    $tutor_id = '';
    header('location:login.php');
    exit();
}

if (isset($_POST['delete_video'])) {
    $delete_id = filter_var($_POST['video_id'], FILTER_SANITIZE_STRING);

    
    $verify_video = $conn->prepare("SELECT thumb, video FROM `content` WHERE id = ? LIMIT 1");
    $verify_video->execute([$delete_id]);

    if ($verify_video->rowCount() > 0) {
        $fetch_video = $verify_video->fetch(PDO::FETCH_ASSOC);

        
        if (!empty($fetch_video['thumb']) && file_exists("../uploaded_files/" . $fetch_video['thumb'])) {
            unlink("../uploaded_files/" . $fetch_video['thumb']);
        }

        if (!empty($fetch_video['video']) && file_exists("../uploaded_files/" . $fetch_video['video'])) {
            unlink("../uploaded_files/" . $fetch_video['video']);
        }

        
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
    <title>Dashboard</title>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="contents">
    <h1 class="heading">Your Contents</h1>

    <div class="box-container">
        <div class="box" style="text-align: center;">
            <h3 class="title" style="margin-bottom: .5rem;">Create New Content</h3>
            <a href="add_content.php" class="btn">Add Content</a>
        </div>

        <?php
        $select_videos = $conn->prepare("SELECT id, title, thumb, status, date FROM `content` WHERE tutor_id = ? ORDER BY date DESC");
        $select_videos->execute([$tutor_id]);

        if ($select_videos->rowCount() > 0) {
            while ($fetch_videos = $select_videos->fetch(PDO::FETCH_ASSOC)) {
                $video_id = htmlspecialchars($fetch_videos['id']);
                $status_color = ($fetch_videos['status'] == 'active') ? 'color:limegreen' : 'color:red';
        ?>
                <div class="box">
                    <div class="flex">
                        <div>
                            <i class="fas fa-dot-circle" style="<?= $status_color; ?>"></i>
                            <span style="<?= $status_color; ?>"><?= htmlspecialchars($fetch_videos['status']); ?></span>
                        </div>
                        <div>
                            <i class="fas fa-calendar"></i>
                            <span><?= htmlspecialchars($fetch_videos['date']); ?></span>
                        </div>
                    </div>
                    <img src="../uploaded_files/<?= htmlspecialchars($fetch_videos['thumb']); ?>" class="thumb" alt="Video Thumbnail">
                    <h3 class="title"><?= htmlspecialchars($fetch_videos['title']); ?></h3>
                    <form action="" method="post" class="flex-btn">
                        <input type="hidden" name="video_id" value="<?= $video_id; ?>">
                        <a href="update_content.php?get_id=<?= $video_id; ?>" class="option-btn">Update</a>
                        <input type="submit" value="Delete" class="delete-btn" onclick="return confirm('Delete this video?');" name="delete_video">
                    </form>
                    <a href="view_content.php?get_id=<?= $video_id; ?>" class="btn">View Content</a>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">No contents added yet!</p>';
        }
        ?>
    </div>
</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
