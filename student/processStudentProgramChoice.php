<?php
require_once(__DIR__ . '/../data/db.php');
if($_POST){
    $progID = $_POST['progID'] ?? null;
    $deptid = $_POST['deptid'] ?? null;
    $studcollid = $_POST['studcollid'] ?? null;

    $origin = $_SERVER['HTTP_REFERER'] ?? 'index.php?section=student&page=studentList';

    if (empty($progID) || empty($deptid) || empty($studcollid)){
        header("Location: {$origin}", true, 302);
        exit;
    }

    
    $check = $db->prepare("SELECT progid FROM programs WHERE progid = :progid AND progcolldeptid = :deptid");
    $check->execute(['progid' => $progID, 'deptid' => $deptid]);
    $validProg = $check->fetchColumn();

    if (!$validProg) {
        
        header("Location: index.php?section=student&page=studentList&deptid={$deptid}&studcollid={$studcollid}", true, 302);
        exit;
    }

    header("Location: index.php?section=student&page=studentListByProgram&progid={$progID}&deptid={$deptid}&studcollid={$studcollid}", true, 302);
    exit;
}
