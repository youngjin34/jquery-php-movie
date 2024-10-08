<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json; charset=utf-8');

// 데이터베이스 연결 설정
$servername = "210.117.212.110";
$username = "root";
$password = "qwe003227";
$dbname = "movie_page";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginId = $_POST['loginId'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("INSERT INTO users (loginId, password, name, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $loginId, $password, $name, $phone);

    if ($stmt->execute()) {
        // JSON 형식으로 응답 
        echo json_encode(["success" => true, "message" => "사용자가 추가되었습니다."]);
    } else {
        // JSON 형식으로 응답 
        echo json_encode(["success" => false, "message" => "데이터 추가 실패: " . $stmt->error]);
    }

    $stmt->close();
} else {
    // JSON 형식으로 응답 
    echo json_encode(["success" => false, "message" => "올바른 요청이 아닙니다."]);
}

// 데이터베이스 연결 종료
$conn->close();


?>
