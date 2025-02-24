<?php
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "192.168.10.10:3306";
    $username = "root";
    $password = "rootpassword";
    $dbname = "quiz";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die(json_encode(["status" => "error", "message" => "DB 연결 실패"]));
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $name = $data['name'] ?? '';
    $phone = $data['phone'] ?? '';
    $answer = $data['answer'] ?? '';
    $consent = $data['consent'] ?? 0;
    $timestamp = date("Y-m-d H:i:s");

    if (empty($name) || empty($phone) || empty($answer) || !$consent) {
        echo json_encode(["status" => "error", "message" => "모든 필드를 입력하세요."]);
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM quiz_entries WHERE name=?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "이미 응모한 이름입니다."]);
    } else {
        $stmt = $conn->prepare("INSERT INTO quiz_entries (name, phone, answer, consent, timestamp) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $name, $phone, $answer, $consent, $timestamp);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "응모가 완료되었습니다."]);
        } else {
            echo json_encode(["status" => "error", "message" => "저장 중 오류 발생."]);
        }
    }

    $stmt->close();
    $conn->close();
}
?>
