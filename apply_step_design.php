<?php
// Script to apply premium design to all step templates for both acte types

$dirs = ['acte_vente', 'acte_const'];
$base_dir = __DIR__ . '/templates/actes/dossier/';

foreach ($dirs as $dir) {
    $path_prefix = $base_dir . $dir . '/';
    
    // Process each .html.twig file in the dir
    foreach (glob($path_prefix . '*.html.twig') as $file) {
        $content = file_get_contents($file);
        
        // 1. Inject shared styles include after form_theme if not already present
        if (strpos($content, 'dossier_step_styles') === false && strpos($content, 'form_theme') !== false) {
            $content = preg_replace(
                '/(\{% form_theme[^%]+%\}\n?)/',
                "$1{{ include('_includes/dossier_step_styles.html.twig') }}\n",
                $content
            );
        }
        
        // 2. Replace old orangered h3 title style 
        $content = preg_replace(
            '/<h3 style="color: orangered">([^<]+)<\/h3>/',
            '<h3 class="step-title"><span class="step-title-bar"></span>$1</h3>',
            $content
        );
        
        // 3. Replace basic card with styled card (if not already done)
        if (strpos($content, 'border-radius:14px') === false) {
            $content = str_replace(
                '<div class="card">'."\n".'            <div class="card-body form-card">',
                '<div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden">'."\n".'            <div class="card-body form-card" style="padding:28px">',
                $content
            );
        }
        
        // 4. Replace old button row layout with action-row div
        $content = preg_replace(
            '/<br>\s*<div class="row">\s*<div class="col-xl-12">\s*<div class="text-right">\s*(.*?)\s*<\/div>\s*<\/div>\s*<\/div>/s',
            '<div class="action-row">$1</div>',
            $content
        );
        
        file_put_contents($file, $content);
        echo "Processed: $dir/" . basename($file) . "\n";
    }
}
echo "\nDone!\n";
