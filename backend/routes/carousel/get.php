<?php
$stmt = $conn->prepare("SELECT * FROM `coursel_items` WHERE `weergeven` = 1 ");
$stmt->execute();
$result = $stmt->get_result();
$carouselItems = [];
while ($row = $result->fetch_assoc()) {
    $carouselItems[] = $row;
}
jsonResponse(['carouselItems' => $carouselItems], 200);
