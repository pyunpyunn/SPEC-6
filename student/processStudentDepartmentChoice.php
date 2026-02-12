<?php
if($_POST){
    $deptID = $_POST['deptID'] ?? null;
    $studcollid = $_POST['studcollid'] ?? null;

    $origin = $_SERVER['HTTP_REFERER'] ?? 'index.php?section=student&page=studentList';

    if (empty($deptID)){
        header("Location: {$origin}", true, 302);
        exit;
    }

    header("Location: index.php?section=student&page=studentList&deptid={$deptID}&studcollid={$studcollid}", true, 302);
    exit;
}
