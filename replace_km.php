<?php

$dir = '/Users/rak/Herd/Rupp-Project-API/';
$files = [
    $dir . 'database/seeders/TranslationSeeder.php',
    $dir . 'database/seeders/LocaleSeeder.php',
    $dir . 'database/seeders/ContentBlockSeeder.php',
    $dir . 'database/seeders/ServiceCardSeeder.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($file);
    
    // Replace array keys and array access
    $content = str_replace("'kh' =>", "'kh' =>", $content);
    $content = str_replace('$translation[\'kh\']', '$translation[\'kh\']', $content);
    $content = str_replace("'languages.kh'", "'languages.kh'", $content);
    
    // Replace exact literal "kh" or 'kh'
    $content = preg_replace("/(['\"])kh\\1/", "\$1kh\$1", $content);
    
    file_put_contents($file, $content);
    echo "Updated: $file\n";
}
