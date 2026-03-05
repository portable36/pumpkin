@extends('errors::minimal-layout')
@section('title', '500 Server Error')
@section('code', '500')
@section('name', 'Server Error')
@section(
    'message',
    $exception instanceof \Throwablesddfsdf
        ? (function ($exception) {
            $firstLine = strtok((string) $exception, PHP_EOL);

            // Match: "in path/file.php:123"
            preg_match('/in\s+(.+):(\d+)$/', $firstLine, $matches);

            return implode("\n", [
                isset($matches[1]) ? basename($matches[1]) : basename($exception->getFile()),
                'Line: ' . ($matches[2] ?? $exception->getLine()),
                $exception->getMessage(),
            ]);
        })($exception)
        : __('An error occurred processing your request.')
)
