<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
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

// POST 데이터 가져오기
$userId = isset($_POST['userId']) ? intval($_POST['userId']) : null;
$movieId = isset($_POST['movieId']) ? intval($_POST['movieId']) : null;

if ($userId && $movieId) {
    // 좋아요 상태 확인
    $checkSql = "SELECT * FROM likes WHERE userId = ? AND movieId = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ii", $userId, $movieId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // 좋아요가 이미 존재하므로 삭제
        $deleteSql = "DELETE FROM likes WHERE userId = ? AND movieId = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("ii", $userId, $movieId);
        
        if ($deleteStmt->execute()) {
            echo json_encode(["message" => "좋아요가 취소되었습니다."]);
        } else {
            echo json_encode(["error" => "좋아요 삭제 실패: " . $deleteStmt->error]);
        }

        $deleteStmt->close();
    } else {
        // 좋아요가 존재하지 않으므로 추가
        $insertSql = "INSERT INTO likes (userId, movieId) VALUES (?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("ii", $userId, $movieId);
        
        if ($insertStmt->execute()) {
            echo json_encode(["message" => "좋아요가 추가되었습니다."]);
        } else {
            echo json_encode(["error" => "좋아요 추가 실패: " . $insertStmt->error]);
        }

        $insertStmt->close();
    }

    $checkStmt->close();
} else {
    echo json_encode(["error" => "유효한 사용자 및 영화 ID가 필요합니다."]);
}


$conn->close();
?>
