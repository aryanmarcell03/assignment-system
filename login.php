<?php 

require "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if(empty($email) || empty($password)){
        $error = "All fields required";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {

                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username']; 

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Incorrect password";
            }
        } else {
            $error = "User not found";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background-color: #f2f2f2;
}

.card-custom {
    max-width: 450px;
    margin: 80px auto;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>

</head>
<body>

<div class="card card-custom">
    <h3 class="text-center mb-4">Login</h3>

    <form method="POST">
        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>

        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

        <button class="btn btn-primary w-100">Login</button>
    </form>

    <p class="text-danger text-center mt-3"><?= $error ?></p>

    <p class="text-center mt-3">
        Don't have account? <a href="register.php">Register here</a>
    </p>
</div>

</body>
</html>
