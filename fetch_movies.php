<?php
header('Content-Type: application/json'); // JSON 응답 설정

$api_key = 'c04cda719b1700c4b7896ebdd3ce0397'; // 올바른 API 키를 입력하세요
$page = 1; // 원하는 페이지 번호
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
    echo $response; // API 응답 반환
} else {
    http_response_code(500); // 오류 발생 시 500 상태 코드 반환
    echo json_encode(['error' => 'API 요청 실패']);
}
?>
