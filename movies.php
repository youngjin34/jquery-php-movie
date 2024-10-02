<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// 데이터베이스 연결 설정
$servername = "210.117.212.110";
$username = "root";
$password = "qwe003227";
$dbname = "movie_page";

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// 영화 데이터 가져오기 쿼리
$sql = "SELECT * FROM movies"; 
$result = $conn->query($sql);

$movies = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
} else {
    echo json_encode(["error" => "영화 데이터가 없습니다."]);
    exit;
}

// JSON 형식으로 출력
echo json_encode($movies);

// 연결 종료
$conn->close();
?>
