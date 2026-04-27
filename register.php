<?php
require "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'];

    if (empty($username) || empty($email) || empty($password_raw)) {
        $message = "All fields are required!";
    } else {

        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email already exists!";
        } else {

            // hash password
            $password = password_hash($password_raw, PASSWORD_DEFAULT);

            // insert user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                $message = "Register success!";
            } else {
                $message = "Something went wrong!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Register</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background-color: #f2f2f2;
}

.card-box {
    max-width: 500px;
    margin: 80px auto;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
    background: #fff;
}

.btn-custom {
    background-color: #4a90e2;
    color: white;
}
</style>
</head>

<body>

<div class="card-box text-center">

    <h3 class="mb-4">Register</h3>

    <?php if(!empty($message)) { ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php } ?>

    <form method="POST">

        <div class="mb-3">
            <input type="text" name="username" class="form-control" placeholder="Name" required>
        </div>

        <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>

        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <button type="submit" class="btn btn-custom w-100">Register</button>

    </form>

    <p class="mt-3">
        Already have account? <a href="login.php">Login here</a>
    </p>

</div>

</body>
</html>
