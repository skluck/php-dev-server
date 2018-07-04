<?php

namespace SK\Server;

use Symfony\Component\Process\Process;

class ProcessHandler
{
    private $processes;
    private $running;

    public function __construct(array $processes)
    {
        $this->processes = $processes;
        $this->running = [];
    }

    public function run()
    {
        $longest = 0;
        foreach ($this->processes as $process) {
            if (strlen($process->type()) > $longest) {
                $longest = strlen($process->type());
            }
        }

        foreach ($this->processes as $process) {
            $processType = $process->type();
            $callback = $this->buildOutputCallback($processType, $longest);

            echo "Starting ${processType}\n";

            $sfProcess = $process->create();
            // $sfProcess->disableOutput();
            $sfProcess->start($callback);

            $this->running[] = $sfProcess;
        }

        $this->registerSignal();
        $this->wait();
    }

    private function buildOutputCallback($type, $longest)
    {
        $prefix = str_pad($type, $longest, ' ', STR_PAD_LEFT);

        $callback = function($type, $buffer) use ($prefix) {
            echo "[ ${prefix} ] ${buffer}";
        };

        return $callback;
    }

    private function wait()
    {
        while ($this->running) {
            foreach ($this->running as $i => $process) {
                if (!$process->isRunning()) {
                    unset($this->running[$i]);
                }
            }

            sleep(1);
        }
    }

    private function registerSignal()
    {
        pcntl_async_signals(true);
        pcntl_signal(SIGINT, function($signo, $signinfo)  {
            echo "\nWarning: interrupt received, stopping server...\n";
            foreach ($this->running as $process) {
                $process->signal($signo);
            }
        });
    }
}
