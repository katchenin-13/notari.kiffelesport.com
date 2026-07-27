<?php

$file = __DIR__ . '/src/Controller/Actes/DossierController.php';
$content = file_get_contents($file);

// 1. Uncomment identification
$lines = explode("\n", $content);
$in_identification = false;
foreach ($lines as $i => $line) {
    if (strpos($line, "// #[Route('/dossier/{id}/identification'") !== false) {
        $in_identification = true;
    }
    if ($in_identification) {
        if (strpos($line, '/**') !== false && strpos($lines[$i+1] ?? '', '@Route("/dossier/{id}/redaction"') !== false) {
            $in_identification = false;
        }
    }
    
    if ($in_identification) {
        if (substr(trim($line), 0, 2) === '//') {
            $lines[$i] = preg_replace('#^(\s*)// ?#', '$1', $line);
        }
    }
}
$content = implode("\n", $lines);

// 2. Comment out CommentaireIdentification inside the newly uncommented block
$content = str_replace(
    'if(!$dossier->getCommentaireIdentifications()->count()){
            $commentaire = new CommentaireIdentification();
            $commentaire->setDescription("");
            $dossier->addCommentaireIdentification($commentaire);
        }',
    '// if(!$dossier->getCommentaireIdentifications()->count()){
        //     $commentaire = new CommentaireIdentification();
        //     $commentaire->setDescription("");
        //     $dossier->addCommentaireIdentification($commentaire);
        // }',
    $content
);

// 3. Add acte_const routes to annotations and attributes

// For annotations:
// @Route("/dossier/{id}/receuil-piece", name="acte_vente_piece", methods={"GET", "POST", "PUT"})
$content = preg_replace_callback(
    '/(\s*\*\s*@Route\(")(\/dossier\/\{id\}\/([a-z-]+))(",\s*name="acte_vente_([a-z_]+)",\s*methods=\{.*\}\))/',
    function($matches) {
        $original = $matches[0];
        // Generate the new route
        $new_path = $matches[2] . '-const';
        $new_name = 'acte_const_' . $matches[5];
        $new_route = $matches[1] . $new_path . $matches[4];
        // Replace name="acte_vente_..." with name="acte_const_..."
        $new_route = str_replace('name="acte_vente_', 'name="acte_const_', $new_route);
        return $original . "\n" . $new_route;
    },
    $content
);

// For attributes:
// #[Route('/dossier/{id}/identification', name: 'acte_vente_identification', methods: ['GET', 'POST', 'PUT'])]
$content = preg_replace_callback(
    '/(\s*#\[Route\([\'"])(\/dossier\/\{id\}\/([a-z-]+))([\'"],\s*name:\s*[\'"]acte_vente_([a-z_]+)[\'"],\s*methods:\s*\[.*\]\)\])/',
    function($matches) {
        $original = $matches[0];
        $new_path = $matches[2] . '-const';
        $new_name = 'acte_const_' . $matches[5];
        $new_route = $matches[1] . $new_path . $matches[4];
        // Replace name: 'acte_vente_...' with name: 'acte_const_...'
        $new_route = str_replace('name: \'acte_vente_', 'name: \'acte_const_', $new_route);
        $new_route = str_replace('name: "acte_vente_', 'name: "acte_const_', $new_route);
        return $original . "\n" . $new_route;
    },
    $content
);

file_put_contents($file, $content);
echo "Modification done!\n";
