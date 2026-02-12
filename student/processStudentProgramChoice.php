<?php
if($_POST){
    $progID = $_POST['progID'] ?? null;
    $deptid = $_POST['deptid'] ?? null;
    $studcollid = $_POST['studcollid'] ?? null;

    $origin = $_SERVER['HTTP_REFERER'] ?? 'index.php?section=student&page=studentList';

    if (empty($progID)){
        header("Location: {$origin}", true, 302);
        exit;
    }

    header("Location: index.php?section=student&page=studentListByProgram&progid={$progID}&deptid={$deptid}&studcollid={$studcollid}", true, 302);
    exit;
}
