<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OX 퀴즈</title>
    <link rel="stylesheet" href="styles.css">
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

            fetch("http://192.168.10.10:3306/backend.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
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
