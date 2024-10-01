<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json; charset=utf-8'); // 응답 타입을 JSON으로 설정

// 데이터베이스 연결 설정
$servername = "210.117.212.110"; // 데이터베이스 서버 IP 주소
$username = "root"; // 데이터베이스 사용자명
$password = "qwe003227"; // 데이터베이스 비밀번호
$dbname = "movie_page"; // 사용할 데이터베이스 이름

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// POST 요청에서 데이터 가져오기
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginId = $_POST['loginId'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];

    // Prepared statement 사용
    $stmt = $conn->prepare("INSERT INTO users (loginId, password, name, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $loginId, $password, $name, $phone);

    if ($stmt->execute()) {
        echo json_encode(["message" => "사용자가 추가되었습니다."]);
    } else {
        echo json_encode(["error" => "데이터 추가 실패: " . $conn->error]);
    }

    $stmt->close();
}

// 데이터베이스 연결 종료
$conn->close();
?>
