<?php
require 'config.php';

echo "Checking database structure...\n\n";

$stmt = $conn->query('PRAGMA table_info(mahasiswa)');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Columns in mahasiswa table:\n";
foreach($columns as $col) {
    echo "- " . $col['name'] . " (" . $col['type'] . ")\n";
}

echo "\nTotal columns: " . count($columns) . "\n";
?>
