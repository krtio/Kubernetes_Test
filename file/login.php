<?php
session_start(); 

$host = '10.96.105.25:3306';
$dbname = 'aaa';
$username = 'admin';
$password = 'P@ssw0rd';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}

$form = isset($_GET['form']) ? $_GET['form'] : 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($form === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            header('Location: 192.168.10.10:32603/index.php');
            exit;
        } else {
            echo "<div class='error'>로그인 실패: 이메일 또는 패스워드를 확인하세요.</div>";
        }
    } elseif ($form === 'signup') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            echo "<div class='success'>회원가입 성공! 로그인 해주세요.</div>";
        } else {
            echo "<div class='error'>회원가입 실패: " . $conn->error . "</div>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }
        .container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .navigation {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            background: #f0f0f0;
            border-radius: 8px;
            padding: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .navigation button {
            flex: 1;
            padding: 10px 20px;
            background-color: white;
            color: #333;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .navigation button:hover {
            background-color: #4CAF50;
            color: white;
        }
        .navigation button.active {
            background-color: #4CAF50;
            color: white;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }
        .form-section {
            display: none;
        }
        .form-section.active {
            display: block;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 0 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .form-group button {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 0 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="navigation">
            <button id="login-tab" class="active" onclick="location.href='?form=login'">로그인</button>
            <button id="signup-tab" onclick="location.href='?form=signup'">회원가입</button>
        </div>

        <?php if ($form === 'login'): ?>
        <div id="login-form" class="form-section active">
            <h2>로그인</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="login-email">이메일</label>
                    <input type="email" id="login-email" name="email" placeholder="이메일을 입력하세요." required>
                </div>
                <div class="form-group">
                    <label for="login-password">패스워드</label>
                    <input type="password" id="login-password" name="password" placeholder="패스워드를 입력하세요." required>
                </div>
                <div class="form-group">
                    <button type="submit">로그인</button>
                </div>
            </form>
        </div>
        <?php elseif ($form === 'signup'): ?>
        <div id="signup-form" class="form-section active">
            <h2>회원가입</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="signup-name">이름</label>
                    <input type="text" id="signup-name" name="name" placeholder="이름을 입력하세요." required>
                </div>
                <div class="form-group">
                    <label for="signup-email">이메일</label>
                    <input type="email" id="signup-email" name="email" placeholder="이메일을 입력하세요." required>
                </div>
                <div class="form-group">
                    <label for="signup-password">패스워드</label>
                    <input type="password" id="signup-password" name="password" placeholder="패스워드를 입력하세요." required>
                </div>
                <div class="form-group">
                    <button type="submit">회원가입</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>
