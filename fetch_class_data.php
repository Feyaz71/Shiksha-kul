<?php
include "config.php";

if (!isset($_GET["id"])) {
    echo json_encode(["error" => "Class ID is missing."]);
    exit;
}

$class_id = intval($_GET["id"]);

$result = $conn->query("SELECT * FROM classes WHERE id = $class_id");

if ($result->num_rows == 0) {
    echo json_encode(["error" => "Class not found."]);
    exit;
}

$class = $result->fetch_assoc();

$files = [];
$file_result = $conn->query("SELECT id, file_name, file_path FROM files WHERE class_id = $class_id");

while ($row = $file_result->fetch_assoc()) {
    $files[] = [
        "id" => $row["id"],
        "filename" => $row["file_name"],  // ✅ Change `file_name` instead of `filename`
        "filepath" => $row["file_path"]
    ];
}

echo json_encode([
    "class_id" => $class_id,
    "class_name" => $class["name"],
    "files" => $files
]);
?>
