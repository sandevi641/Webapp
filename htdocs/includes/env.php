<?php
// includes/env.php
// Simple .env parser - reads ../.env by default
function load_env($path = __DIR__ . '/../.env') {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (!strpos($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $key = trim($key);
        $val = trim($val);
        // strip quotes if present
        if ((substr($val,0,1) === '"' && substr($val,-1) === '"') ||
            (substr($val,0,1) === "'" && substr($val,-1) === "'")) {
            $val = substr($val,1,-1);
        }
        if (!getenv($key)) putenv(sprintf("%s=%s", $key, $val));
        $_ENV[$key] = $val;
    }
}
