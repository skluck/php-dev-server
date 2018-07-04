<?php

namespace SK\Server;

use Symfony\Component\Process\Process;

class CaddyProcess
{
    const BINARY = 'caddy';

    const CADDY_CONFIG = '/Caddyfile';

    private $configDir;
    private $workingDir;

    public function __construct($configDir, $workingDir = '')
    {
        $this->workingDir = $workingDir ? rtrim($workingDir, '/') : getcwd();
        $this->configDir = rtrim($configDir, '/');
    }

    public function type(): string
    {
        return 'Caddy';
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
        $args = [];

        $caddyConfig = $this->configDir . self::CADDY_CONFIG;
        if (file_exists($caddyConfig)) {
            $args = array_merge($args, ['-conf', $caddyConfig]);
        }

        return $args;
    }
}
