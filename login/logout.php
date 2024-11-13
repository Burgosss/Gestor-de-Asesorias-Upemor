<?php
    include '../Static/connect/db.php';

    session_start();
    session_destroy();
    header('Location: ../Index.html');
?>