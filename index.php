<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = '10.110.212.126:3306';
$dbname = 'aaa';
$username = 'admin';
$password = 'P@ssw0rd';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$tableCheck = "SHOW TABLES LIKE 'boards'";
$result = $conn->query($tableCheck);
if ($result->num_rows == 0) {
    $createTableSQL = "
        CREATE TABLE boards (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL,
            content TEXT NOT NULL,
            image_url VARCHAR(500) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
    $conn->query($createTableSQL);
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EduPro</title>
  <link rel="stylesheet" href="./style.css">
</head>
<body>

<header>
    <div class="center-section">
      <a href="http://192.168.10.10:32603">
        <img src="logo.png" alt="EduPro Logo">
      </a>
    </div>
    <div class="right-section">
      <?php if (isset($_SESSION['user'])): ?>
        <button class="logout-btn" onclick="location.href='logout.php'">로그아웃</button>
      <?php else: ?>
        <button class="login-btn" onclick="location.href='login.php'">로그인</button>
      <?php endif; ?>
    </div>
</header>

<nav class="nav-menu">
    <a href="./index.php?category=all">전체</a>
    <a href="./index.php?category=개발프로그래밍">개발 · 프로그래밍</a>
    <a href="./index.php?category=게임개발">게임 개발</a>
    <a href="./index.php?category=데이터사이언스">데이터 사이언스</a>
    <a href="./index.php?category=인공지능">인공지능</a>
</nav>

<div class="course-grid">
    <?php
    $sql = "SELECT id, title, category, content, image_url FROM boards";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()):
      echo '<div class="course-item">';
      echo '<img class="course-image" src="'.htmlspecialchars($row['image_url']).'" alt="강의 이미지">';
      echo '<div class="course-content">';
      echo '<div class="course-title">'.htmlspecialchars($row['title']).'</div>';
      echo '<div class="course-meta">카테고리: '.htmlspecialchars($row['category']).'</div>';
      echo '<div class="course-description">'.htmlspecialchars(substr($row['content'], 0, 100)).'...</div>';
      if (isset($_SESSION['user'])):
        echo '<button class="btn-enroll">수강신청</button>';
      endif;
      echo '</div></div>';
    endwhile;
    ?>
</div>

<footer class="footer">
    <div class="links">
      <div>
        <strong>인프런</strong>
        <a href="#">에듀프로 소개</a>
        <a href="#">블로그</a>
      </div>
      <div>
        <strong>신청하기</strong>
        <a href="#">멘토링 소개</a>
        <a href="#">인프런 제휴</a>
      </div>
      <div>
        <strong>고객센터</strong>
        <a href="#">공지사항</a>
        <a href="#">자유 게시판</a>
      </div>
    </div>

    <div>ⓒ BaroCloud. ALL RIGHTS RESERVED</div>

    <div class="social">
      <a href="#">EDUPRO</a>
    </div>
</footer>

</body>
</html>
