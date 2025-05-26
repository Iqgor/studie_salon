<?php
$tegel = $_POST['slug'] ?? null;
$stmt = $conn->prepare("SELECT slug, name FROM teksten WHERE tegel = ?");
$stmt->bind_param("s", $tegel);
$stmt->execute();
$result = $stmt->get_result();
$links = [];
while ($row = $result->fetch_assoc()) {
    $links[] = [
        'slug' => $row['slug'],
        'name' => $row['name']
    ];
}
jsonResponse($links, 200);