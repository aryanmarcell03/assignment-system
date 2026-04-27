<?php
require "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'];
    $role = $_POST['role']; 

    // Server-side validation
    if (empty($username) || empty($email) || empty($password_raw) || empty($role)) {
        $message = "All fields are required!";
    } else if (strlen($username) < 3) {
        $message = "Name must be at least 3 characters!";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address!";
    } else if (strlen($password_raw) < 6) {
        $message = "Password must be at least 6 characters!";
    } else {

        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email already exists!";
        } else {

            $password = password_hash($password_raw, PASSWORD_DEFAULT);

            // Updated INSERT statement to include the role column
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $password, $role);

            if ($stmt->execute()) {
                $message = "Register successful! <a href='login.php' class='alert-link'>Login here</a>";
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

<script>
function validateRegisterForm() {
    let username = document.getElementById('username').value.trim();
    let email = document.getElementById('email').value.trim();
    let password = document.getElementById('password').value;
    let role = document.getElementById('role').value;
    
    if (username === '' || email === '' || password === '' || role === '') {
        alert('All fields are required!');
        return false;
    }
    
    if (username.length < 3) {
        alert('Name must be at least 3 characters!');
        return false;
    }
    
    // Email validation
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address!');
        return false;
    }
    
    if (password.length < 6) {
        alert('Password must be at least 6 characters!');
        return false;
    }
    
    return true;
}
</script>
</head>

<body>

<div class="card-box text-center">

    <h3 class="mb-4">Register</h3>

    <?php if(!empty($message)) { ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php } ?>

    <form method="POST" onsubmit="return validateRegisterForm()">

        <div class="mb-3">
            <input type="text" id="username" name="username" class="form-control" placeholder="Name" required>
        </div>

        <div class="mb-3">
            <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
        </div>

        <div class="mb-3">
            <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <div class="mb-3">
            <select id="role" name="role" class="form-control" required>
                <option value="">Select Role</option>
                <option value="student">Student</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit" class="btn btn-custom w-100">Register</button>

    </form>

    <p class="mt-3">
        Already have account? <a href="login.php">Login here</a>
    </p>

</div>

</body>
</html>
