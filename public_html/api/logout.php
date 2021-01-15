<?php
try {
    
    session_start();
    session_unset(); 
    // destroy the session 
    session_destroy(); 
    header('Location: /');
    exit();
}
catch(Exception $e) {
    header('Location: /');
}
?>