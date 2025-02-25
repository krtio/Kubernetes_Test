<?php
session_start();
require_once './dbconn.php';
$conn = getDatabaseConnection();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EduPro</title>
  <link rel="stylesheet" href="./style.css">
  <style>
    .course-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
      padding: 20px;
    }

    .course-item {
      border: 1px solid #ddd;
      border-radius: 8px;
      overflow: hidden;
      background-color: #fff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
    }

    .course-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .course-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }

    .course-content {
      padding: 15px;
    }

    .btn-enroll, .btn-enter {
      display: block;
      width: 100%;
      padding: 10px;
      font-size: 14px;
      border-radius: 5px;
      cursor: pointer;
      text-align: center;
      color: #fff;
      transition: background-color 0.3s ease;
    }
    
    .btn-enroll { background-color: #dc3545; }
    .btn-enroll:hover { background-color: #c82333; }
    .btn-enter { background-color: #28a745; }
    .btn-enter:hover { background-color: #218838; }
  </style>
</head>
<body>
  <header>
    <div class="center-section">
      <a href="http://43.201.29.34">
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
      $boardId = htmlspecialchars($row['id']);
      $title = htmlspecialchars($row['title']);
      $category = htmlspecialchars($row['category']);
      $content = htmlspecialchars(substr($row['content'], 0, 100));
      $image_url = htmlspecialchars($row['image_url']);
      ?>
      <div class="course-item">
        <img class="course-image" src="<?= $image_url ?>" alt="강의 이미지">
        <div class="course-content">
          <div class="course-title"> <?= $title ?> </div>
          <div class="course-meta">카테고리: <?= $category ?> </div>
          <div class="course-description"> <?= $content ?>... </div>
          <?php if (isset($_SESSION['user'])): ?>
            <button class="btn-enroll">수강신청</button>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

  <footer>
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
        <a href="http://43.201.29.34/page/3/index.php">자유 게시판</a>
      </div>
    </div>
    <div>ⓒ BaroCloud. ALL RIGHTS RESERVED</div>
    <div class="social">
      <a href="#">EDUPRO</a>
    </div>
  </footer>
</body>
</html>
