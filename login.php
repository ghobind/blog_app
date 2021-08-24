<?php

session_start();

include("connection.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($user) && !empty($password)) {
        $query = "select * from login where username='$user' limit 1";

        $result = mysqli_query($dbc, $query);
        if ($result) {
            if ($result && mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);

                if ($user_data['password'] === $password) {
                    $_SESSION['username'] = $user_data['username'];
                    header("Location: main.php");
                    die;
                } else {
                    echo "Wrong username or password";
                }
            }
        } else {
            echo "Wrong username or password";
        }
    } else {
        echo "Wrong username or password";
    }
}

?>

<html>

<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
</head>

<body style="background: #E8F9FF;">
    <div class="container my-3">
        <h1>Login</h1>
        <form method="POST">
            <div class="mb-3 row">
                <label for="username" class="col-sm-2 col-form-label">Username</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="username" name="username">
                </div>
            </div>
            <div class="mb-3 row">
                <label for="password" class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="password" name="password">
                </div>
            </div>
            <div class="mb-3">
                <input type="submit" name="login" value="Log In" class="btn btn-primary" />
                <a class="btn btn-secondary" href="register.php"> Register</a>
            </div>

        </form>
    </div>
</body>

</html>