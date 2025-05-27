<?php
$id = $_POST['id'] ?? null; // Get id from POST data
                $title = $_POST['title'] ?? null; // Get name from POST data
                if (!$id || !$title) {
                    jsonResponse(['error' => 'id and title are required'], 400);
                    exit;
                }
                $stmt = $conn->prepare("UPDATE coursel_items SET title = ? WHERE id = ?");
                $stmt->bind_param("si", $title, $id);
                if ($stmt->execute()) {
                    jsonResponse(['success' => 'Link updated successfully'], 200);
                } else {
                    jsonResponse(['error' => 'Failed to update link'], 500);
                }