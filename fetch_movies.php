<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$api_key = 'c04cda719b1700c4b7896ebdd3ce0397';
$total_movies = 50;
$movies = [];

$pages_needed = ceil($total_movies / 20);

for ($page = 1; $page <= $pages_needed; $page++) {
    $url = "https://api.themoviedb.org/3/movie/popular?language=ko&page=$page&api_key=$api_key";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        $data = json_decode($response, true);
        if (isset($data['results']) && is_array($data['results'])) {
            $movies = array_merge($movies, $data['results']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => '결과가 없거나 잘못된 JSON 형식입니다.']);
            exit;
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'API 요청 실패: ' . $http_code]);
        exit;
    }
}

// 요청한 영화 수에 맞게 잘라내기
$movies = array_slice($movies, 0, $total_movies);

// 데이터베이스 연결 설정
$servername = "210.117.212.110";
$username = "iden";
$password = "qwe003227";
$dbname = "movie_page";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "데이터베이스 연결 실패: " . $conn->connect_error]));
}

// 영화가 존재하지 않을 때만 추가
if (!empty($movies)) {
    foreach ($movies as $movie) {
        $id = $movie['id'];
        $title = $conn->real_escape_string($movie['title']);
        $overview = $conn->real_escape_string($movie['overview']);
        $release_date = $conn->real_escape_string($movie['release_date']);
        $vote_average = $conn->real_escape_string($movie['vote_average']);
        
        // genre_ids 처리 (쉼표로 구분된 문자열)
        $genre_ids = implode(',', $movie['genre_ids']);
        
        // poster_path 처리
        $poster_path = !empty($movie['poster_path']) ? 'https://image.tmdb.org/t/p/original' . $movie['poster_path'] : null;

        // backdrop_path 처리
        $backdrop_path = !empty($movie['backdrop_path']) ? 'https://image.tmdb.org/t/p/original' . $movie['backdrop_path'] : null;

        // 영화가 이미 존재하는지 확인
        $check_stmt = $conn->prepare("SELECT id FROM movies WHERE id = ?");
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows == 0) {
            // 영화 삽입
            $stmt = $conn->prepare("INSERT INTO movies (id, title, overview, release_date, vote_average, poster_path, genre_ids, backdrop_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $id, $title, $overview, $release_date, $vote_average, $poster_path, $genre_ids, $backdrop_path);

            if ($stmt->execute()) {
                // 삽입 성공 메시지
                echo json_encode(["message" => "영화가 성공적으로 추가되었습니다: $title"]);
            } else {
                echo json_encode(["error" => "오류: " . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(["message" => "영화가 이미 존재합니다: $title"]);
        }

        $check_stmt->close();
    }
} else {
    echo json_encode(["message" => "추가할 영화가 없습니다."]);
}

$conn->close();

// 성공적으로 저장된 영화의 JSON 출력
echo json_encode(['results' => $movies]);
?>
