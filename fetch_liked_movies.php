<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json; charset=utf-8');

include 'db_connection.php'; // 데이터베이스 연결 파일

// 데이터베이스 연결 설정
$servername = "210.117.212.110";
$username = "iden";
$password = "qwe003227";
$dbname = "movie_page";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// POST 요청에서 userId를 가져옵니다.
$data = json_decode(file_get_contents("php://input"));
$userId = $data->userId; // 클라이언트에서 전달된 userId

// 좋아요한 영화 정보를 가져오는 쿼리
$query = "SELECT m.id, m.title, m.release_date, m.vote_average, m.poster_path, m.overview
          FROM likes l
          JOIN movies m ON l.movieId = m.id
          WHERE l.userId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$movies = array();
while ($row = $result->fetch_assoc()) {
    $movies[] = $row;
}

echo json_encode($movies);
$stmt->close(); // prepared statement 닫기
$conn->close(); // 데이터베이스 연결 닫기
?>
