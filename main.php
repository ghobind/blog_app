<?php
session_start();

$username = $_SESSION['username'];

include("connection.php");
include("post_object.php");

// search database and add post objects to the post array
$query = "select * from posts";
$result = mysqli_query($dbc, $query);

if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        $formattedDate = date('F j, Y', strtotime($row['date']));
        $post = new Post(
            $row['post_id'],
            $row['owner'],
            $formattedDate,
            $row['title'],
            $row['description'],
            $row['image']
        );
        $posts[] = $post;
    }
}

// search database and update likes and tags of each post
$query = "select * from likes";
$result = mysqli_query($dbc, $query);

if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        for ($i = 0; $i < count($posts); $i++) {
            if ($row['post_id'] == $posts[$i]->post_id) {
                $posts[$i]->likes++;
                break;
            }
        }
    }
}

$query = "select * from tags";
$result = mysqli_query($dbc, $query);

if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        for ($i = 0; $i < count($posts); $i++) {
            if ($row['post_id'] == $posts[$i]->post_id) {
                $posts[$i]->tags[] = $row['tag_name'];
            }
        }
    }
}

// if posts array size is > 5, we want to choose top 5 post (based on number of likes)
$filteredPosts = array();
// order by likes
if (count($posts) >= 5) {
    // 1. sort posts array from largest to smallest
    usort($posts, fn ($a, $b) => strcmp($b->likes, $a->likes));

    // 2. only take the first 5 posts
    for ($i = 0; $i < 5; $i++) {
        $filteredPosts[] = $posts[$i];
    }
} else {
    $filteredPosts = $posts;
}

// search database and find all posts this user has liked
$likedposts = array();  // array containing post_id of liked posts
$query = "select * from likes where username='$username'";
$result = mysqli_query($dbc, $query);
if($result) {
    while($row = mysqli_fetch_array($result)) {
        $likedposts[] = $row['post_id'];
    }
}

function checkIfLiked($post_id) {
    if(in_array($post_id, $GLOBALS["likedposts"], true)) {
        return "images/thumbs-up-solid.svg";
    }
    return "images/thumbs-up-regular.svg";
}

function thumbsUpClicked($post_id) {
    echo "clicked " . $post_id;
}

?>

<html>

<head>
    <title>Main Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
</head>

<body style="background: #E8F9FF;">
    <div class="container mt-3">
        <div class="mb-3 ">
            <h1 class="">Welcome, <?php echo $username; ?></h1>
            <div>
                <div class="float-left" style="display:inline;">
                    <a class="btn btn-outline-primary" href="newpost.php">New Post</a>
                    <a class="btn btn-outline-primary" href="allposts.php">View All Posts</a>
                </div>
                <div class="text-end" style="clear:right; float: right; display: inline;">
                    <a class="btn btn-outline-secondary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
        <h3>Top 5 Posts</h3>
        <?php
        for ($i = 0; $i < count($filteredPosts); $i++) {
            echo "<div class='card flex-row flex-wrap mb-3' >"
                . "<div class='card-header border-0'>"
                    . "<img style='width:200; height: 200;' src='/uploads/" . $filteredPosts[$i]->image. "' alt='post_image'>"
                . "</div>"
                . "<div class='card-body' style='word-wrap: break-word; max-width: 80%;'>"
                . "<h5 class='card-title'>" . $filteredPosts[$i]->title . "</h5>"
                . "<p class='card-text' >" . $filteredPosts[$i]->description . "</p>"
                . "<div >"
                // . "<a href='newpost.php' class='stretched-link'></a>"
                . "<div class='float-left' style='display: inline;'><div>";
            for ($j = 0; $j < count($filteredPosts[$i]->tags); $j++) {
                echo "<span class='mx-1 mb-3 badge rounded-pill bg-light text-dark'>" . "#" . $filteredPosts[$i]->tags[$j] . " </span>";
            }
                echo "</div>";
                echo "<div class='float-left' style='display: inline;'>" 
                        . "<img width='10%' height='10%' " /*. "onclick='thumbsUpClicked(" . $filteredPosts[$i]->post_id . "'" */
                            . "src='" . checkIfLiked($filteredPosts[$i]->post_id) . "' alt='thumbs-up'/>" 
                        . "<span style='margin-left:-3%;''>" . $filteredPosts[$i]->likes . "</span>"
                    . "</div>";
            echo "</div>"
                . "<div class='mr-3' style='clear:right; float: right; display: inline;' >"
                . "<div class='text-end' style='font-size: 110%'>" . $filteredPosts[$i]->owner . "</div>"
                . "<div class='text-muted'>" . $filteredPosts[$i]->date . "</div>"
                . "</div>"
                . "</div>"
                . "</div>"
                . "</div>";
        }
        ?>
    </div>
</body>

</html>