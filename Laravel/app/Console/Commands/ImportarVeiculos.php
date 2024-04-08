<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ImportarVeiculos extends Command
{
    protected $signature = 'importar:veiculos';

    protected $description = 'Importa veículos do site FIPE para o banco de dados';

    public function handle()
    {
        $pythonScript = base_path('caminho_para_o_seu_script_python.py');
        
        $process = new Process(['python3', $pythonScript]);
        
        $process->run();

        // Verifica se houve algum erro na execução do script Python
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->info('Veículos importados com sucesso!');
    }
}
