<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupDemoData extends Command
{
    protected $signature = 'app:setup-demo {--fresh : Fresh install}';
    protected $description = 'Setup demo data for the application';

    public function handle(): int
    {
        $this->info('Setting up demo data...');

        if ($this->option('fresh')) {
            $this->call('migrate:fresh', ['--seed' => true]);
        } else {
            $this->call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder']);
        }

        $this->info('Demo data setup complete!');
        return Command::SUCCESS;
    }
}