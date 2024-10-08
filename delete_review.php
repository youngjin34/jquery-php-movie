<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// 데이터베이스 연결 설정
$servername = "210.117.212.110";
$username = "root";
$password = "qwe003227";
$dbname = "movie_page";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// POST 요청이 있는지 확인
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviewId = $_POST['reviewId'];
    $password = $_POST['password'];

    // 리뷰 삭제를 위해 비밀번호 확인
    $stmt = $conn->prepare("SELECT password FROM review WHERE reviewId = ?");
    $stmt->bind_param("i", $reviewId);
    $stmt->execute();
    $stmt->bind_result($storedPassword);
    $stmt->fetch();
    $stmt->close();

    if ($storedPassword && $storedPassword === $password) {
        $deleteStmt = $conn->prepare("DELETE FROM review WHERE reviewId = ?");
        $deleteStmt->bind_param("i", $reviewId);

        if ($deleteStmt->execute()) {
            echo json_encode(["message" => "리뷰가 성공적으로 삭제되었습니다."]);
        } else {
            echo json_encode(["error" => "리뷰 삭제 실패: " . $deleteStmt->error]);
        }

        $deleteStmt->close();
    } else {
        echo json_encode(["error" => "비밀번호가 일치하지 않습니다."]);
    }
} else {
    echo json_encode(["error" => "잘못된 요청입니다. POST 요청만 허용됩니다."]);
}

$conn->close();
?>
