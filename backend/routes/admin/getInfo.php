<?php
$tables = [];
$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0]; // Naam van de tabel zit in kolom 0
    }
}
$tableData = [];
$table = $_POST['table'] ?? null; // Get table name from POST data
if (!$table && !empty($tables)) {
    $table = $tables[0];
}
if ($table && in_array($table, $tables)) {
    // Get all columns except 'created_at' and 'updated_at'
    $columnsResult = $conn->query("SHOW COLUMNS FROM `$table`");
    $columns = [];
    $columnTypes = [];
    while ($col = $columnsResult->fetch_assoc()) {
        if ($col['Field'] !== 'created_at' && $col['Field'] !== 'updated_at') {
            $columns[] = "`" . $col['Field'] . "`";
            $columnTypes[$col['Field']] = $col['Type'];
        }
    }
    $columnsList = implode(', ', $columns);
    $stmt = $conn->prepare("SELECT $columnsList FROM `$table`");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tableData[] = $row;
    }
} else {
    $tableData  = 'Invalid table name';
}
jsonResponse([
    'tables' => $tables,
    'data' => $tableData,
    'table' => $table,
    'columnTypes' => $columnTypes ?? null
], 200);
