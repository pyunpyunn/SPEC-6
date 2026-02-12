<?php
require_once(__DIR__ . '/../data/db.php');
if($_POST){
    $deptID = $_POST['deptID'] ?? null;
    $studcollid = $_POST['studcollid'] ?? null;

    $origin = $_SERVER['HTTP_REFERER'] ?? 'index.php?section=student&page=studentList';

    if (empty($deptID) || empty($studcollid)){
        header("Location: {$origin}", true, 302);
        exit;
    }

    $check = $db->prepare("SELECT deptid FROM departments WHERE deptid = :deptid AND deptcollid = :collid");
    $check->execute(['deptid' => $deptID, 'collid' => $studcollid]);
    $valid = $check->fetchColumn();

    if (!$valid) {
        
        header("Location: index.php?section=student&page=studentList&studcollid={$studcollid}", true, 302);
        exit;
    }

    header("Location: index.php?section=student&page=studentList&deptid={$deptID}&studcollid={$studcollid}", true, 302);
    exit;
}
