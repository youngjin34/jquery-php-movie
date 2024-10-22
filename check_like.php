<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json; charset=utf-8');

// 데이터베이스 연결 설정
$servername = "210.117.212.110";
$username = "iden";
$password = "qwe003227";
$dbname = "movie_page";

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// GET 데이터 가져오기
$userId = isset($_GET['userId']) ? intval($_GET['userId']) : null;
$movieId = isset($_GET['movieId']) ? intval($_GET['movieId']) : null;

if ($userId && $movieId) {
    // 좋아요 상태 확인
    $checkSql = "SELECT * FROM likes WHERE userId = ? AND movieId = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ii", $userId, $movieId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["liked" => true]);
    } else {
        echo json_encode(["liked" => false]);
    }

    $checkStmt->close();
} else {
    echo json_encode(["error" => "유효한 사용자 및 영화 ID가 필요합니다."]);
}

$conn->close();
?>
