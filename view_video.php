<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: signin_form.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: secure.php");
    exit();
}

$id = $_GET['id'];

$conn = mysqli_connect("localhost", "root", "", "videos1");
$query = "SELECT * FROM videos WHERE id = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 1) {
    $video = mysqli_fetch_assoc($result);
} else {
    echo "Video not found.";
    exit();
}

$userId = $_SESSION['id'];

// Fetch existing comments for the video
$fetchCommentsQuery = "SELECT comments.*, users.username, DATE_FORMAT(upload_datetime, '%W, %M %e, %Y, %l:%i %p') AS formatted_datetime
                       FROM comments 
                       INNER JOIN users ON comments.commenter_id = users.id
                       WHERE comments.video_id = $id
                       ORDER BY comments.upload_datetime DESC";
$commentsResult = mysqli_query($conn, $fetchCommentsQuery);

// Check if user has liked or disliked the video
$checkLikeQuery = "SELECT * FROM likes WHERE video_id = $id AND user_id = $userId";
$checkDislikeQuery = "SELECT * FROM dislikes WHERE video_id = $id AND user_id = $userId";

$hasLiked = mysqli_num_rows(mysqli_query($conn, $checkLikeQuery)) > 0;
$hasDisliked = mysqli_num_rows(mysqli_query($conn, $checkDislikeQuery)) > 0;

// Count total likes and dislikes
$countLikesQuery = "SELECT COUNT(*) AS total_likes FROM likes WHERE video_id = $id";
$countDislikesQuery = "SELECT COUNT(*) AS total_dislikes FROM dislikes WHERE video_id = $id";

$totalLikesResult = mysqli_query($conn, $countLikesQuery);
$totalLikes = mysqli_fetch_assoc($totalLikesResult)['total_likes'];

$totalDislikesResult = mysqli_query($conn, $countDislikesQuery);
$totalDislikes = mysqli_fetch_assoc($totalDislikesResult)['total_dislikes'];

// Process form submission to add new comment
if (isset($_POST['submit_comment'])) {
    $comment = $_POST['comment'];
    $commenter_id = $_SESSION['id'];

    $insertCommentQuery = "INSERT INTO comments (video_id, commenter_id, comment, upload_datetime) 
                       VALUES ($id, $commenter_id, '$comment', NOW())";

    if (mysqli_query($conn, $insertCommentQuery)) {
        // Redirect to prevent form resubmission
        header("Location: view_video.php?id=$id");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
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
            background-color: white; /* White background */
            color: #333; /* Dark text */
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4; /* Light gray container */
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 2.5em;
            margin: 10px 0;
            color: #333;
        }

        p {
            font-size: 1.2em;
            color: #555;
        }

        video {
            width: 100%;
            max-width: 900px; /* Increased width for larger video frame */
            height: auto;
            border-radius: 10px;
            margin-top: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .like, .dislike {
            background-color: #333;
            border: 2px solid #555;
            color: #fff;
            padding: 10px 20px;
            font-size: 1.2em;
            cursor: pointer;
            border-radius: 5px;
            margin-right: 15px;
            transition: background-color 0.3s, color 0.3s;
        }

        .like:hover, .dislike:hover {
            background-color: #007bff;
            color: white;
        }

        .like.clicked {
            background-color: #28a745;
            color: white;
        }

        .dislike.clicked {
            background-color: #dc3545;
            color: white;
        }

        .buttons-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .comments-section {
            margin-top: 40px;
        }

        .comment {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }

        .comment-header {
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .comment-body {
            font-size: 1.1em;
            color: #555;
        }

        .comment-footer {
            font-size: 0.9em;
            color: #888;
            text-align: right;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1em;
            margin-bottom: 15px;
            background-color: #f9f9f9;
            color: #333;
        }

        .submit-comment-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.2em;
            cursor: pointer;
        }

        .submit-comment-btn:hover {
            background-color: #218838;
        }

        .back-link {
            text-decoration: none;
            color: #007bff;
            font-size: 1.2em;
            margin-bottom: 20px;
            display: inline-block;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="secure3.php" class="back-link">Back</a>

        <div class="header">
            <h2><?php echo $video['title']; ?></h2>
            <p><strong>Description:</strong> <?php echo $video['description']; ?></p>
        </div>

        <video controls>
            <source src="uploads/<?php echo $video['filename']; ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- Like and dislike buttons -->
        <div class="buttons-container">
            <form action="" method="POST" id="likeDislikeForm">
                <button type="submit" name="like" class="like <?php echo $hasLiked ? 'clicked' : ''; ?>">Like</button>
                <span><?php echo $totalLikes; ?> Likes</span>
                <button type="submit" name="dislike" class="dislike <?php echo $hasDisliked ? 'clicked' : ''; ?>">Dislike</button>
                <span><?php echo $totalDislikes; ?> Dislikes</span>
            </form>
        </div>

        <!-- Comments section -->
        <div class="comments-section">
            <h3>Comments</h3>
            <?php
            if (mysqli_num_rows($commentsResult) > 0) {
                while ($comment = mysqli_fetch_assoc($commentsResult)) {
                    echo '<div class="comment">
                            <div class="comment-header">' . $comment['username'] . ' - ' . $comment['formatted_datetime'] . '</div>
                            <div class="comment-body">' . $comment['comment'] . '</div>
                            <div class="comment-footer">Posted on ' . $comment['formatted_datetime'] . '</div>
                        </div>';
                }
            } else {
                echo '<p>No comments yet.</p>';
            }
            ?>
        </div>

        <!-- Add comment form -->
        <div class="add-comment">
            <h3>Add a Comment</h3>
            <form action="" method="POST">
                <textarea name="comment" rows="4" placeholder="Enter your comment" required></textarea>
                <button type="submit" name="submit_comment" class="submit-comment-btn">Submit Comment</button>
            </form>
        </div>
    </div>

    <?php
    // Handle like and dislike submission
    if (isset($_POST['like'])) {
        if (!$hasLiked) {
            if ($hasDisliked) {
                $deleteDislikeQuery = "DELETE FROM dislikes WHERE video_id = $id AND user_id = $userId";
                mysqli_query($conn, $deleteDislikeQuery);
            }
            $likeQuery = "INSERT INTO likes (video_id, user_id) VALUES ($id, $userId)";
            mysqli_query($conn, $likeQuery);
            header("Location: view_video.php?id=$id");
            exit();
        } else {
            $deleteLikeQuery = "DELETE FROM likes WHERE video_id = $id AND user_id = $userId";
            mysqli_query($conn, $deleteLikeQuery);
            header("Location: view_video.php?id=$id");
            exit();
        }
    }

    if (isset($_POST['dislike'])) {
        if (!$hasDisliked) {
            if ($hasLiked) {
                $deleteLikeQuery = "DELETE FROM likes WHERE video_id = $id AND user_id = $userId";
                mysqli_query($conn, $deleteLikeQuery);
            }
            $dislikeQuery = "INSERT INTO dislikes (video_id, user_id) VALUES ($id, $userId)";
            mysqli_query($conn, $dislikeQuery);
            header("Location: view_video.php?id=$id");
            exit();
        } else {
            $deleteDislikeQuery = "DELETE FROM dislikes WHERE video_id = $id AND user_id = $userId";
            mysqli_query($conn, $deleteDislikeQuery);
            header("Location: view_video.php?id=$id");
            exit();
        }
    }
    ?>
</body>

</html>
