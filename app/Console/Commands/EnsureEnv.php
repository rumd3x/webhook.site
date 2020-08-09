<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class EnsureEnv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:ensure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure .env file exists and is populated properly and consistently';

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
     * @return mixed
     */
    public function handle()
    {
        $appKey = env('APP_KEY');
        if ($appKey === null) {
            $this->info('APP KEY non existent, creating entry...');
            $this->setEnvironmentValue('APP_KEY', '');
        }

        if (empty($appKey)) {
            $this->info('Generating APP KEY...');
            Artisan::call('key:generate', ['--show' => true]);
            $key = Artisan::output();
            $this->setEnvironmentValue('APP_KEY', trim($key));
        }

        $this->setEnvironmentValue('APP_ENV', $this->getOSEnvVar('APP_ENV'));
        $this->setEnvironmentValue('APP_DEBUG', $this->getOSEnvVar('APP_DEBUG'));
        $this->setEnvironmentValue('APP_KEY', $this->getOSEnvVar('APP_KEY'));
        $this->setEnvironmentValue('APP_URL', $this->getOSEnvVar('APP_URL'));
        $this->setEnvironmentValue('APP_LOG', $this->getOSEnvVar('APP_LOG'));
        $this->setEnvironmentValue('DB_CONNECTION', $this->getOSEnvVar('DB_CONNECTION'));
        $this->setEnvironmentValue('REDIS_HOST', $this->getOSEnvVar('REDIS_HOST'));
        $this->setEnvironmentValue('BROADCAST_DRIVER', $this->getOSEnvVar('BROADCAST_DRIVER'));
        $this->setEnvironmentValue('CACHE_DRIVER', $this->getOSEnvVar('CACHE_DRIVER'));
        $this->setEnvironmentValue('QUEUE_DRIVER', $this->getOSEnvVar('QUEUE_DRIVER'));
        $this->setEnvironmentValue('ECHO_HOST_MODE', $this->getOSEnvVar('ECHO_HOST_MODE'));      
        
        $this->info('Environment ok!');
    }

    private function getOSEnvVar(string $var, $default = '')
    {
        $value = getenv($var) ?: $default;
        if (strpos($value, ' ') !== false) {
            $value = "'$value'";
        }
        return $value;
    }

    private function setEnvironmentValue(string $key, string $value)
    {
        $key = strtoupper($key);
        $file_content = explode("\n", File::get('.env'));

        $found = false;
        for ($i = 0; $i < count($file_content); $i++) {
            if (strpos(strtoupper($file_content[$i]), $key) !== false) {
                $found = true;
                $file_content[$i] = "$key=$value";
            }
        }

        if ($found) {
            File::put('.env', implode("\n", $file_content));
            return;
        }

        File::append('.env', "$key=$value\n");
    }
}