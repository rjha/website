<?php
    $s_time = microtime(true);
    echo $_SERVER["HTTP_HOST"];
    $e_time = microtime(true);
    printf(" \n <!-- Request %s took %f milliseconds --> \n", $originalURI, ($e_time - $s_time)*1000);

?>
