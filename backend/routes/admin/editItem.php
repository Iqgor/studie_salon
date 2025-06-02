<?php
$table = $_POST['table'];
$id = $_POST['id'];
$action = $_POST['action'];


$result = mysqli_query($conn, "SELECT * FROM $table WHERE id = " . intval($id));
$item = mysqli_fetch_assoc($result);

switch($action){
    case 'edit':
        $data = $_POST['data'];
        $setParts = [];
        $types = '';
        $values = [];
        if (is_string($data)) {
            $data = json_decode($data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                jsonResponse(['error' => 'Ongeldige JSON in data']);
                exit;
            }
        }
        foreach ($data as $key => $value) {
            $setParts[] = "$key = ?";
            $types .= 's';
            $values[] = $value;
        }
        $query = "UPDATE $table SET " . implode(', ', $setParts) . " WHERE id = ?";
        $types .= 'i';
        $values[] = $id;
        break;

    case 'delete':
        $query = "DELETE FROM $table WHERE id = ?";
        $types = 'i';
        $values = [$id];
        break;
    case 'hide':
        $newWeergeven = ($item['weergeven'] == 1) ? 0 : 1;
        $query = "UPDATE $table SET weergeven = ? WHERE id = ?";
        $types = 'ii';
        $values = [$newWeergeven, $id];
        break;
    case 'add':
        $data = $_POST['data'];
        $setParts = [];
        $types = '';
        $values = [];
        if (is_string($data)) {
            $data = json_decode($data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                jsonResponse(['error' => 'Ongeldige JSON in data']);
                exit;
            }
        }
        foreach ($data as $key => $value) {
            $setParts[] = "$key = ?";
            $types .= 's';
            $values[] = $value;
        }
        $query = "INSERT INTO $table SET " . implode(', ', $setParts);
        break;
    default:
        jsonResponse(['error' => 'Ongeldige actie']);
        exit;
}
// Prepare and execute the query
$stmt = $conn->prepare($query);
if (!$stmt) {
    jsonResponse(['error' => 'Fout bij voorbereiden van de query: ' . $conn->error]);
    exit;
}
$stmt->bind_param($types, ...$values);
if (!$stmt->execute()) {
    jsonResponse(['error' => 'Fout bij uitvoeren van de query: ' . $stmt->error]);
    exit;
}
jsonResponse(['success' => true, 'message' => 'Item succesvol bijgewerkt']);
