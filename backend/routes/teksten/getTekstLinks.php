<?php
$tegel = $_POST['slug'] ?? null;

if (!$tegel || !preg_match('/^[a-zA-Z0-9_-]+$/', $tegel)) {
    jsonResponse(['error' => 'Invalid or missing slug'], 400);
}

$query = "
    SELECT 
        t.slug, 
        t.name, 
        t.tegel_naam, 
        c.coursel_name 
    FROM teksten t
    LEFT JOIN coursel_items c ON t.tegel = c.url
    WHERE t.tegel = ? AND t.weergeven = 1
";

$stmt = $conn->prepare($query);

if (!$stmt) {
    jsonResponse(['error' => 'Failed to prepare statement'], 500);
}

$stmt->bind_param("s", $tegel);
$stmt->execute();
$result = $stmt->get_result();

$links = [];
while ($row = $result->fetch_assoc()) {
    $links[] = [
        'slug' => $row['slug'],
        'name' => $row['name'],
        'tegel_naam' => $row['tegel_naam'],
        'coursel_name' => $row['coursel_name'] // This is the new field
    ];
}

jsonResponse($links, 200);









// Igor zijn originele code

// $tegel = $_POST['slug'] ?? null;
// $stmt = $conn->prepare("SELECT slug, name, tegel_naam FROM teksten WHERE tegel = ? and weergeven = 1");
// $stmt->bind_param("s", $tegel);
// $stmt->execute();
// $result = $stmt->get_result();
// $links = [];
// while ($row = $result->fetch_assoc()) {
//     $links[] = [
//         'slug' => $row['slug'],
//         'name' => $row['name'],
//         'tegel_naam' => $row['tegel_naam']
//     ];
// }
// jsonResponse($links, 200);