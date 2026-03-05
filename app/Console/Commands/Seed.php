<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
use Nwidart\Modules\Facades\Module;

class Seed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install {--seed=all} {--migrate=true}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('migrate')) {
            try {
                $this->info('Running migrations using Laravel migration files...');
                
                // Just use regular Laravel migrations
                $this->call('migrate:fresh', ['--force' => true, '--no-interaction' => true]);
                
                $this->info('Migration successful.');
                
            } catch (\Exception $e) {
                $this->error('Migration failed: ' . $e->getMessage());
                throw $e;
            }
        }

        $this->mainAppSeed();
        if (! file_exists('Modules/Dummy/Database/Seeders/DummyImportDatabaseSeeder.php')) {
            $this->moduleSeed();
        } else {
            $this->copyImages();
        }

        $this->info('Database seeding completed successfully.');

        $this->warn('Copying seed files...');

        // Generate passport Client ID and secret
        $this->passportInstall();

        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('config:clear');
        $this->call('route:clear');
    }

    /*
    * Main App Seed
    *
    * @return void
    */
    protected function mainAppSeed()
    {
        $this->call('db:seed');
    }

    /*
    * Module Seed
    *
    * @return void
    */
    protected function moduleSeed()
    {
        $this->warn('Module Seeding: ');

        $this->modulesName()->each(function ($module) {
            Artisan::call('module:seed ' . $module);
            $this->line('   ✔ ' . $module);
        });

        $this->info('Module seeding completed successfully.');
    }

    /*
    * Modules Name
    *
    * @return array
    */
    protected function modulesName()
    {
        if ($this->option('seed') !== 'all') {
            return explode(',', $this->option('seed'));
        }

        return collect(Module::getOrdered())
            ->map(fn ($module) => $module->getName())
            ->values();
    }

    /**
     * Copies images from the module path to the public uploads directory.
     *
     * @return void
     */
    private function copyImages()
    {
        \File::copyDirectory(module_path('Dummy', 'Resources/assets/seeder/'), public_path('uploads/'));
    }

    /**
     * Passport Install
     *
     * @return void
     */
    private function passportInstall()
    {
        $this->info('Setting up Passport...');

        if (! file_exists(storage_path('oauth-private.key')) || ! file_exists(storage_path('oauth-public.key'))) {
            $this->info('Generating Passport encryption keys...');
            $this->call('passport:keys');
        }

        // Create personal access client if not exists
        $personalClient = Client::where('personal_access_client', 1)
            ->where('name', 'Personal Access Client')
            ->first();

        if (! $personalClient) {
            try {
                $this->info('Creating personal access client...');
                $personalClient = Client::create([
                    'name' => 'Personal Access Client',
                    'secret' => Str::random(40),
                    'redirect' => 'http://localhost',
                    'personal_access_client' => true,
                    'password_client' => false,
                    'revoked' => false,
                ]);
                DB::table('oauth_personal_access_clients')->insert([
                    'client_id' => $personalClient->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->info('Personal access client created successfully.');
            } catch (\Exception $e) {
                $this->warn('Personal access client may already exist');
            }
        } else {
            $this->info('Personal access client already exists.');
        }

        // Create password grant client if not exists
        $passwordClient = Client::where('password_client', 1)
            ->where('name', 'Password Grant Client')
            ->where('provider', 'users')
            ->first();

        if (! $passwordClient) {
            try {
                $this->info('Creating password grant client...');
                Client::create([
                    'name' => 'Password Grant Client',
                    'secret' => Str::random(40),
                    'redirect' => 'http://localhost',
                    'provider' => 'users',
                    'personal_access_client' => false,
                    'password_client' => true,
                    'revoked' => false,
                ]);
                $this->info('Password grant client created successfully.');
            } catch (\Exception $e) {
                $this->warn('Password grant client may already exists.');
            }
        } else {
            $this->info('Password grant client already exists.');
        }

        $this->info('Passport setup completed successfully.');
    }
}
