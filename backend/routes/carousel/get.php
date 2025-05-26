<?php
$stmt = $conn->prepare("SELECT * FROM `coursel_items`");
                $stmt->execute();
                $result = $stmt->get_result();
                $carouselItems = [];
                while ($row = $result->fetch_assoc()) {
                    $carouselItems[] = $row;
                }
                jsonResponse(['carouselItems' => $carouselItems], 200);