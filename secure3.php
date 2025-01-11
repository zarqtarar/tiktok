<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: signin_form.php");
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "videos1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle video upload
if (isset($_POST['upload_video'])) {
    // Handle video upload
    $title = $_POST['title'];
    $description = $_POST['description'];
    $publisher = $_POST['publisher'];
    $producer = $_POST['producer'];
    $genre = $_POST['genre'];
    $ageRating = $_POST['age_rating'];

    // File upload handling for video
    $video_target_dir = "uploads/";
    $video_target_file = $video_target_dir . basename($_FILES["fileToUpload"]["name"]);
    $video_uploadOk = 1;
    $videoFileType = strtolower(pathinfo($video_target_file, PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($video_target_file)) {
        echo "Sorry, video file already exists.";
        $video_uploadOk = 0;
    }

    // Check file size for video
    if ($_FILES["fileToUpload"]["size"] > 50000000) {
        echo "Sorry, your video file is too large.";
        $video_uploadOk = 0;
    }

    // Allow certain file formats for video
    $allowedVideoFormats = array("mp4", "avi", "mov");
    if (!in_array($videoFileType, $allowedVideoFormats)) {
        echo "Sorry, only MP4, AVI, and MOV files are allowed for video.";
        $video_uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error for video
    if ($video_uploadOk == 0) {
        echo "Sorry, your video file was not uploaded.";
    } else {
        // if everything is ok, try to upload the video file
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $video_target_file)) {
            // Insert video details into database
            $video_filename = basename($_FILES["fileToUpload"]["name"]);
            $uploader_id = $_SESSION['id'];
            $upload_datetime = date("Y-m-d H:i:s"); // Current date and time

            $sql = "INSERT INTO videos (title, description, publisher, producer, genre, AgeRating, filename, uploader_id, upload_datetime) 
                    VALUES ('$title', '$description', '$publisher', '$producer', '$genre', '$ageRating', '$video_filename', '$uploader_id', '$upload_datetime')";

            if (mysqli_query($conn, $sql)) {
                echo "The video file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "Sorry, there was an error uploading your video.";
        }
    }
}

// Fetch genres from the Genres table
$genre_query = "SELECT genre_name FROM Genres";
$genre_result = mysqli_query($conn, $genre_query);
$genres = array();
while ($row = mysqli_fetch_assoc($genre_result)) {
    $genres[] = $row['genre_name'];
}

// Fetch all age ratings from the AgeRating table
$age_rating_query = "SELECT rating_name FROM AgeRating";
$age_rating_result = mysqli_query($conn, $age_rating_query);
$age_ratings = array();
while ($row = mysqli_fetch_assoc($age_rating_result)) {
    $age_ratings[] = $row['rating_name'];
}

// Delete selected videos
if (isset($_POST['delete_videos'])) {
    if (isset($_POST['videos']) && !empty($_POST['videos'])) {
        $videos_to_delete = $_POST['videos'];
        foreach ($videos_to_delete as $video_id) {
            // Delete associated likes
            mysqli_query($conn, "DELETE FROM Likes WHERE video_id = $video_id");
            
            // Delete associated dislikes
            mysqli_query($conn, "DELETE FROM Dislikes WHERE video_id = $video_id");
            
            // Delete associated comments
            mysqli_query($conn, "DELETE FROM Comments WHERE video_id = $video_id");
            
            // Fetch video filename
            $file_query = "SELECT filename FROM videos WHERE id = $video_id";
            $file_result = mysqli_query($conn, $file_query);
            $file_row = mysqli_fetch_assoc($file_result);
            $video_filename = $file_row['filename'];
            
            // Delete video file
            $video_path = "uploads/" . $video_filename;
            if (file_exists($video_path)) {
                unlink($video_path);
            }
            
            // Delete video entry from database
            mysqli_query($conn, "DELETE FROM videos WHERE id = $video_id");
        }
        echo "Selected videos along with their associated likes, dislikes, comments, and files have been deleted.";
    } else {
        echo "No videos selected for deletion.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .logout {
            float: right;
        }

        .video-link {
            display: block;
            margin-bottom: 10px;
        }

        .video-container {
            margin-top: 20px;
        }

        .upload-container {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .nav-tabs .nav-link.active {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }

        .nav-tabs .nav-link {
            color: #007bff;
            border: 1px solid transparent;
            border-top-left-radius: .25rem;
            border-top-right-radius: .25rem;
            margin-right: 2px;
            line-height: 1.5;
            padding: .5rem .75rem;
        }

        .nav-tabs {
            border-bottom: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#">Secure Page</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
        <div class="row">
            <div class="col-md-6">
                <!-- Dropdown menu to select videos -->
                <form action="" method="GET" class="mb-3">
                    <label for="view-option">View Videos:</label>
                    <select name="view-option" id="view-option" class="form-control">
                        <option value="all">All Videos</option>
                        <option value="uploaded-by-me">Uploaded by Me</option>
                    </select>
                    <button type="submit" class="btn btn-primary mt-2">View</button>
                </form>

                <!-- Uploaded videos -->
                <div class="video-container">
                    <h3>Uploaded Videos</h3>
                    <form action="" method="POST">
                        <?php
                        // Fetch uploaded videos from the database based on the selected option
                        $view_option = isset($_GET['view-option']) ? $_GET['view-option'] : 'all';

                        if ($view_option === 'all') {
                            $query = "SELECT * FROM videos";
                        } else {
                            $id = $_SESSION['id'];
                            $query = "SELECT * FROM videos WHERE uploader_id = $id";
                        }

                        $result = mysqli_query($conn, $query); // Define $result here

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<div><input type='checkbox' name='videos[]' value='" . $row['id'] . "'><a href='view_video.php?id=" . $row['id'] . "' class='video-link'>" . $row['title'] . "</a></div>";
                            }
                        } else {
                            echo "No videos uploaded yet.";
                        }
                        ?>
                        <button type="submit" class="btn btn-danger mt-2" name="delete_videos">Delete Selected Videos</button>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Upload form for video -->
                <div class="upload-container">
                    <h3>Upload a New Video</h3>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="title">Video Title:</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="publisher">Publisher:</label>
                            <input type="text" name="publisher" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="producer">Producer:</label>
                            <input type="text" name="producer" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="genre">Genre:</label>
                            <select name="genre" class="form-control" required>
                                <?php foreach ($genres as $genre): ?>
                                    <option value="<?php echo $genre; ?>"><?php echo $genre; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="age_rating">Age Rating:</label>
                            <select name="age_rating" class="form-control" required>
                                <?php foreach ($age_ratings as $rating): ?>
                                    <option value="<?php echo $rating; ?>"><?php echo $rating; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fileToUpload">Video File:</label>
                            <input type="file" name="fileToUpload" class="form-control-file" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="upload_video">Upload Video</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
