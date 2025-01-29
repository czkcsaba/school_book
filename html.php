<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>

<?php

function head(){
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Orai</title>
    </head>';
}

function displayBodyStart(){
    echo '<body>';
}

function displayNav(){
    echo '<form action="" method="POST">
    <button id="create" name="create">Adatbázis létrehozása</button>
</form>';
}

function displayBodyEnd(){
    echo '</body></html>';    
}

function displayMessage($message, $type){
    
}