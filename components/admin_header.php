<?php
// Displaying messages if any exist (e.g., success or error messages)
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span> <!-- Message text -->
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i> <!-- Close button to remove the message -->
      </div>
      ';
   }
}
?>

<header class="header">

   <section class="flex">

      <!-- Logo that links to the dashboard -->
      <a href="dashboard.php" class="logo">Admin.</a>

      <!-- Search form -->
      <form action="search_page.php" method="post" class="search-form">
         <input type="text" name="search" placeholder="Search here..." required maxlength="100">
         <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>

      <div class="icons">
         <!-- Menu toggle button for mobile -->
         <div id="menu-btn" class="fas fa-bars"></div>
         <!-- Search toggle button -->
         <div id="search-btn" class="fas fa-search"></div>
         <!-- User profile button -->
         <div id="user-btn" class="fas fa-user"></div>
         <!-- Toggle theme button -->
         <div id="toggle-btn" class="fas fa-sun"></div>
      </div>

      <div class="profile">
         <?php
            // Fetching the tutor's profile details from the database using tutor_id
            $select_profile = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
            $select_profile->execute([$tutor_id]);
            if($select_profile->rowCount() > 0){
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <!-- Displaying tutor's profile information -->
         <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <h3><?= $fetch_profile['name']; ?></h3>
         <span><?= $fetch_profile['profession']; ?></span>
         <a href="profile.php" class="btn">View profile</a>

         <!-- Buttons for login and registration (if tutor is not logged in) -->
         <div class="flex-btn">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>
         <!-- Logout button -->
         <a href="../components/admin_logout.php" onclick="return confirm('logout from this website?');" class="delete-btn">logout</a>
         <?php
            }else{
         ?>
         <!-- Message when the user is not logged in -->
         <h3>Please Login or Register</h3>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>
         <?php
            }
         ?>
      </div>

   </section>

</header>

<!-- Header section ends -->

<!-- Sidebar section starts -->

<div class="side-bar">

   <!-- Close sidebar button -->
   <div class="close-side-bar">
      <i class="fas fa-times"></i>
   </div>

   <!-- Tutor's profile in the sidebar -->
   <div class="profile">
         <?php
            // Fetching the tutor's profile for the sidebar
            $select_profile = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
            $select_profile->execute([$tutor_id]);
            if($select_profile->rowCount() > 0){
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <!-- Displaying tutor's profile image and details -->
         <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="Profile Image">
         <h3><?= $fetch_profile['name']; ?></h3>
         <span><?= $fetch_profile['profession']; ?></span>
         <a href="profile.php" class="btn">View profile</a>
         <?php
            }else{
         ?>
         <!-- Message for users not logged in -->
         <h3>Please login or register</h3>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>
         <?php
            }
         ?>
      </div>

   <!-- Sidebar navigation links -->
   <nav class="navbar">
      <a href="dashboard.php"><i class="fas fa-home"></i><span>Home</span></a>
      <a href="playlists.php"><i class="fa-solid fa-bars-staggered"></i><span>Playlists</span></a>
      <a href="contents.php"><i class="fas fa-graduation-cap"></i><span>Contents</span></a>
      <a href="comments.php"><i class="fas fa-comment"></i><span>Comments</span></a>
      <!-- Logout link -->
      <a href="../components/admin_logout.php" onclick="return confirm('logout from this website?');"><i class="fas fa-right-from-bracket"></i><span>logout</span></a>
   </nav>

</div>

<!-- Sidebar section ends -->
