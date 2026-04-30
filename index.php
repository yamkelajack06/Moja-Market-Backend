<?php
   require __DIR__ . '/config/db.php';

    Database::connect();
    $result = Database::queryDatabase("select exists (
    select * from person where age = 19
    );");


    if ($result != "") {
        $row = pg_fetch_row($result);
        echo "Query result: " . json_encode($row);
    }