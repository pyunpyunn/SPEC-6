<?php
require_once(__DIR__ . '/../data/db.php');
if($_POST){
    $deptID = $_POST['deptID'] ?? null;
    $progcollid = $_POST['progcollid'] ?? null;

    $origin = $_SERVER['HTTP_REFERER'] ?? 'index.php?section=program&page=programList';

    if (empty($deptID) || empty($progcollid)){
        header("Location: {$origin}", true, 302);
        exit;
    }

    
    $check = $db->prepare("SELECT deptid FROM departments WHERE deptid = :deptid AND deptcollid = :collid");
    $check->execute(['deptid' => $deptID, 'collid' => $progcollid]);
    $valid = $check->fetchColumn();

    if (!$valid) {
        
        header("Location: index.php?section=program&page=programList&progcollid={$progcollid}", true, 302);
        exit;
    }

    header("Location: index.php?section=program&page=programListByDept&deptid={$deptID}&progcollid={$progcollid}", true, 302);
    exit;
}