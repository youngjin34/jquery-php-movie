<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
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

// 리뷰 등록
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password']; // 비밀번호를 해시로 변환하는 부분을 추가할 수 있습니다.
    $content = $_POST['content'];
    $userId = $_POST['userId'];
    $movieId = $_POST['movieId'];

    // 리뷰를 삽입
    $stmt = $conn->prepare("INSERT INTO review (password, content, userId, movieId) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $password, $content, $userId, $movieId);

    if ($stmt->execute()) {
        echo json_encode(["message" => "리뷰가 성공적으로 추가되었습니다."]);
    } else {
        echo json_encode(["error" => "리뷰 추가 실패: " . $stmt->error]);
    }

    $stmt->close();
}

// 리뷰 가져오기
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['movieId'])) {
        $movieId = $_GET['movieId'];

        // 해당 movieId에 대한 리뷰 가져오기
        $stmt = $conn->prepare("SELECT r.reviewId, r.content, r.created_at, u.name FROM review r JOIN users u ON r.userId = u.userId WHERE r.movieId = ?");
        $stmt->bind_param("i", $movieId);
        $stmt->execute();
        $result = $stmt->get_result();

        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }

        echo json_encode($reviews);

        $stmt->close();
    } else {
        echo json_encode(["error" => "movieId가 제공되지 않았습니다."]);
    }
}

$conn->close();
?>
