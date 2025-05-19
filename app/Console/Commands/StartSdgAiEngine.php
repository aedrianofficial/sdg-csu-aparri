<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class StartSdgAiEngine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sdg:start-engine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the SDG AI Engine FastAPI server';

    /**
     * The process instance for the AI engine
     */
    protected $process;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting SDG AI Engine...');
        
        // Get the path to the SDG AI Engine directory
        $aiEnginePath = base_path('sdg_ai_engine');
        
        // Ensure the virtual environment exists
        if (!file_exists("{$aiEnginePath}/venv")) {
            $this->error('Virtual environment not found. Creating one...');
            $this->createVirtualEnvironment($aiEnginePath);
        }
        
        // Determine the correct Python executable path based on platform
        $pythonPath = $this->getPythonPath($aiEnginePath);
        
        // Start the FastAPI server
        $this->startFastApiServer($aiEnginePath, $pythonPath);
        
        // Register the AI engine URL in .env file
        $this->updateEnvFile();
        
        $this->info('SDG AI Engine started successfully on http://localhost:8000');
        
        return \Symfony\Component\Console\Command\Command::SUCCESS;
    }
    
    /**
     * Create a virtual environment for the SDG AI Engine
     */
    protected function createVirtualEnvironment($aiEnginePath)
    {
        $this->info('Setting up Python virtual environment...');
        
        $process = new Process(['python', '-m', 'venv', 'venv'], $aiEnginePath);
        $process->setTimeout(300); // 5 minutes timeout
        $process->run();
        
        if (!$process->isSuccessful()) {
            $this->error('Failed to create virtual environment: ' . $process->getErrorOutput());
            return;
        }
        
        $this->info('Installing dependencies...');
        
        // Determine pip command based on platform
        $pipCommand = PHP_OS_FAMILY === 'Windows' ? 'venv\\Scripts\\pip' : 'venv/bin/pip';
        
        $process = new Process([$pipCommand, 'install', '-r', 'requirements.txt'], $aiEnginePath);
        $process->setTimeout(300); // 5 minutes timeout
        $process->run();
        
        if (!$process->isSuccessful()) {
            $this->error('Failed to install dependencies: ' . $process->getErrorOutput());
            return;
        }
        
        $this->info('Virtual environment created and dependencies installed.');
    }
    
    /**
     * Get the Python executable path based on platform
     */
    protected function getPythonPath($aiEnginePath)
    {
        return PHP_OS_FAMILY === 'Windows' 
            ? "{$aiEnginePath}\\venv\\Scripts\\python.exe"
            : "{$aiEnginePath}/venv/bin/python";
    }
    
    /**
     * Start the FastAPI server
     */
    protected function startFastApiServer($aiEnginePath, $pythonPath)
    {
        $this->info('Starting FastAPI server...');
        
        // Use uvicorn to start the server
        $this->process = new Process(
            [$pythonPath, '-m', 'uvicorn', 'app.main:app', '--host', '0.0.0.0', '--port', '8000'],
            $aiEnginePath,
            null,
            null,
            null
        );
        
        // Start the process in the background
        $this->process->start();
        
        // Wait a bit for the server to start
        sleep(3);
        
        if (!$this->process->isRunning()) {
            $this->error('Failed to start FastAPI server: ' . $this->process->getErrorOutput());
            return;
        }
        
        // Register the process to be terminated when PHP shuts down
        register_shutdown_function(function () {
            if ($this->process && $this->process->isRunning()) {
                $this->process->stop();
            }
        });
    }
    
    /**
     * Update the .env file with the SDG AI Engine URL
     */
    protected function updateEnvFile()
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);
        
        if (strpos($envContent, 'SDG_AI_ENGINE_URL') === false) {
            file_put_contents($envFile, $envContent . "\nSDG_AI_ENGINE_URL=http://localhost:8000\n");
        }
    }
} 
