<?php
require("protected/vendor/autoload.php");
$swagger = \Swagger\scan('protected/modules/api/');
header('Content-Type: application/json');
file_put_contents('res.json', $swagger);
echo 'bingo!';