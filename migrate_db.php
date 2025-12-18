<?php
require 'config.php';

echo "Adding new columns to mahasiswa table...\n\n";

try {
    // Add telegram_username column
    $conn->exec("ALTER TABLE mahasiswa ADD COLUMN telegram_username TEXT");
    echo "✓ Added telegram_username column\n";
} catch(PDOException $e) {
    echo "- telegram_username already exists or error: " . $e->getMessage() . "\n";
}

try {
    // Add verification_status column
    $conn->exec("ALTER TABLE mahasiswa ADD COLUMN verification_status TEXT DEFAULT 'pending'");
    echo "✓ Added verification_status column\n";
} catch(PDOException $e) {
    echo "- verification_status already exists or error: " . $e->getMessage() . "\n";
}

try {
    // Add verification_token column
    $conn->exec("ALTER TABLE mahasiswa ADD COLUMN verification_token TEXT");
    echo "✓ Added verification_token column\n";
} catch(PDOException $e) {
    echo "- verification_token already exists or error: " . $e->getMessage() . "\n";
}

try {
    // Add verified_at column
    $conn->exec("ALTER TABLE mahasiswa ADD COLUMN verified_at DATETIME");
    echo "✓ Added verified_at column\n";
} catch(PDOException $e) {
    echo "- verified_at already exists or error: " . $e->getMessage() . "\n";
}

echo "\n✅ Database migration completed!\n\n";

// Check final structure
$stmt = $conn->query('PRAGMA table_info(mahasiswa)');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Updated columns in mahasiswa table:\n";
foreach($columns as $col) {
    echo "- " . $col['name'] . " (" . $col['type'] . ")\n";
}

echo "\nTotal columns: " . count($columns) . "\n";
?>
