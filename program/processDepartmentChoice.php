<?php
if($_POST){
    $deptID = $_POST['deptID'] ?? null;
    $progcollid = $_POST['progcollid'] ?? null;

    $origin = $_SERVER['HTTP_REFERER'] ?? 'index.php?section=program&page=programList';

    if (empty($deptID)){
        header("Location: {$origin}", true, 302);
        exit;
    }

    header("Location: index.php?section=program&page=programListByDept&deptid={$deptID}&progcollid={$progcollid}", true, 302);
    exit;}