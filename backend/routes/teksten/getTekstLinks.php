<?php
$tegel = $_POST['slug'] ?? null;
$stmt = $conn->prepare("SELECT slug, name, tegel_naam FROM teksten WHERE tegel = ?");
$stmt->bind_param("s", $tegel);
$stmt->execute();
$result = $stmt->get_result();
$links = [];
while ($row = $result->fetch_assoc()) {
    $links[] = [
        'slug' => $row['slug'],
        'name' => $row['name'],
        'tegel_naam' => $row['tegel_naam']
    ];
}
jsonResponse($links, 200);
