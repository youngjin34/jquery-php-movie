<?php
header('Content-Type: application/json'); // JSON 응답 설정

$api_key = 'c04cda719b1700c4b7896ebdd3ce0397'; // 올바른 API 키를 입력하세요
$total_movies = 50; // 가져오고 싶은 영화 개수
$movies = []; // 영화 데이터를 저장할 배열

// 20개씩 반환되므로, 필요한 페이지 수 계산
$pages_needed = ceil($total_movies / 20); // 20개씩 반환되는 API 특성에 맞게 페이지 수 계산

for ($page = 1; $page <= $pages_needed; $page++) {
    $url = "https://api.themoviedb.org/3/movie/popular?language=ko&page=$page&api_key=$api_key";

    // cURL 사용하여 API 요청
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 응답 상태 확인
    if ($http_code == 200) {
        $data = json_decode($response, true); // 응답을 JSON으로 디코딩
        if (isset($data['results']) && is_array($data['results'])) {
            $movies = array_merge($movies, $data['results']); // 영화 데이터를 병합
        }
    } else {
        http_response_code(500); // 오류 발생 시 500 상태 코드 반환
        echo json_encode(['error' => 'API 요청 실패']);
        exit;
    }
}

// 필요한 수만큼 영화를 자름 (예: 50개)
$movies = array_slice($movies, 0, $total_movies);

// 결과를 JSON으로 반환
echo json_encode(['results' => $movies]);
?>
