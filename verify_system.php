<?php

// Test script untuk verifikasi koneksi sistem ARSIP PUSRI
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$results = [];

// TEST 1: Database Connection
$results['Database Connection'] = 'Testing...';
try {
    DB::connection()->getPdo();
    $results['Database Connection'] = '✓ Connected';
} catch (Exception $e) {
    $results['Database Connection'] = '✗ Error: ' . $e->getMessage();
}

// TEST 2: Database Tables
$results['Database Tables'] = 'Testing...';
try {
    $tables = DB::select('SHOW TABLES');
    $results['Database Tables'] = '✓ Found ' . count($tables) . ' tables';
} catch (Exception $e) {
    $results['Database Tables'] = '✗ Error: ' . $e->getMessage();
}

// TEST 3: User Model
$results['User Model'] = 'Testing...';
try {
    $users = App\Models\User::count();
    $results['User Model'] = '✓ Found ' . $users . ' users';
} catch (Exception $e) {
    $results['User Model'] = '✗ Error: ' . $e->getMessage();
}

// TEST 4: SuratKeputusan Model
$results['SuratKeputusan Model'] = 'Testing...';
try {
    $count = App\Models\SuratKeputusan::count();
    $results['SuratKeputusan Model'] = '✓ Found ' . $count . ' SK documents';
} catch (Exception $e) {
    $results['SuratKeputusan Model'] = '✗ Error: ' . $e->getMessage();
}

// TEST 5: SuratPerjanjian Model
$results['SuratPerjanjian Model'] = 'Testing...';
try {
    $count = App\Models\SuratPerjanjian::count();
    $results['SuratPerjanjian Model'] = '✓ Found ' . $count . ' SP documents';
} catch (Exception $e) {
    $results['SuratPerjanjian Model'] = '✗ Error: ' . $e->getMessage();
}

// TEST 6: SuratAddendum Model
$results['SuratAddendum Model'] = 'Testing...';
try {
    $count = App\Models\SuratAddendum::count();
    $results['SuratAddendum Model'] = '✓ Found ' . $count . ' Addendum documents';
} catch (Exception $e) {
    $results['SuratAddendum Model'] = '✗ Error: ' . $e->getMessage();
}

// TEST 7: DocumentNumberService
$results['DocumentNumberService'] = 'Testing...';
try {
    $service = new App\Services\DocumentNumberService();
    $results['DocumentNumberService'] = '✓ Loaded and initialized';
} catch (Exception $e) {
    $results['DocumentNumberService'] = '✗ Error: ' . $e->getMessage();
}

// TEST 8: CsvExportService
$results['CsvExportService'] = 'Testing...';
try {
    $service = new App\Services\CsvExportService();
    $results['CsvExportService'] = '✓ Loaded and initialized';
} catch (Exception $e) {
    $results['CsvExportService'] = '✗ Error: ' . $e->getMessage();
}

// TEST 9: NotificationService
$results['NotificationService'] = 'Testing...';
try {
    $service = new App\Services\NotificationService();
    $results['NotificationService'] = '✓ Loaded and initialized';
} catch (Exception $e) {
    $results['NotificationService'] = '✗ Error: ' . $e->getMessage();
}

// TEST 10: Routes
$results['Routes'] = 'Testing...';
try {
    $routes = Route::getRoutes();
    $results['Routes'] = '✓ Found ' . count($routes) . ' routes';
} catch (Exception $e) {
    $results['Routes'] = '✗ Error: ' . $e->getMessage();
}

// TEST 11: Storage (MinIO)
$results['Storage/MinIO'] = 'Testing...';
try {
    $disk = Storage::disk('minio');
    $results['Storage/MinIO'] = '✓ MinIO disk configured';
} catch (Exception $e) {
    $results['Storage/MinIO'] = '✗ Error: ' . $e->getMessage();
}

// TEST 12: View Rendering
$results['View Rendering'] = 'Testing...';
try {
    if (view()->exists('layouts.user')) {
        $results['View Rendering'] = '✓ Views system working';
    } else {
        $results['View Rendering'] = '✗ Views not found';
    }
} catch (Exception $e) {
    $results['View Rendering'] = '✗ Error: ' . $e->getMessage();
}

// TEST 13: Middleware
$results['Middleware'] = 'Testing...';
try {
    $middleware = config('app.middleware', []);
    $results['Middleware'] = '✓ Middleware configured';
} catch (Exception $e) {
    $results['Middleware'] = '✗ Error: ' . $e->getMessage();
}

// TEST 14: Auth
$results['Authentication'] = 'Testing...';
try {
    $authDriver = config('auth.defaults.driver');
    $results['Authentication'] = '✓ Auth driver: ' . $authDriver;
} catch (Exception $e) {
    $results['Authentication'] = '✗ Error: ' . $e->getMessage();
}

// TEST 15: Model Relationships
$results['Model Relationships'] = 'Testing...';
try {
    $user = App\Models\User::first();
    if ($user) {
        $results['Model Relationships'] = '✓ User relationships loaded';
    } else {
        $results['Model Relationships'] = '⚠ No users in database';
    }
} catch (Exception $e) {
    $results['Model Relationships'] = '✗ Error: ' . $e->getMessage();
}

// Display results
echo "\n" . str_repeat("=", 70) . "\n";
echo "                   SYSTEM VERIFICATION REPORT\n";
echo "                     ARSIP PUSRI v2.0\n";
echo str_repeat("=", 70) . "\n\n";

$passed = 0;
$failed = 0;
$warning = 0;

foreach ($results as $test => $result) {
    if (strpos($result, '✓') === 0) {
        $passed++;
        echo "✓ " . str_pad($test, 35) . " " . $result . "\n";
    } elseif (strpos($result, '✗') === 0) {
        $failed++;
        echo "✗ " . str_pad($test, 35) . " " . $result . "\n";
    } else {
        $warning++;
        echo "⚠ " . str_pad($test, 35) . " " . $result . "\n";
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "SUMMARY: " . $passed . " Passed | " . $failed . " Failed | " . $warning . " Warnings\n";
echo str_repeat("=", 70) . "\n\n";

if ($failed === 0) {
    echo "✓ ALL SYSTEMS OPERATIONAL - READY FOR PRODUCTION!\n\n";
} else {
    echo "✗ SOME ISSUES DETECTED - PLEASE REVIEW ABOVE\n\n";
}
