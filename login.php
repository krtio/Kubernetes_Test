<?php
session_start();

$host = '10.110.212.126:3306';
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
    <link rel="stylesheet" href="./style.css">
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
