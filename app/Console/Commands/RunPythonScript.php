<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


class RunPythonScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-python-script';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    public function handle()
    {
        $path = base_path() . '/python_scripts/insert_veiculos.py';
        $command = escapeshellcmd("python \"$path\"");
        $output = shell_exec($command);
        echo $output;
    }
    
}
