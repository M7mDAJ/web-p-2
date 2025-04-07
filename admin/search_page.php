<?php

include '../components/connect.php';

session_start();
if (!isset($_SESSION['tutor_id'])) {
    header('location:login.php');
    exit();
}

$tutor_id = $_SESSION['tutor_id'];

if (isset($_POST['delete_video'])) {
    $delete_id = filter_var($_POST['video_id'], FILTER_SANITIZE_STRING);
    
    $verify_video = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND tutor_id = ? LIMIT 1");
    $verify_video->execute([$delete_id, $tutor_id]);
    $video = $verify_video->fetch(PDO::FETCH_ASSOC);
    
    if ($video) {
        if (!empty($video['thumb']) && file_exists('../uploaded_files/' . $video['thumb'])) {
            unlink('../uploaded_files/' . $video['thumb']);
        }
        if (!empty($video['video']) && file_exists('../uploaded_files/' . $video['video'])) {
            unlink('../uploaded_files/' . $video['video']);
        }
        $conn->prepare("DELETE FROM `likes` WHERE content_id = ?")->execute([$delete_id]);
        $conn->prepare("DELETE FROM `comments` WHERE content_id = ?")->execute([$delete_id]);
        $conn->prepare("DELETE FROM `content` WHERE id = ?")->execute([$delete_id]);
        $message[] = 'Video deleted!';
    } else {
        $message[] = 'Video not found or already deleted!';
    }
}

if (isset($_POST['delete_playlist'])) {
    $delete_id = filter_var($_POST['playlist_id'], FILTER_SANITIZE_STRING);
    
    $verify_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? AND tutor_id = ? LIMIT 1");
    $verify_playlist->execute([$delete_id, $tutor_id]);
    $playlist = $verify_playlist->fetch(PDO::FETCH_ASSOC);
    
    if ($playlist) {
        if (!empty($playlist['thumb']) && file_exists('../uploaded_files/' . $playlist['thumb'])) {
            unlink('../uploaded_files/' . $playlist['thumb']);
        }
        $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?")->execute([$delete_id]);
        $conn->prepare("DELETE FROM `playlist` WHERE id = ?")->execute([$delete_id]);
        $message[] = 'Playlist deleted!';
    } else {
        $message[] = 'Playlist not found or already deleted!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="contents">
    <h1 class="heading">Contents</h1>
    <div class="box-container">
    <?php
    if (!empty($_POST['search'])) {
        $search = '%' . $_POST['search'] . '%';
        $select_videos = $conn->prepare("SELECT * FROM `content` WHERE title LIKE ? AND tutor_id = ? ORDER BY date DESC");
        $select_videos->execute([$search, $tutor_id]);
        if ($select_videos->rowCount() > 0) {
            while ($video = $select_videos->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='box'>";
                echo "<h3 class='title'>{$video['title']}</h3>";
                echo "<form method='post'>";
                echo "<input type='hidden' name='video_id' value='{$video['id']}'>";
                echo "<a href='update_content.php?get_id={$video['id']}' class='option-btn'>Update</a>";
                echo "<input type='submit' value='Delete' class='delete-btn' name='delete_video' onclick='return confirm(\"Delete this video?\");'>";
                echo "</form>";
                echo "<a href='view_content.php?get_id={$video['id']}' class='btn'>View content</a>";
                echo "</div>";
            }
        } else {
            echo "<p class='empty'>No contents found!</p>";
        }
    }
    ?>
    </div>
</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>
</body>
</html>
