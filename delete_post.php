<?php
require_once __DIR__ . '/connect.php';

// First, find the IDs of the posts we want to delete to delete their comments
$stmt = $conn->prepare("SELECT id FROM posts WHERE tieude LIKE '%sex%' OR noidung LIKE '%sex%' OR tieude LIKE '%mẹ%' OR noidung LIKE '%mẹ%'");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $postId = $row['id'];
    $conn->query("DELETE FROM comments WHERE post_id = $postId");
}
$stmt->close();

$stmt = $conn->prepare("DELETE FROM posts WHERE tieude LIKE '%sex%' OR noidung LIKE '%sex%' OR tieude LIKE '%mẹ%' OR noidung LIKE '%mẹ%'");
$stmt->execute();
echo "Deleted " . $stmt->affected_rows . " posts.\n";
$stmt->close();

$stmt = $conn->prepare("DELETE FROM comments WHERE traloi LIKE '%sex%' OR traloi LIKE '%mẹ%'");
$stmt->execute();
echo "Deleted " . $stmt->affected_rows . " comments.\n";
$stmt->close();

$conn->close();
?>
