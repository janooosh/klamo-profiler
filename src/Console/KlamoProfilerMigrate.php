<?php

namespace Klamo\ProfilingSystem\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class KlamoProfilerMigrate extends Command{

    protected $signature = 'klamoProfiler:migrate';

    protected $description = 'Gives a choise to migrate or refresh all Klamo profiling system tables';

    public function handle()
    {
        $this->info('What type of migration do you want?');
        $this->info('Option 1: Migrate for first time');
        $this->info('Option 2: Drop all tables and remigrate');
        $this->info('Option 3: Do nothing');

        $migrate_option = $this->ask('Choose an option');

        switch($migrate_option){
            case '1':
                $this->call('migrate', [
                    '--path' => '/database/migrations/ProfilingSystem/'
                ]);
                break;
            case '2':
                $this->call('migrate:refresh',[
                    '--path' => '/database/migrations/ProfilingSystem/'
                ]);
                break;
            case '3':
                break;
            default:
                $this->info('Incorrect options, try again');
                $this->info('_____________________________');
                $this->handle();
        }

        $this->info('Migration process completed');      
    }
}