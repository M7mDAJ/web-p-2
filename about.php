<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Meta information about the webpage -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>about us</title>

    <!-- Font Awesome for icons -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css"
    />

    <!-- Link to the custom CSS file -->
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>

<?php include 'components/user_header.php'; ?>

<!-- About section -->
<section class="about">
      <div class="row">
        <!-- Image section -->
        <div class="image">
          <img src="images/about-img.svg" alt="" />
        </div>

        <!-- Content section -->
        <div class="content">
          <h3>why choose us?</h3>
          <p>
            Lorem ipsum dolor sit amet consectetur, adipisicing elit. Ut dolorum
            quasi illo? Distinctio expedita commodi, nemo a quam error
            repellendus sint, fugiat quis numquam eum eveniet sequi aspernatur
            quaerat tenetur.
          </p>
          <a href="courses.html" class="inline-btn">our courses</a>
        </div>
      </div>

         <!-- Statistical highlights -->
         <div class="box-container">
        <div class="box">
          <i class="fas fa-graduation-cap"></i>
          <div>
            <h3>+10k</h3>
            <p>online courses</p>
          </div>
        </div>

        <div class="box">
          <i class="fas fa-user-graduate"></i>
          <div>
            <h3>+40k</h3>
            <p>brilliant students</p>
          </div>
        </div>

        <div class="box">
          <i class="fas fa-chalkboard-user"></i>
          <div>
            <h3>+2k</h3>
            <p>expert tutors</p>
          </div>
        </div>

        <div class="box">
          <i class="fas fa-briefcase"></i>
          <div>
            <h3>100%</h3>
            <p>job placement</p>
          </div>
        </div>
      </div>
    </section>

<!-- about section ends -->

<!-- Reviews section -->
<section class="reviews">
      <h1 class="heading">student's reviews</h1>
    
      <!-- Container for all review boxes -->
      <div class="box-container">
        <!-- Review 1 -->
        <div class="box">
          <p>
            Lorem ipsum dolor sit amet consectetur, adipisicing elit.
            Necessitatibus, suscipit a. Quibusdam, dignissimos consectetur. Sed
            ullam iusto eveniet qui aut quibusdam vero voluptate libero facilis
            fuga. Eligendi eaque molestiae modi?
          </p>
          <div class="student">
            <img src="images/pic-2.jpg" alt="" />
            <div>
              <h3>john deo</h3>
              <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
              </div>
            </div>
          </div>
        </div>

      <!-- Review 2 -->
      <div class="box">
          <p>
            Lorem ipsum dolor sit amet consectetur, adipisicing elit.
            Necessitatibus, suscipit a. Quibusdam, dignissimos consectetur. Sed
            ullam iusto eveniet qui aut quibusdam vero voluptate libero facilis
            fuga. Eligendi eaque molestiae modi?
          </p>
          <div class="student">
            <img src="images/pic-3.jpg" alt="" />
            <div>
              <h3>john deo</h3>
              <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
              </div>
            </div>
          </div>
        </div>

      <!-- Review 3 -->
      <div class="box">
          <p>
            Lorem ipsum dolor sit amet consectetur, adipisicing elit.
            Necessitatibus, suscipit a. Quibusdam, dignissimos consectetur. Sed
            ullam iusto eveniet qui aut quibusdam vero voluptate libero facilis
            fuga. Eligendi eaque molestiae modi?
          </p>
          <div class="student">
            <img src="images/pic-4.jpg" alt="" />
            <div>
              <h3>john deo</h3>
              <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
              </div>
            </div>
          </div>
        </div>

      <!-- Review 4 -->
      <div class="box">
          <p>
            Lorem ipsum dolor sit amet consectetur, adipisicing elit.
            Necessitatibus, suscipit a. Quibusdam, dignissimos consectetur. Sed
            ullam iusto eveniet qui aut quibusdam vero voluptate libero facilis
            fuga. Eligendi eaque molestiae modi?
          </p>
          <div class="student">
            <img src="images/pic-5.jpg" alt="" />
            <div>
              <h3>john deo</h3>
              <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
              </div>
            </div>
          </div>
        </div>

       <!-- Review 5 -->
       <div class="box">
          <p>
            Lorem ipsum dolor sit amet consectetur, adipisicing elit.
            Necessitatibus, suscipit a. Quibusdam, dignissimos consectetur. Sed
            ullam iusto eveniet qui aut quibusdam vero voluptate libero facilis
            fuga. Eligendi eaque molestiae modi?
          </p>
          <div class="student">
            <img src="images/pic-6.jpg" alt="" />
            <div>
              <h3>john deo</h3>
              <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
              </div>
            </div>
          </div>
        </div>

      <!-- Review 6 -->
      <div class="box">
          <p>
            Lorem ipsum dolor sit amet consectetur, adipisicing elit.
            Necessitatibus, suscipit a. Quibusdam, dignissimos consectetur. Sed
            ullam iusto eveniet qui aut quibusdam vero voluptate libero facilis
            fuga. Eligendi eaque molestiae modi?
          </p>
          <div class="student">
            <img src="images/pic-7.jpg" alt="" />
            <div>
              <h3>john deo</h3>
              <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

<!-- reviews section ends -->


<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>