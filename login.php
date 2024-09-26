<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 데이터베이스 연결 설정
    $servername = "210.117.212.125"; // 데이터베이스 서버 IP 주소
    $username = "root"; // 데이터베이스 사용자명
    $password = "qwe003227"; // 데이터베이스 비밀번호
    $dbname = "movie_page"; // 사용할 데이터베이스 이름

    // 데이터베이스 연결
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 연결 확인
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
        exit;
    }

    // POST 요청으로부터 데이터 가져오기
    $loginId = $_POST['userId'];
    $userPassword = $_POST['password'];

    // 사용자 ID로 해당 사용자 조회
    $sql = "SELECT * FROM users WHERE loginId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loginId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 사용자가 존재하는 경우 비밀번호 확인
        $user = $result->fetch_assoc();
        if ($user['password'] === $userPassword) {
            echo json_encode(["success" => true, "message" => "Login successful"]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid password"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "User not found"]);
    }

    // 데이터베이스 연결 종료
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
