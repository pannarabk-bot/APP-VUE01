<?php
// 1. Header สำหรับปลดล็อก CORS และตั้งค่า JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// 2. จัดการ Method OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

include 'condb.php';

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // ✅ ดึงข้อมูลทั้งหมด
    if ($method === "GET") {
        $stmt = $conn->prepare("SELECT * FROM student ORDER BY student_id DESC");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $result]);
    }

    // ✅ เพิ่มข้อมูล
    elseif ($method === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        
        // ตรวจสอบค่าว่าง (ลบคำว่า email ซ้ำซ้อนออก)
        if (empty($data["first_name"]) || empty($data["last_name"]) || empty($data["phone"]) || empty($data["email"])) {
            echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบ"]);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO student (first_name, last_name, phone, email) 
                                VALUES (:first_name, :last_name, :phone, :email)");

        $stmt->bindParam(":first_name", $data["first_name"]);
        $stmt->bindParam(":last_name", $data["last_name"]);
        $stmt->bindParam(":phone", $data["phone"]);
        $stmt->bindParam(":email", $data["email"]);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "เพิ่มข้อมูลเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถเพิ่มข้อมูลได้"]);
        }
    }

    // ✅ แก้ไขข้อมูล
    elseif ($method === "PUT") {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["student_id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบค่า student_id"]);
            exit;
        }

        // ลบเครื่องหมายคอมม่า (,) ส่วนเกินหน้า WHERE และแก้ SQL ให้กระชับ
        $sql = "UPDATE student
                SET first_name = :first_name, 
                    last_name = :last_name, 
                    phone = :phone, 
                    email = :email 
                WHERE student_id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":first_name", $data["first_name"]);
        $stmt->bindParam(":last_name", $data["last_name"]);
        $stmt->bindParam(":phone", $data["phone"]);
        $stmt->bindParam(":email", $data["email"]);
        $stmt->bindParam(":id", $data["student_id"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "แก้ไขข้อมูลเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถแก้ไขข้อมูลได้"]);
        }
    }

    // ✅ ลบข้อมูล
    elseif ($method === "DELETE") {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["student_id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบค่า student_id"]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM student WHERE student_id = :id");
        $stmt->bindParam(":id", $data["student_id"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "ลบข้อมูลเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถลบข้อมูลได้"]);
        }
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>