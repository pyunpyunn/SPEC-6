<?php
if($_POST){
    $schoolID = $_POST['schoolID'] ?? null;
    $returnTo = $_POST['returnTo'] ?? null;

    $origin = $_SERVER['HTTP_REFERER'] ?? 'index.php?section=department&page=chooseSchool';

    if (empty($schoolID)){
        header("Location: {$origin}", true, 302);
        exit;
    }

    // If caller requested to return to program flow, redirect there
    if ($returnTo === 'program'){
        header("Location: index.php?section=program&page=programList&progcollid={$schoolID}", true, 302);
        exit;
    }

    // Default: department flow
    header("Location: index.php?section=department&page=departmentList&deptcollid={$schoolID}", true, 302);
    exit;

}