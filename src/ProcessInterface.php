<?php

namespace SK\Server;

use Symfony\Component\Process\Process;

interface ProcessInterface
{
    public function type(): string;

    public function create(): ?Process;
}
