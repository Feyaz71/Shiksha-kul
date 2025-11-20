<?php
include "config.php";

$filename = $_GET["file"];
$filepath = "uploads/" . $filename;

if (file_exists($filepath)) {
    header("Content-Description: File Transfer");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=" . basename($filepath));
    readfile($filepath);
    exit;
} else {
    echo "File not found!";
}
?>
