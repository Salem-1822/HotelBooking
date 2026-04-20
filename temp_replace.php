<?php
$dirs = [
    __DIR__ . '/resources/views/super_admin',
    __DIR__ . '/app/Http/Controllers/Admin',
    __DIR__ . '/app/Http/Middleware',
];

function processDir($dir) {
    if (!is_dir($dir)) return;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php'])) {
            $content = file_get_contents($file->getPathname());
            $original = $content;
            
            $content = str_replace("route('admin.", "route('super_admin.", $content);
            $content = str_replace('route("admin.', 'route("super_admin.', $content);
            $content = str_replace("routeIs('admin.", "routeIs('super_admin.", $content);
            $content = str_replace('routeIs("admin.', 'routeIs("super_admin.', $content);
            $content = str_replace("view('admin.", "view('super_admin.", $content);
            $content = str_replace('view("admin.', 'view("super_admin.', $content);
            $content = str_replace("@extends('admin.", "@extends('super_admin.", $content);
            $content = str_replace('@extends("admin.', '@extends("super_admin.', $content);
            $content = str_replace("redirect()->route('admin.", "redirect()->route('super_admin.", $content);
            
            if ($content !== $original) {
                file_put_contents($file->getPathname(), $content);
                echo "Updated " . $file->getPathname() . "\n";
            }
        }
    }
}

foreach ($dirs as $dir) {
    processDir($dir);
}
