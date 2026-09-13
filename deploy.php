<?php

declare(strict_types=1);

$projectPath = '/home/h422794/dimo';

function runCommand(string $command): string
{
    $output = [];
    $exitCode = 0;

    exec($command.' 2>&1', $output, $exitCode);

    return "Exit code: {$exitCode}\n".implode("\n", $output);
}

header('Content-Type: text/plain; charset=utf-8');

echo "=== Git Status ===\n\n";

echo runCommand(
    'cd '.escapeshellarg($projectPath).' && git status --short --branch'
);

echo "\n\n=== Current Commit ===\n\n";

echo runCommand(
    'cd '.escapeshellarg($projectPath).' && git rev-parse HEAD'
);
