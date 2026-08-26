<?php
// Insert PencairanPinjaman routes into web.php at the right location
$filePath = 'routes/web.php';
$lines = file($filePath, FILE_IGNORE_NEW_LINES);

// Find line 229: the delete route for pinjaman
// Insert routes after line 229 (0-indexed: 229)
$insertLines = [
    '',
    '    // Pencairan Pinjaman (Inertia + React)',
    "    Route::get('/pencairan-pinjaman', [PencairanPinjamanController::class, 'index'])->name('pencairan-pinjaman');",
    "    Route::get('/pencairan-pinjaman/create', [PencairanPinjamanController::class, 'create'])->name('pencairan-pinjaman.create');",
    "    Route::post('/pencairan-pinjaman', [PencairanPinjamanController::class, 'store'])->name('pencairan-pinjaman.store');",
    "    Route::get('/pencairan-pinjaman/{pencairan}', [PencairanPinjamanController::class, 'show'])->name('pencairan-pinjaman.show');",
    "    Route::get('/pencairan-pinjaman/{pencairan}/edit', [PencairanPinjamanController::class, 'edit'])->name('pencairan-pinjaman.edit');",
    "    Route::put('/pencairan-pinjaman/{pencairan}', [PencairanPinjamanController::class, 'update'])->name('pencairan-pinjaman.update');",
    "    Route::delete('/pencairan-pinjaman/{pencairan}', [PencairanPinjamanController::class, 'destroy'])->name('pencairan-pinjaman.destroy');",
    '',
    '    // Pencairan Pinjaman Actions',
    "    Route::post('/pencairan-pinjaman/{pencairan}/approve', [PencairanPinjamanController::class, 'approve'])->name('pencairan-pinjaman.approve');",
    "    Route::post('/pencairan-pinjaman/{pencairan}/reject', [PencairanPinjamanController::class, 'reject'])->name('pencairan-pinjaman.reject');",
    "    Route::post('/pencairan-pinjaman/{pencairan}/cairkan', [PencairanPinjamanController::class, 'cairkan'])->name('pencairan-pinjaman.cairkan');",
    '',
    '',
];

// Insert after line 229 (index 229)
$newLines = array_merge(
    array_slice($lines, 0, 230),  // lines 1-230
    $insertLines,
    array_slice($lines, 230)      // lines 231+
);

file_put_contents($filePath, implode("\n", $newLines));
echo "Routes inserted. Total lines: " . count($newLines) . "\n";

// Verify no duplicates
$content = file_get_contents($filePath);
$count = substr_count($content, "// Pencairan Pinjaman (Inertia + React)");
echo "PencairanPinjaman route blocks found: $count\n";
?>
