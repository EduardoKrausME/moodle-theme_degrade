<?php
// create-static.php

if (php_sapi_name() !== 'cli') {
    exit("Este script deve ser executado via CLI.\n");
}

// Pergunta o nome do arquivo
echo "Digite o nome do arquivo/pasta: ";
$handle = fopen("php://stdin", "r");
$moduleTitle = trim(fgets($handle));
fclose($handle);

// Sanitiza o nome: mantém apenas caracteres alfanuméricos
$folderName = preg_replace("/[^-a-zA-Z0-9\s]/", "", $moduleTitle);
$folderName = preg_replace("/\s+/", "-", trim($moduleTitle));
$folderName = strtolower($folderName);

if (empty($folderName)) {
    exit("Nome inválido. Apenas caracteres alfanuméricos são permitidos.\n");
}

// Cria a pasta
$targetDir = "../../../{$folderName}";
if (!is_dir($targetDir)) {
    if (!mkdir($targetDir, 0777, true)) {
        exit("Não foi possível criar a pasta: $targetDir\n");
    }
    echo "Pasta criada: $targetDir\n";
} else {
    echo "A pasta já existe: $targetDir\n";
}

// Arquivos que precisam ser copiados
$files = ["editor.html", "editor-plugin.js", "info.json", "preview.html", "print.png", "style.scss"];
foreach ($files as $file) {
    $source = __DIR__ . DIRECTORY_SEPARATOR . $file;
    $destination = $targetDir . DIRECTORY_SEPARATOR . $file;

    if (!file_exists($source)) {
        echo "Aviso: arquivo não encontrado ao lado do script: $file\n";
        continue;
    }

    $sourcecontent = file_get_contents($source);

    $sourcecontent = str_replace("{moduleTitle}", $moduleTitle, $sourcecontent);
    $sourcecontent = str_replace("{folderName}", $folderName, $sourcecontent);
    file_put_contents($destination, $sourcecontent);
}

$stringlang ="\$stringlang['{$folderName}'] = '{$moduleTitle}';\n";
file_put_contents("../../lang/en.php", $stringlang, FILE_APPEND);

echo "Finalizado!\n";
