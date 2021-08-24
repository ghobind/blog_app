<?php
session_start();

include("connection.php");
include("post_object.php");

$username = $_SESSION['username'];

// search database and add post objects to the post array
$query = "select * from posts ORDER BY `date` DESC";
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

// PAGINATION
// 1. set limit, query for first n-limit posts
$limit = 5;

$displayedPosts = array();
// current page number
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
// start page
$start = ($page - 1) * $limit;
$query = "select * from posts ORDER BY `date` DESC limit $start, $limit ";
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
        $displayedPosts[] = $post;
    }
}

// find total number of posts
$totalPosts = count($posts);
// Find total number of pages
$totalPages = ceil($totalPosts / $limit);

$previous = $page - 1;
$next = $page + 1;

// check if at start or end of page number
function checkStart()
{
    if ($GLOBALS('previous') == 1) {
        return "disabled";
    }
}
function checkEnd()
{
    if ($GLOBALS('next') == $GLOBALS('totalPages')) {
        return "disabled";
    }
}


// search database and find all posts this user has liked
$likedposts = array();  // array containing post_id of liked posts
$query = "select * from likes where username='$username'";
$result = mysqli_query($dbc, $query);
if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        $likedposts[] = $row['post_id'];
    }
}

function checkIfLiked($post_id)
{
    if (in_array($post_id, $GLOBALS["likedposts"], true)) {
        return "images/thumbs-up-solid.svg";
    }
    return "images/thumbs-up-regular.svg";
}

?>

<html>

<head>
    <title>View All Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
</head>

<body style="background: #E8F9FF;">
    <div class="container mt-3">
        <div class="mb-3">
            <h1>View All Posts</h1>
            <div>
                <div class="float-left" style="display:inline;">
                    <a class="btn btn-outline-primary" href="newpost.php">New Post</a>
                    <a class="btn btn-outline-primary" href="main.php">Back To Main</a>
                </div>
                <div class="text-end" style="clear:right; float: right; display: inline;">
                    <a class="btn btn-outline-secondary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
        <?php
        for ($i = 0; $i < count($displayedPosts); $i++) {
            echo "<div class='card flex-row flex-wrap mb-3'>"
                . "<div class='card-header border-0'>"
                . "<img style='width:200; height: 200;' src='/uploads/" . $displayedPosts[$i]->image . "' alt='post_image'>"
                . "</div>"
                . "<div class='card-body' style='word-wrap: break-word; max-width: 80%;'>"
                . "<h5 class='card-title'>" . $displayedPosts[$i]->title . "</h5>"
                . "<p class='card-text'>" . $displayedPosts[$i]->description . "</p>"
                . "<div >"
                . "<div class='float-left' style='display: inline;'><div>";
            for ($j = 0; $j < count($displayedPosts[$i]->tags); $j++) {
                echo "<span class='mx-1 mb-3 badge rounded-pill bg-light text-dark'>" . "#" . $displayedPosts[$i]->tags[$j] . " </span>";
            }
            echo "</div>";
            echo "<div class='float-left' style='display: inline;'>"
                . "<img width='10%' height='10%' " /*. "onclick='thumbsUpClicked(" . $filteredPosts[$i]->post_id . "'" */
                . "src='" . checkIfLiked($displayedPosts[$i]->post_id) . "' alt='thumbs-up'/>"
                . "<span style='margin-left:-3%;''>" . $displayedPosts[$i]->likes . "</span>"
                . "</div>";
            echo "</div>"
                . "<div class='mr-3' style='clear:right; float: right; display: inline;' >"
                . "<div class='text-end' style='font-size: 110%'>" . $displayedPosts[$i]->owner . "</div>"
                . "<div class='text-muted'>" . $displayedPosts[$i]->date . "</div>"
                . "</div>"
                . "</div>"
                . "</div>"
                . "</div>";
        }
        ?>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <?php echo $previous == 0 ? "<li class='page-item disabled'>" : "<li class='page-item'>" ?>
                <a class="page-link" href="allposts.php?page=<?= $previous; ?>">Previous</a></li>
                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <li class="page-item"><a class="page-link" href="allposts.php?page=<?= $i; ?>"><?= $i; ?></a></li>
                <?php endfor; ?>
                <?php echo $next == ($totalPages + 1) ? "<li class='page-item disabled'>" : "<li class='page-item'>" ?>
                <a class="page-link" href="allposts.php?page=<?= $next; ?>">Next</a></li>
            </ul>
        </nav>
    </div>
</body>

</html>