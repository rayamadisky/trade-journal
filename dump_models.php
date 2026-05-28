<?php
$data = file_get_contents('https://generativelanguage.googleapis.com/v1beta/models?key=AIzaSyAAZ_hDCbDovzQT-JsUkGxQRhuEhC4ZZGU');
file_put_contents('models_clean.json', $data);
echo "Done\n";
