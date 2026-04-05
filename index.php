<?php
session_start();
include("db_connect.php");

if(isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    $sql = "SELECT * FROM staff WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);
    
    if($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        
        if($user['role'] == 'Admin') {
            header("Location: pages/dashboard.php");
        } elseif($user['role'] == 'Kitchen') {
            header("Location: pages/kitchen.php");
        } else {
            header("Location: pages/orders.php");
        }
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Grabs & Giggles</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            width: 100%;
        }

        /* Split Screen */
        .split {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* Left Side - White Background with Form */
        .left {
            width: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        /* Form Box */
        .form-box {
            background: white;
            border-radius: 30px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid #eef2f6;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo i {
            font-size: 50px;
            color: #ff6b35;
        }

        .logo h2 {
            color: #1e293b;
            font-size: 28px;
            margin-top: 10px;
        }

        .logo p {
            color: #64748b;
            font-size: 14px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-weight: 500;
            font-size: 14px;
        }

        .input-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            transition: all 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: #ff6b35;
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .login-btn {
            width: 100%;
            background: linear-gradient(135deg, #ff8c42, #ff6b35);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 16px;
            font-weight: 600;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
        }

        .error {
            background: #fef2f2;
            color: #dc2626;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
        }

        /* Right Side - Image */
        .right {
            width: 50%;
            background-image: url('images/bg2.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .split {
                flex-direction: column;
            }
            .left {
                width: 100%;
                height: 60%;
                padding: 20px;
            }
            .right {
                width: 100%;
                height: 40%;
            }
            .form-box {
                padding: 25px;
            }
            .logo h2 {
                font-size: 24px;
            }
            .logo i {
                font-size: 40px;
            }
        }

        @media (max-width: 480px) {
            .form-box {
                padding: 20px;
            }
            .logo h2 {
                font-size: 20px;
            }
            .input-group input {
                padding: 10px 12px;
                font-size: 14px;
            }
            .login-btn {
                padding: 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="split">
    <!-- Left Side - Login Form -->
    <div class="left">
        <div class="form-box">
            <div class="logo">
                <i class="fas fa-utensils"></i>
                <h2>Grabs & Giggles</h2>
                <p>Restaurant Management System</p>
            </div>
            
            <?php if(isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="Enter your email">
                </div>
                
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter your password">
                </div>
                
                <button type="submit" name="login" class="login-btn">
                    Sign In
                </button>
            </form>
        </div>
    </div>
    
    <!-- Right Side - Image -->
    <div class="right"></div>
</div>

</body>
</html>