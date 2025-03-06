<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "10.96.105.25:3306";
    $username = "admin";
    $password = "P@ssw0rd";
    $dbname = "aaa";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die(json_encode(["status" => "error", "message" => "DB 연결 실패"]));
    }

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $answer = $_POST['answer'];
    $consent = isset($_POST['consent']) ? 1 : 0;
    $timestamp = date("Y-m-d H:i:s");

    if (empty($name) || empty($phone) || empty($answer) || !$consent) {
        echo json_encode(["status" => "error", "message" => "모든 필드를 입력하세요."]);
        exit();
    }

    $sql = "SELECT * FROM aaa WHERE name=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "이미 응모한 이름입니다."]);
    } else {
        $sql = "INSERT INTO aaa (name, phone, answer, consent, timestamp) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssis", $name, $phone, $answer, $consent, $timestamp);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "응모가 완료되었습니다."]);
        } else {
            echo json_encode(["status" => "error", "message" => "저장 중 오류 발생."]);
        }
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OX 퀴즈</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            text-align: center;
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .question {
            font-size: 22px;
            margin: 20px 0;
            font-weight: bold;
        }
        .buttons {
            margin: 20px 0;
        }
        .button {
            font-size: 18px;
            padding: 12px 25px;
            margin: 10px;
            cursor: pointer;
            border: none;
            border-radius: 10px;
            transition: 0.3s;
        }
        .button:hover {
            transform: scale(1.1);
        }
        .button.o {
            background-color: #4CAF50;
            color: white;
        }
        .button.x {
            background-color: #F44336;
            color: white;
        }
        .modal {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .close-btn, .submit-btn {
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
            color: white;
        }
        .close-btn {
            background: #ff7043;
        }
        .submit-btn {
            background: #3498db;
        }
        .submit-btn:hover {
            background: #2980b9;
            transform: scale(1.05);
        }
        .submit-btn:disabled {
            background: #b0c4de;
            cursor: not-allowed;
        }
        .input-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
        }
        .input-group input {
            width: 80%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }
        .checkbox-label {
            align-self: flex-start;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>OX 퀴즈를 맞춰보세요!</h1>
        <div class="question" id="question">석탄(화석연료)은 깨끗하고 영원히 쓸 수 있는 에너지원이다?</div>
        <div class="buttons">
            <button class="button o" onclick="checkAnswer('O')">O</button>
            <button class="button x" onclick="checkAnswer('X')">X</button>
        </div>
    </div>
    
    <div class="overlay" id="overlay"></div>
    <div class="modal" id="modal">
        <h2> 응모하기 </h2>
        <div class="input-group">
            <input type="text" id="name" placeholder="성명">
            <input type="text" id="phone" placeholder="전화번호">
        </div>
        <label class="checkbox-label">
            <input type="checkbox" id="consent" onchange="toggleSubmitButton()"> 개인정보 이용 · 제공 · 활용 동의서
        </label>
        <p>퀴즈의 정답을 맞추시는 모든 분들께 스타벅스 아메리카노<br>쿠폰을 카카오톡 선물하기로 보내드립니다.</p>
        <div class="button-group">
            <button class="submit-btn" onclick="submitForm()" disabled>제출하기</button>
            <button class="close-btn" onclick="closeModal()">닫기</button>
        </div>
    </div>
    
    <script>
        let selectedAnswer = '';

        function checkAnswer(answer) {
            selectedAnswer = answer;
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('modal').style.display = 'block';
            document.querySelector(".submit-btn").disabled = true;
        }

        function closeModal() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('modal').style.display = 'none';
        }

        function toggleSubmitButton() {
            const consentChecked = document.getElementById("consent").checked;
            document.querySelector(".submit-btn").disabled = !consentChecked;
        }

        function submitForm() {
            const name = document.getElementById("name").value.trim();
            const phone = document.getElementById("phone").value.trim();
            const consent = document.getElementById("consent").checked ? 1 : 0;

            if (!name || !phone || !consent) {
                alert("모든 정보를 입력하세요.");
                return;
            }

            fetch("", {
                method: "POST",
                body: new URLSearchParams({ name, phone, answer: selectedAnswer, consent })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.status === "success") closeModal();
            });
        }
    </script>
</body>
</html>
