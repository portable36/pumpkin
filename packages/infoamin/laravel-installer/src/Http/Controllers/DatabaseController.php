<?php

namespace Infoamin\Installer\Http\Controllers;

use App\Http\Controllers\Controller;
use Infoamin\Installer\Repositories\EnvironmentRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DatabaseController extends Controller
{
    /**
     * Show form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $host     = env('DB_HOST');
        $port     = env('DB_PORT');
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        return view('packages.installer.database', compact('host', 'port', 'database', 'username', 'password'));
    }

    /**
     * Manage form submission.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  Infoamin\Installer\Repositories\EnvironmentRepository  $environmentRepository
     * @return redirection
     */
    public function store(Request $request, EnvironmentRepository $environmentRepository)
    {
        // Set config for migrations and seeds
        $connection = config('database.default');
        config([
            'database.connections.' . $connection . '.host'     => $request->host,
            'database.connections.' . $connection . '.port'     => $request->port,
            'database.connections.' . $connection . '.database' => $request->dbname,
            'database.connections.' . $connection . '.password' => $request->password,
            'database.connections.' . $connection . '.username' => $request->username,
        ]);

        // Update .env file
        $environmentRepository->SetDatabaseSetting($request);
        $seedType = 'dummy-data-off';

        return redirect('install/seedmigrate/' . $seedType);
    }

    public function seedMigrate($type)
    {
        try {
            ini_set('max_execution_time', 600);
            
            // Drop all tables first
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                try {
                    \Illuminate\Support\Facades\DB::statement('DROP TABLE IF EXISTS `' . $tableName . '`');
                } catch (\Exception $e) {
                    // Ignore
                }
            }
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            $output = Artisan::call('app:install');
            
            if (Artisan::output()) {
                \Log::info('Installation output: ' . Artisan::output());
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage() . "\n\nOutput: " . Artisan::output();
            return view('packages.installer.database-error', ['error' => $errorMessage]);
        }

        return redirect('install/register');
    }
}
