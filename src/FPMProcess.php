<?php

namespace SK\Server;

use Symfony\Component\Process\Process;

class FPMProcess implements ProcessInterface
{
    const BINARY = 'php-fpm';

    const FPM_CONFIG = '/fpm.ini';
    const PHP_CONFIG = '/php.ini';

    private $configDir;
    private $workingDir;

    public function __construct($configDir, $workingDir = '')
    {
        $this->workingDir = $workingDir ? rtrim($workingDir, '/') : getcwd();
        $this->configDir = rtrim($configDir, '/');
    }

    public function type(): string
    {
        return 'FPM';
    }

    public function create(): ?Process
    {
        $args = $this->buildConfig();
        $process = $this->createProcess($args);

        return $process;
    }

    private function createProcess(array $options)
    {
        $args = array_merge(
            [self::BINARY],
            $options
        );

        $process = new Process($args);
        $process->setWorkingDirectory($this->workingDir);
        $process->setTimeout(null);

        return $process;
    }

    private function buildConfig()
    {
        $args = [
            '--nodaemonize',
            '--prefix', $this->workingDir
        ];

        $phpConfig = $this->configDir . self::PHP_CONFIG;
        if (file_exists($phpConfig)) {
            $args = array_merge($args, ['-c', $phpConfig]);
        }

        $fpmConfig = $this->configDir . self::FPM_CONFIG;
        if (file_exists($fpmConfig)) {
            $args = array_merge($args, ['--fpm-config', $fpmConfig]);
        }

        return $args;
    }
}
