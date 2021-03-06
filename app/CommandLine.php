<?php

namespace App;

use Exception;
use Symfony\Component\Process\Process;

class CommandLine
{
    public function requestSudo()
    {
        $this->run('sudo cat /dev/null');
    }

    public function run(string $command, callable $onError = null)
    {
        if (! is_callable($onError)) {
            $onError = function ($exitCode, $output) use ($command) {
                throw new Exception(sprintf(
                    '%s failed with exit code %s.',
                    $command,
                    $exitCode
                ));
            };
        }

        $process = Process::fromShellCommandline($command)->setTimeout(null);

        $process->run();

        if (! $process->isSuccessful()) {
            $onError($process->getExitCode(), $process->getErrorOutput());
            return trim($process->getErrorOutput());
        }

        return trim($process->getOutput());
    }
}
