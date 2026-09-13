<?php

declare(strict_types=1);

$projectPath = '/home/h422794/dimo';

chdir($projectPath);

$output = [];
$exitCode = 0;

exec('/usr/bin/git status --short --branch 2>&1', $output, $exitCode);

$log = [
    '=== Git Status ===',
    'Exit code: '.$exitCode,
    implode(PHP_EOL, $output),
    '',
    '=== Current Commit ===',
    trim((string) shell_exec('/usr/bin/git rev-parse HEAD 2>&1')),
    '',
];

file_put_contents(
    $projectPath.'/storage/logs/deploy.log',
    implode(PHP_EOL, $log).PHP_EOL,
    FILE_APPEND
);
