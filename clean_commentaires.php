<?php

$dirs = ['acte_vente', 'acte_const'];
$files = ['redaction.html.twig', 'piece.html.twig', 'identification.html.twig', 'paiement.html.twig'];

$base_dir = __DIR__ . '/templates/actes/dossier/';

foreach ($dirs as $dir) {
    foreach ($files as $file) {
        $path = $base_dir . $dir . '/' . $file;
        if (file_exists($path)) {
            $content = file_get_contents($path);
            
            // Remove the import statement
            $content = preg_replace('/\{%\s*from\s*[\'"][^\'"]+[\'"]\s*import.*prototype_commentaire.*%}\n?/', '', $content);
            
            // Remove the macro call
            $content = preg_replace('/\{\{\s*prototype_commentaire\(form\)\s*\}\}\n?/', '', $content);
            
            file_put_contents($path, $content);
            echo "Cleaned $dir/$file\n";
        }
    }
}
