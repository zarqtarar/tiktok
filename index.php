<?php
session_start();

// Database connection
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "videos1";

$conn = mysqli_connect($hostname, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle search query
$searchTerm = "";
if (isset($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM videos WHERE title LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%'";
} else {
    // Fetch all videos if no search term is provided
    $query = "SELECT * FROM videos";
}

$result = mysqli_query($conn, $query);

// Get total number of videos
$totalVideos = mysqli_num_rows($result);
$randomIndex = rand(0, $totalVideos - 1);  // Get a random index to display a random video

// Fetthe videos into an array
$videos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $videos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiktok</title>
    <!-- Favicon for Tiktok -->
    <link rel="icon" href="tiktok-favicon.ico" type="image/x-icon">
    <!-- Google Font (Rubik) -->
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #000;
            overflow: hidden;
            height: 100vh;
        }

        /* Navbar Styling */
        .navbar {
            background: rgba(0, 0, 0, 0.7);
            position: absolute;
            width: 100%;
            z-index: 10;
            padding: 10px 0;
        }

        /* App Name (Tiktok) Styling */
        .navbar-brand {
            font-family: 'Rubik', sans-serif;
            font-size: 2.5rem;
            font-weight: bold;
            color: #fff;
            background: linear-gradient(45deg, #ff004f, #4c5af2);
            -webkit-background-clip: text;
            background-clip: text;
            text-shadow: 0 0 10px rgba(255, 0, 80, 0.7), 0 0 20px rgba(255, 0, 80, 0.7);
        }

        /* Flex container for aligning app name and search bar */
        .navbar .navbar-nav {
            flex-grow: 1;
            justify-content: flex-start; /* Align items to the left */
        }

        /* Search bar container */
        .navbar .search-form {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-form input {
            width: 250px;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 5px;
            margin-right: 10px;
        }

        .search-form button {
            background-color: #e50914;
            border: none;
            color: white;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #f40612;
        }

        .video-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100%;
            width: 100%;
            overflow: hidden;
            position: relative;
            padding-top: 100px; /* Added padding to move video down */
        }

        .video-card {
            position: relative;
            width: 35%;
            height: 80vh;
            background-color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            display: none;
            margin: 0 auto;
        }

        .video-frame {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .video-actions {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 5px;
            padding: 10px;
        }

        .action-button {
            color: white;
            background: #333;
            border: none;
            padding: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            width: 50px;
            text-align: center;
            font-size: 18px;
        }

        .action-button:hover {
            background-color: #e50914;
        }

        .arrow-up, .arrow-down {
            position: fixed;
            right: 20px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
            color: white;
            font-size: 24px;
            z-index: 20;
        }

        .arrow-up {
            top: 100px;
        }

        .arrow-down {
            bottom: 20px;
        }

        /* Add margin to move the Home, SignIn, and SignUp buttons more to the right */
        .navbar-nav.ml-auto {
            margin-left: auto;
            margin-right: 20px; /* Adjust this value to move it further */
        }

        /* Add space between Manage Videos and Upload Video */
        .manage-link {
            margin-left: 50px; /* Move the Manage Videos button more to the right */
            position: relative;
            top: 10px; /* Move it down to align with the Home button */
        }

        .upload-link-btn {
            margin-left: 30px; /* Maintain space between the buttons */
            position: relative;
            top: 10px; /* Move it down to align with the Home button */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">Tiktok</a>
        <div class="collapse navbar-collapse">
            <!-- Flex container for Navbar items -->
            <ul class="navbar-nav mx-auto">
                <!-- Align search form to the right of the navbar brand -->
                <li class="nav-item ml-auto">
                    <form class="form-inline search-form" method="GET" action="">
                        <input type="text" name="search" placeholder="Search..." value="<?php echo $searchTerm; ?>" autocomplete="off">
                        <button type="submit">Search</button>
                    </form>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <?php if (isset($_SESSION['username'])) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                    <?php if ($_SESSION['username'] == 'Admin') : ?>
                        <a class="upload-link manage-link" href="secure3.php">Manage Videos</a>
                    <?php endif; ?>
                    <a class="upload-link upload-link-btn" href="secure4.php">Upload Video</a>
                <?php else : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="signup.php" id="signup-link">Sign Up</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="signin_form.php" id="signin-link">Sign In</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Video Results -->
    <div class="video-container">
        <?php
        if ($result && $totalVideos > 0) {
            // Output all videos
            foreach ($videos as $index => $video) {
                echo '<div class="video-card" id="video-' . $index . '" ' . ($index == $randomIndex ? 'style="display:block;"' : '') . '>';
                echo '<video class="video-frame" controls autoplay loop>';
                echo '<source src="uploads/' . $video['filename'] . '" type="video/mp4">';
                echo 'Your browser does not support the video tag.';
                echo '</video>';

                // Like, Dislike, Comment buttons with Font Awesome icons (only icons, no text)
                echo '<div class="video-actions">';
                echo '<button class="action-button" id="like-' . $index . '"><i class="fas fa-thumbs-up"></i></button>';
                echo '<button class="action-button" id="dislike-' . $index . '"><i class="fas fa-thumbs-down"></i></button>';
                echo '<button class="action-button" id="comment-' . $index . '"><i class="fas fa-comment"></i></button>';
                echo '</div>';

                echo '</div>';
            }
        } else {
            echo '<div class="col-12"><p>No videos found.</p></div>';
        }
        ?>
    </div>

    <!-- Arrow Buttons -->
    <div class="arrow-up" onclick="showPreviousVideo()">↑</div>
    <div class="arrow-down" onclick="showNextVideo()">↓</div>

    <script>
        let currentIndex = <?php echo $randomIndex; ?>; // Set the initial random index
        const videos = document.querySelectorAll('.video-card');
        const totalVideos = videos.length;

        function showNextVideo() {
            // Pause all videos
            const allVideos = document.querySelectorAll('video');
            allVideos.forEach(video => video.pause());

            // Hide current video
            videos[currentIndex].style.display = 'none';

            // Move to the next video
            currentIndex = (currentIndex + 1) % totalVideos;

            // Show the next video
            videos[currentIndex].style.display = 'block';

            // Play the next video
            const nextVideo = videos[currentIndex].querySelector('video');
            nextVideo.play();
        }

        function showPreviousVideo() {
            // Pause all videos
            const allVideos = document.querySelectorAll('video');
            allVideos.forEach(video => video.pause());

            // Hide current video
            videos[currentIndex].style.display = 'none';

            // Move to the previous video
            currentIndex = (currentIndex - 1 + totalVideos) % totalVideos;

            // Show the previous video
            videos[currentIndex].style.display = 'block';

            // Play the previous video
            const previousVideo = videos[currentIndex].querySelector('video');
            previousVideo.play();
        }

        // Initialize by showing the first random video
        videos[currentIndex].style.display = 'block';

        // Set volume to 100% for all videos by default
        const allVideos = document.querySelectorAll('video');
        allVideos.forEach(video => {
            video.volume = 1; // Set the volume to max (100%)
            video.muted = false; // Ensure video is not muted
        });
    </script>
</body>

</html>
