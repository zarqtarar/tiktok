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

// Handle video and thumbnail upload
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

    // File upload handling for thumbnail
    $thumbnail_target_dir = "uploads/";
    $thumbnail_target_file = $thumbnail_target_dir . basename($_FILES["thumbnailToUpload"]["name"]);
    $thumbnail_uploadOk = 1;
    $thumbnailFileType = strtolower(pathinfo($thumbnail_target_file, PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($video_target_file)) {
        echo "Sorry, video file already exists.";
        $video_uploadOk = 0;
    }
    if (file_exists($thumbnail_target_file)) {
        echo "Sorry, thumbnail image file already exists.";
        $thumbnail_uploadOk = 0;
    }

    // Check file size for video
    if ($_FILES["fileToUpload"]["size"] > 50000000) {
        echo "Sorry, your video file is too large.";
        $video_uploadOk = 0;
    }
    // Check file size for thumbnail
    if ($_FILES["thumbnailToUpload"]["size"] > 5000000) {
        echo "Sorry, your thumbnail image file is too large.";
        $thumbnail_uploadOk = 0;
    }

    // Allow certain file formats for video
    $allowedVideoFormats = array("mp4", "avi", "mp3", "mov", "pdf", "docx", "png", "jpg");
    if (!in_array($videoFileType, $allowedVideoFormats)) {
        echo "Sorry, only MP4, AVI, MOV, PDF, and DOCX files are allowed for video.";
        $video_uploadOk = 0;
    }
    // Allow certain file formats for thumbnail
    $allowedThumbnailFormats = array("jpg", "jpeg", "png", "gif");
    if (!in_array($thumbnailFileType, $allowedThumbnailFormats)) {
        echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed for thumbnail image.";
        $thumbnail_uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error for video
    if ($video_uploadOk == 0) {
        echo "Sorry, your video file was not uploaded.";
    } elseif ($thumbnail_uploadOk == 0) {
        echo "Sorry, your thumbnail image file was not uploaded.";
    } else {
        // if everything is ok, try to upload files
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $video_target_file) && move_uploaded_file($_FILES["thumbnailToUpload"]["tmp_name"], $thumbnail_target_file)) {
            // Insert video details into database
            $video_filename = basename($_FILES["fileToUpload"]["name"]);
            $thumbnail_filename = basename($_FILES["thumbnailToUpload"]["name"]);
            $uploader_id = $_SESSION['id'];
            $upload_datetime = date("Y-m-d H:i:s"); // Current date and time

            $sql = "INSERT INTO videos (title, description, publisher, producer, genre, AgeRating, filename, thumbnail, uploader_id, upload_datetime) 
                    VALUES ('$title', '$description', '$publisher', '$producer', '$genre', '$ageRating', '$video_filename', '$thumbnail_filename', '$uploader_id', '$upload_datetime')";

            if (mysqli_query($conn, $sql)) {
                echo "The video file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " and thumbnail image file " . htmlspecialchars(basename($_FILES["thumbnailToUpload"]["name"])) . " have been uploaded.";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "Sorry, there was an error uploading your files.";
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
            
            // Fetch video and thumbnail filenames
            $file_query = "SELECT filename, thumbnail FROM videos WHERE id = $video_id";
            $file_result = mysqli_query($conn, $file_query);
            $file_row = mysqli_fetch_assoc($file_result);
            $video_filename = $file_row['filename'];
            $thumbnail_filename = $file_row['thumbnail'];
            
            // Delete video file
            $video_path = "uploads/" . $video_filename;
            if (file_exists($video_path)) {
                unlink($video_path);
            }
            
            // Delete thumbnail file
            $thumbnail_path = "uploads/" . $thumbnail_filename;
            if (file_exists($thumbnail_path)) {
                unlink($thumbnail_path);
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
    <title>Upload videos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #333333; /* Dark gray background */
            color: #f4f4f4; /* Light text color */
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
            padding: 30px;
            border: 1px solid #444;
            border-radius: 8px;
            background-color: #555;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .nav-tabs .nav-link.active {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }

        .nav-tabs .nav-link {
            color: #007bff;
            border: 1px solid transparent;
            border-radius: .25rem;
            margin-right: 2px;
            line-height: 1.5;
            padding: .5rem .75rem;
        }

        .nav-tabs {
            border-bottom: 1px solid #dee2e6;
        }

        .form-group label {
            font-weight: bold;
            color: #ccc; /* Light color for labels */
        }

        .form-control {
            border-radius: 5px;
            box-shadow: none;
            border: 1px solid #666;
            background-color: #444; /* Darker background for input fields */
            color: #ddd; /* Lighter text inside input fields */
        }

        .form-control-file {
            padding: 8px;
            border-radius: 5px;
            background-color: #444;
            color: #ddd;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #f0f0f0;
            color: #333;
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
                
            </div>

            <div class="col-md-6">
                <!-- Upload form -->
                <div class="upload-container">
                    <h3>Upload Video</h3>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" class="form-control" id="title" name="title">
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="publisher">Publisher:</label>
                            <input type="text" class="form-control" id="publisher" name="publisher">
                        </div>
                        <div class="form-group">
                            <label for="producer">Producer:</label>
                            <input type="text" class="form-control" id="producer" name="producer">
                        </div>
                        <div class="form-group">
                            <label for="genre">Genre:</label>
                            <select class="form-control" id="genre" name="genre">
                                <?php foreach ($genres as $genre) : ?>
                                    <option value="<?php echo $genre; ?>"><?php echo $genre; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="age_rating">Age Rating:</label>
                            <select class="form-control" id="age_rating" name="age_rating">
                                <option value="">Select Age Rating</option>
                                <?php foreach ($age_ratings as $age_rating) : ?>
                                    <option value="<?php echo $age_rating; ?>"><?php echo $age_rating; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fileToUpload">Select video to upload:</label>
                            <input type="file" class="form-control-file" name="fileToUpload" id="fileToUpload">
                        </div>
                        <div class="form-group">
                            <label for="thumbnailToUpload">Select thumbnail image to upload:</label>
                            <input type="file" class="form-control-file" name="thumbnailToUpload" id="thumbnailToUpload">
                        </div>
                        <button type="submit" class="btn btn-primary" name="upload_video">Upload Video</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<?php
mysqli_close($conn);
?>
