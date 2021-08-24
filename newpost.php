<?php
session_start();

$username = $_SESSION['username'];

include("connection.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $owner = $username;
    $date = date("Y-m-d");
    $tags = $_POST['tags'];
    $tags = preg_replace('/\s+/', '', $tags);
    $tagsArray = explode(",", $tags);
    $image = '';

    $errorMsg = '';

    // set image
    if (isset($_POST['submit']) && isset($_FILES['my_image']) && !empty($_FILES['my_image']['name'])) {
        // echo "<pre>";
        // print_r($_FILES['my_image']);
        // echo"</pre>";

        $img_name = $_FILES['my_image']['name'];
        $img_size = $_FILES['my_image']['size'];
        $tmp_name = $_FILES['my_image']['tmp_name'];
        $error = $_FILES['my_image']['error'];

        if ($error === 0) {
            if ($img_size > 1000000) {
                $errorMsg = "Sorry, your file is too large.";
            } else {
                $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                $img_ex = strtolower($img_ex);

                $allowed_ex = array('jpg', 'jpeg', 'png');

                if (in_array($img_ex, $allowed_ex)) {
                    $new_img_name = uniqid("IMG-", true) . '.' . $img_ex;
                    $img_upload_path = 'uploads/' . $new_img_name;
                    if (!empty($title) && !empty($description) && !empty($tags)) {
                        move_uploaded_file($tmp_name, $img_upload_path);
                    }

                    $image = $new_img_name;
                } else {
                    $errorMsg = "You can't upload files of this type";
                }
            }
        } else {
            $errorMsg = "Unknown error occurred!";
        }
    }

    if (!empty($title) && !empty($description) && !empty($tags) && !empty($image)) {
        // add post into database
        $addpost = "INSERT INTO posts(owner, date, title, description, image, post_id) VALUES ('$owner','$date','$title','$description','$image',NULL)";
        $result = mysqli_query($dbc, $addpost) or die(mysqli_error($dbc));
        if ($result) {
            // query for added post to get post_id
            $query = "select * from posts where title = '$title' and owner='$owner' and description='$description'";
            $result = mysqli_query($dbc, $query);
            $row = mysqli_fetch_array($result);
            $post_id = $row['post_id'];

            // add tags into database
            for ($i = 0; $i < count($tagsArray); $i++) {
                $addtags = "insert into tags (tag_name, post_id)
                VALUES ('$tagsArray[$i]', '$post_id')";

                $result = mysqli_query($dbc, $addtags);
                if (!$result) {
                    echo "Something went wrong";
                }
            }

            // redirect to allposts to see newly added post
            header("Location: allposts.php");
            die;
        } else {
            echo "Unable to add to database";
        }
    } else {
        if(empty($errorMsg)) {
            echo "All fields must be filled.";
        }
    }
}

?>

<html>

<head>
    <title>New Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body style="background: #E8F9FF;">
    <?php if (isset($errorMsg)) : ?>
        <p><?php echo $errorMsg ?></p>
    <?php endif ?>
    <div class="container mt-3">
        <div class="mb-3">
            <h1> New Post</h1>
            <div>
                <div class="float-left" style="display:inline;">
                    <a class="btn btn-outline-primary" href="main.php">Back To Main</a>
                    <a class="btn btn-outline-primary" href="allposts.php">View All Posts</a>
                </div>
                <div class="text-end" style="clear:right; float: right; display: inline;">
                    <a class="btn btn-outline-secondary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3 row">
                    <label for="title" class="col-sm-2 col-form-label">Title</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="title" name="title">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="description" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea rows="8" type="text" class="form-control" id="description" name="description"></textarea>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="img" class="col-sm-2 col-form-label">Image</label>
                    <div class="col-sm-10">
                        <input type="file" class="form-control-file" id="img" name="my_image" accept="image/*">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="tags" class="col-sm-2 col-form-label">Tags (comma-separated)</label>
                    <div class="col-sm-10">
                        <div>
                            <input type="text" class="form-control" id="tags" name="tags">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="submit" name="submit" value="Post!" class="btn btn-primary" />
                </div>

            </form>
        </div>
    </div>
</body>

</html>