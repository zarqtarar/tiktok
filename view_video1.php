<?php
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: signin_form.php");
    exit();
}

// Redirect if filename is not provided
if (!isset($_GET['filename']) || empty($_GET['filename'])) {
    header("Location: secure.php");
    exit();
}

$filename = $_GET['filename'];

// Database connection
$conn = mysqli_connect("localhost", "root", "", "videos1");

// Fetch video details based on filename
$query = "SELECT * FROM videos WHERE filename = '$filename'";
$result = mysqli_query($conn, $query);

// Check if video exists
if (mysqli_num_rows($result) == 1) {
    $video = mysqli_fetch_assoc($result);
} else {
    echo "Video not found.";
    exit();
}

$videoId = $video['id'];
$userId = $_SESSION['id'];

// Fetch existing comments for the video
$fetchCommentsQuery = "SELECT comments.*, users.username, DATE_FORMAT(upload_datetime, '%W, %M %e, %Y, %l:%i %p') AS formatted_datetime
                       FROM comments 
                       INNER JOIN users ON comments.commenter_id = users.id
                       WHERE comments.video_id = $videoId
                       ORDER BY comments.upload_datetime DESC";
$commentsResult = mysqli_query($conn, $fetchCommentsQuery);

// Check if user has liked or disliked the video
$checkLikeQuery = "SELECT * FROM likes WHERE video_id = $videoId AND user_id = $userId";
$checkDislikeQuery = "SELECT * FROM dislikes WHERE video_id = $videoId AND user_id = $userId";

$hasLiked = mysqli_num_rows(mysqli_query($conn, $checkLikeQuery)) > 0;
$hasDisliked = mysqli_num_rows(mysqli_query($conn, $checkDislikeQuery)) > 0;

// Count total likes and dislikes
$countLikesQuery = "SELECT COUNT(*) AS total_likes FROM likes WHERE video_id = $videoId";
$countDislikesQuery = "SELECT COUNT(*) AS total_dislikes FROM dislikes WHERE video_id = $videoId";

$totalLikesResult = mysqli_query($conn, $countLikesQuery);
$totalLikes = mysqli_fetch_assoc($totalLikesResult)['total_likes'];

$totalDislikesResult = mysqli_query($conn, $countDislikesQuery);
$totalDislikes = mysqli_fetch_assoc($totalDislikesResult)['total_dislikes'];

// Process form submission to add new comment
if (isset($_POST['submit_comment'])) {
    $comment = $_POST['comment'];
    $commenter_id = $_SESSION['id'];

    $insertCommentQuery = "INSERT INTO comments (video_id, commenter_id, comment, upload_datetime) 
                       VALUES ($videoId, $commenter_id, '$comment', NOW())";

    if (mysqli_query($conn, $insertCommentQuery)) {
        // Redirect to prevent form resubmission
        header("Location: view_video1.php?filename=$filename");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle like and dislike submission
if (isset($_POST['like'])) {
    if (!$hasLiked) {
        // If the user has previously disliked, remove the dislike
        if ($hasDisliked) {
            $deleteDislikeQuery = "DELETE FROM dislikes WHERE video_id = $videoId AND user_id = $userId";
            mysqli_query($conn, $deleteDislikeQuery);
        }
        $likeQuery = "INSERT INTO likes (video_id, user_id) VALUES ($videoId, $userId)";
        mysqli_query($conn, $likeQuery);
        // Reload the page to update the button status
        header("Location: view_video1.php?filename=$filename");
        exit();
    } else {
        // If the user has already liked, remove the like
        $deleteLikeQuery = "DELETE FROM likes WHERE video_id = $videoId AND user_id = $userId";
        mysqli_query($conn, $deleteLikeQuery);
        // Reload the page to update the button status
        header("Location: view_video1.php?filename=$filename");
        exit();
    }
}

if (isset($_POST['dislike'])) {
    if (!$hasDisliked) {
        // If the user has previously liked, remove the like
        if ($hasLiked) {
            $deleteLikeQuery = "DELETE FROM likes WHERE video_id = $videoId AND user_id = $userId";
            mysqli_query($conn, $deleteLikeQuery);
        }
        $dislikeQuery = "INSERT INTO dislikes (video_id, user_id) VALUES ($videoId, $userId)";
        mysqli_query($conn, $dislikeQuery);
        // Reload the page to update the button status
        header("Location: view_video1.php?filename=$filename");
        exit();
    } else {
        // If the user has already disliked, remove the dislike
        $deleteDislikeQuery = "DELETE FROM dislikes WHERE video_id = $videoId AND user_id = $userId";
        mysqli_query($conn, $deleteDislikeQuery);
        // Reload the page to update the button status
        header("Location: view_video1.php?filename=$filename");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Video</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        header {
            background: #333;
            padding: 20px;
            text-align: center;
        }

        header a {
            color: white;
            text-decoration: none;
            font-size: 20px;
            font-weight: 500;
        }

        .container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .video-title {
            font-size: 36px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }

        .video-description {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }

        .video-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .video-container video {
            width: 100%;
            max-width: 800px;
            border-radius: 10px;
        }

        .like-dislike-btns {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .like,
        .dislike {
            padding: 12px 24px;
            font-size: 18px;
            cursor: pointer;
            border: 1px solid transparent;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .like {
            background-color: #28a745;
            color: white;
        }

        .dislike {
            background-color: #dc3545;
            color: white;
        }

        .like:hover {
            background-color: #218838;
        }

        .dislike:hover {
            background-color: #c82333;
        }

        .comments-section {
            margin-bottom: 40px;
        }

        .comment-card {
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .comment-card p {
            font-size: 16px;
            line-height: 1.5;
        }

        .comment-card strong {
            font-weight: 600;
            color: #333;
        }

        .comment-form textarea {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
            resize: vertical;
            margin-bottom: 20px;
        }

        .comment-form button {
            background-color: #2575fc;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .comment-form button:hover {
            background-color: #1e60d3;
        }
    </style>
</head>

<body>

<header>
    <a href="index.php">Back to Home</a>
</header>

<div class="container">
    <h1 class="video-title"><?php echo $video['title']; ?></h1>
    <p class="video-description"><?php echo $video['description']; ?></p>

    <div class="video-container">
        <video controls>
            <source src="uploads/<?php echo $filename; ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <!-- Like and Dislike Buttons -->
    <div class="like-dislike-btns">
        <form method="POST">
            <button type="submit" name="like" class="like <?php echo $hasLiked ? 'liked' : ''; ?>">Like</button>
            <span><?php echo $totalLikes; ?> Likes</span>
            <button type="submit" name="dislike" class="dislike <?php echo $hasDisliked ? 'disliked' : ''; ?>">Dislike</button>
            <span><?php echo $totalDislikes; ?> Dislikes</span>
        </form>
    </div>

    <!-- Comments Section -->
    <div class="comments-section">
        <h3>Comments</h3>
        <?php
        if (mysqli_num_rows($commentsResult) > 0) {
            while ($comment = mysqli_fetch_assoc($commentsResult)) {
                echo "<div class='comment-card'>";
                echo "<strong>" . htmlspecialchars($comment['username']) . "</strong> - " . $comment['formatted_datetime'];
                echo "<p>" . htmlspecialchars($comment['comment']) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>No comments yet. Be the first to comment!</p>";
        }
        ?>
    </div>

    <!-- Add a Comment Form -->
    <div class="comment-form">
        <form action="" method="POST">
            <textarea name="comment" placeholder="Add a comment..." rows="4" required></textarea>
            <button type="submit" name="submit_comment">Post Comment</button>
        </form>
    </div>

</div>

</body>

</html>
