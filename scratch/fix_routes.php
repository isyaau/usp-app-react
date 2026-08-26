<?php
// Fix duplicate routes in web.php
$filePath = 'routes/web.php';
$lines = file($filePath, FILE_IGNORE_NEW_LINES);

// Find boundaries: keep lines 1-242 (index 0-241), then skip to line 291 (index 290)
// Lines 243-290 (index 242-289) are duplicates to remove
$newLines = array_merge(
    array_slice($lines, 0, 242),  // lines 1-242
    array_slice($lines, 290)       // lines 291+
);

file_put_contents($filePath, implode("\n", $newLines));
echo "Fixed! New line count: " . count($newLines) . "\n";
?>
