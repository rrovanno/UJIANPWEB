<?php
session_start();

// Cek jika ada parameter 'code-rsvp' dalam URL
if(isset($_POST['code-rsvp'])){
    $code = $_POST['code-rsvp'];
    if($code == '123456'){
      // Simpan data ke sesi untuk digunakan di registration-view.php
      $_SESSION['valid_code'] = true;
        header('Location: registration-view.php');
        exit();
    } else {
      echo "<p>Salah Input Code RSVP</p>";
    }
}
    // Hapus session sebelumnya
    if(isset($_SESSION['guest_data'])){
      header("Location: home.php");
      unset($_SESSION['guest_data']);
      exit();
    }
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/userUI.css">
  <link rel="stylesheet" href="assets/home.css">
  <link rel="stylesheet" href="style/home-mobile.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&amp;family=Roboto&amp;display=swap"
    rel="stylesheet" />
  <title>Daftar Tamu Pernikahan</title>
</head>

<body>
  <!-- Audio element for background music -->
  <audio id="bg-music" autoplay loop>
    <source src="./assets/music/L-O-V-E.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
  </audio>

  <!-- Navbar -->
  <nav class="navbar">
    <ul>
      <li><a href="index.html"><i class="fas fa-home"></i>Home</a></li>
    </ul>
    <li><a href="#couple"><i class="fas fa-heart"></i>Couple</a></li>
    <li><a href="#information"><i class="fas fa-calendar-alt"></i>Events</a></li>
    <li><a href="#rsvp"><i class="fas fa-envelope"></i>Invitation</a></li>
    <li><a href="#comments"><i class="fas fa-comments"></i>Comments</a></li>
  </nav>
  <!-- Navbar -->

  <main>
    <!-- Content -->
    <section class="content">
      <h1>Assalamualaikum Wr. Wb</h1>
      <p>By love and the grace of love, we cordially invite you to attend the Wedding Celebration of</p>
    </section>
    <section id="couple">
      <div class="groom-bride-section">
        <img alt="Background image of the groom and bride" class="wedding-image" src="assets/img/womenNman.jpeg"
          width="1200" />
        <div class="overlay">
          <h2>Groom and Bride</h2>
          <div class="profile-wrap">
            <div class="profile">
              <img alt="Groom's profile picture" src="assets/img/man.jpeg" />
              <h3>Vitto</h3>
              <p>Son of Mr. M Anies Hasan & Mrs Eva Elisa Wibisono</p>
              <a href="" target="_blank"><i class="fab fa-instagram fa-fw"></i>@Aridhan_</a>
            </div>
            <div class="profile and">
              <span>&</span>
            </div>
            <div class="profile">
              <img alt="Bride's profile picture" src="assets/img/women.jpeg" />
              <h3>Salma</h3>
              <p>Daughter of Mr. Nirwan Iskandar (Alm) & Mrs Elivo Nasution</p>
              <a href="" target="_blank"><i class="fab fa-instagram fa-fw"></i>@rrvannoo</a>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Content -->


    <!-- Information -->
    <section id="information" class="information">
      <h2>Save The Date</h2>
      <h3 class="saturday">Saturday</h3>
      <div class="dates">
        <p class="date">18</p>
        <p class="month">January<br>2025</p>
      </div>
      <div class="information-wrapping">
        <article class="event">
          <h2 class="title">Akad</h2>
          <p class="time">09:30 - 11:00 <span>WIB</span></p>
          <button onclick="window.open('https://calendar.app.google/j6jXQb7xt2RjCh1b6', '_blank');"><i
              class="fas fa-calendar-alt"></i> Add to Calendar</button>
        </article>
        <article class="event">
          <h2 class="title">Reception</h2>
          <p class="time">12:00 - 14:30 <span>WIB</span></p>
          <button onclick="window.open('https://calendar.app.google/EkLF6TKKQzWv2ze2A', '_blank');"><i
              class="fas fa-calendar-alt"></i> Add to Calendar</button>
        </article>
        <article class="event">
          <h2 class="title">Wedding Celebration</h2>
          <h3>DRESS CODE : COCKTAIL ATTIRE</h3>
          <p class="time">19:30 - 22:00 <span>WIB</span></p>
          <button onclick="window.open('https://calendar.app.google/WA2NgAm9sss7dR1y5', '_blank');"><i
              class="fas fa-calendar-alt"></i> Add to Calendar</button>
        </article>
        <article class="location">
          <h2>The St. Regis Jakarta</h2>
          <p>Jalan Haji R. Rasuna Said 4, Setia Budi, Kecamatan Setiabudi</p>
          <p>Kota Jakarta Selatan</p>
          <button onclick="window.open('https://maps.google.com/?cid=8982988377651589278', '_blank');"><i
              class="fas fa-map-marker-alt"></i> View Map</button>
        </article>
      </div>
    </section>
    <!-- Information -->
    <!-- GALLERY -->
    <section class="gallery">
      <img alt="Couple holding hands, man in white shirt and woman in silver dress" height="400"
        src="./assets/img/banner-hero1.jpg" width="600" />
      <img alt="Couple in black attire, woman wearing pearl necklace" height="400" src="./assets/img/banner-hero2.jpg"
        width="600" />
      <img alt="Couple in black attire, woman wearing pearl necklace" height="400" src="./assets/img/banner-hero3.jpg"
        width="600" />
      <img alt="Woman in black dress" height="400" src="./assets/img/banner-hero3.jpg" width="600" />
      <img alt="Couple in black attire, woman wearing pearl necklace" height="400" src="./assets/img/banner-hero2.jpg"
        width="600" />
      <img alt="Couple holding hands, man in white shirt and woman in silver dress" height="400"
        src="./assets/img/banner-hero1.jpg" width="600" />
    </section>
    <!-- GALLERY -->
    <!-- RSVP -->
    <section id="rsvp">
      <article class="rsvp">
        <h1>RSVP</h1>
        <p>Put your 6 digits invitation code</p>
        <form method="post" class="rsvpButton">
          <input type="text" placeholder="Invitation Code" name="code-rsvp" required minlength="6">
          <button type="submit">RSVP</button>

        </form>

      </article>
      <article class="countdown-wrapping countdown">
        <h2>January 18<sup>th</sup>, 2025</h2>
        <div class="countdown-timer">
        <div>
            <span id="days">349</span>
            <p>Days</p>
        </div>
        <div>
            <span id="hours">9</span>
            <p>Hours</p>
        </div>
        <div>
            <span id="minutes">15</span>
            <p>Minutes</p>
        </div>
        <div>
            <span id="seconds">4</span>
            <p>Seconds</p>
        </div>
    </div>
      </article>
    </section>
    <!-- RSVP -->
    <!-- COMMENTS -->
    <section id="comments" class="comments">
      <h1>Your Warmest Wishes</h1>
          <!-- <form method="POST" action="../Model/post_comment.php">
            <div class="form-group">
              <input type="text" name="name" placeholder="Name" required>
            </div>
            <div class="form-group">
              <textarea name="text" placeholder="Say something..." required></textarea>
            </div>
            <button type="submit">Post Comment</button>
          </form> -->
      <div id="comment-list">
        <!-- Komentar yang ada akan ditampilkan di sini -->
        <?php
        require_once '../Config/db.php';
        $sql = "SELECT * FROM guests ORDER BY created_at DESC";
        $stmt = $pdo->query($sql);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($comments as $comment) {
          if($comment['notes'] != null){
            echo '<div class="comment">';
            echo '<div class="name">' . htmlspecialchars($comment['name']) . '</div>';
            echo '<div class="text">' . htmlspecialchars($comment['notes']) . '</div>';
            echo '<div class="time">' . date('d M Y H:i', strtotime($comment['created_at'])) . '</div>';
            echo '</div>';
          }else{
            echo "";
          }
        }
        ?>
      </div>
    </section>
    <!-- COMMENTS -->
    <!-- MUSIC -->
    <div class="music-control">
      <button id="play-button" class="music-button">
        <img src="./assets/img/play.png" alt="Play Music">
      </button>
      <button id="stop-button" class="hide music-button">
        <img src="./assets/img/stop.png" alt="Stop Music">
      </button>
    </div>
    <!-- MUSIC -->
  </main>
  <footer></footer>
  <script src="assets/javascript/index.js"></script>
  <script src="assets/javascript/home.js"></script>
</body>

</html>