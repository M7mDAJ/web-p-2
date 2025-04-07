<?php
// Display messages if available
if (isset($message)) {
    foreach ($message as $msg) {
        echo "<div class='message'>
                <span>" . htmlspecialchars($msg) . "</span>
                <i class='fas fa-times' onclick='this.parentElement.remove();'></i>
              </div>";
    }
}

// Fetch user profile information
$fetch_profile = null;
if (isset($user_id)) {
    $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $select_profile->execute([$user_id]);
    if ($select_profile->rowCount() > 0) {
        $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<header class="header">
    <section class="flex">
        <!-- Website Logo -->
        <a href="home.php" class="logo">Educa.</a>

        <!-- Search Form -->
        <form action="search_course.php" method="post" class="search-form">
            <input type="text" name="search_course" placeholder="Search courses..." required maxlength="100">
            <button type="submit" class="fas fa-search" name="search_course_btn"></button>
        </form>

        <!-- Header Icons -->
        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="search-btn" class="fas fa-search"></div>
            <div id="user-btn" class="fas fa-user"></div>
            <div id="toggle-btn" class="fas fa-sun"></div>
        </div>

        <!-- User Profile Section -->
        <div class="profile">
            <?php if ($fetch_profile) { ?>
                <img src="uploaded_files/<?= htmlspecialchars($fetch_profile['image']); ?>" alt="Profile Picture">
                <h3><?= htmlspecialchars($fetch_profile['name']); ?></h3>
                <span>Student</span>
                <a href="profile.php" class="btn">View Profile</a>
                <div class="flex-btn">
                    <a href="login.php" class="option-btn">Login</a>
                    <a href="register.php" class="option-btn">Register</a>
                </div>
                <a href="components/user_logout.php" onclick="return confirm('Logout from this website?');" class="delete-btn">Logout</a>
            <?php } else { ?>
                <h3>Please Login or Register</h3>
                <div class="flex-btn">
                    <a href="login.php" class="option-btn">Login</a>
                    <a href="register.php" class="option-btn">Register</a>
                </div>
            <?php } ?>
        </div>
    </section>
</header>

<!-- Sidebar Section -->
<div class="side-bar">
    <div class="close-side-bar">
        <i class="fas fa-times"></i>
    </div>

    <!-- User Profile in Sidebar -->
    <div class="profile">
        <?php if ($fetch_profile) { ?>
            <img src="uploaded_files/<?= htmlspecialchars($fetch_profile['image']); ?>" alt="Profile Picture">
            <h3><?= htmlspecialchars($fetch_profile['name']); ?></h3>
            <span>Student</span>
            <a href="profile.php" class="btn">View Profile</a>
        <?php } else { ?>
            <h3>Please Login or Register</h3>
            <div class="flex-btn" style="padding-top: .5rem;">
                <a href="login.php" class="option-btn">Login</a>
                <a href="register.php" class="option-btn">Register</a>
            </div>
        <?php } ?>
    </div>

    <!-- Sidebar Navigation Menu -->
    <nav class="navbar">
        <a href="home.php"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="about.php"><i class="fas fa-question"></i><span>About Us</span></a>
        <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
        <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span>Teachers</span></a>
        <a href="contact.php"><i class="fas fa-headset"></i><span>Contact Us</span></a>
    </nav>
</div>
