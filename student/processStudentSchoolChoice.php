<?php
if($_POST){
    $schoolID = $_POST['schoolID'] ?? null;
    $returnTo = $_POST['returnTo'] ?? null;

    $origin = $_SERVER['HTTP_REFERER'] ?? 'index.php?section=student&page=studentList';

    if (empty($schoolID)){
        header("Location: {$origin}", true, 302);
        exit;
    }
    
    if ($returnTo === 'student'){
        header("Location: index.php?section=student&page=studentList&studcollid={$schoolID}", true, 302);
        exit;
    }
}
