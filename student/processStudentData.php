<?php
require_once("data/db.php");

session_start();
session_regenerate_id();

$entryURL = $_SERVER['HTTP_REFERER'] ?? 'index.php?section=student&page=studentList';


if(!isset($_SESSION['errors']) || !is_array($_SESSION['errors'])) $_SESSION['errors'] = [];
if(!isset($_SESSION['input']) || !is_array($_SESSION['input'])) $_SESSION['input'] = [];
if(!isset($_SESSION['messages']) || !is_array($_SESSION['messages'])) $_SESSION['messages'] = [];


if($_POST && isset($_POST['clearEntries'])){
    $_SESSION['input']['studentID'] = null;
    $_SESSION['input']['studentFullName'] = null;
    $_SESSION['input']['studentMiddleName'] = null;
    $_SESSION['input']['studentLastName'] = null;
    $_SESSION['input']['studentYear'] = null;
    $_SESSION['messages']['createSuccess'] = "";
    $_SESSION['messages']['createError'] = "";

    $_SESSION['errors']['studentID'] = "";
    $_SESSION['errors']['studentFullName'] = "";
    $_SESSION['errors']['studentMiddleName'] = "";
    $_SESSION['errors']['studentLastName'] = "";
    $_SESSION['errors']['studentYear'] = "";

    header("Location: $entryURL", true, 301);
    exit;
}


if($_POST && isset($_POST['saveNewStudentEntry'])){
    $studentID = $_POST['studentID'] ?? null;
    $studentFullName = $_POST['studentFullName'] ?? '';
    $studentMiddleName = $_POST['studentMiddleName'] ?? '';
    $studentLastName = $_POST['studentLastName'] ?? '';
    $studentYear = $_POST['studentYear'] ?? null;
    $progid = $_POST['progid'] ?? null;
    $deptid = $_POST['deptid'] ?? null;
    $studcollid = $_POST['studcollid'] ?? null;

    $_SESSION['input']['studentID'] = $studentID;
    $_SESSION['input']['studentFullName'] = $studentFullName;
    $_SESSION['input']['studentMiddleName'] = $studentMiddleName;
    $_SESSION['input']['studentLastName'] = $studentLastName;
    $_SESSION['input']['studentYear'] = $studentYear;

    
    $errors = [];
    

    $collidToUse = $studcollid ?? null;
    if(filter_var($collidToUse, FILTER_VALIDATE_INT) === false){
        $errors['studentID'] = "Invalid college context for student";
    }

    
    if(filter_var($studentID, FILTER_VALIDATE_INT) === false){
        $errors['studentID'] = "Invalid Student ID";
    }

    
    if(filter_var($studentFullName, FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false) $errors['studentFullName'] = "Invalid Full Name entry or format";
    if(filter_var($studentMiddleName, FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]*$/"]]) === false) $errors['studentMiddleName'] = "Invalid Middle Name entry or format";
    if(filter_var($studentLastName, FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false) $errors['studentLastName'] = "Invalid Last Name entry or format";
    if(filter_var($studentYear, FILTER_VALIDATE_INT) === false) $errors['studentYear'] = "Invalid Year entry or format";

    
    foreach($errors as $k=>$v) $_SESSION['errors'][$k] = $v;

    if(empty($errors)){
        
        $collidToUse = (int)$collidToUse;

        
        $existsStmt = $db->prepare("SELECT studid FROM students WHERE studid = :studid LIMIT 1");
        $existsStmt->execute(['studid' => $studentID]);
        if($existsStmt->fetch()){
            $_SESSION['messages']['createError'] = "Student ID already exists. No new record created.";
            header("Location: $entryURL", true, 301);
            exit;
        }

        
        $stmt = $db->prepare("INSERT INTO students (studid, studcollid, studcolldeptid, studprogid, studfirstname, studmidname, studlastname, studyear) VALUES (:studid, :studcollid, :studcolldeptid, :studprogid, :studfirstname, :studmidname, :studlastname, :studyear)");
        $res = $stmt->execute([
            'studid' => $studentID,
            'studcollid' => $collidToUse,
            'studcolldeptid' => $deptid,
            'studprogid' => $progid,
            'studfirstname' => trim($studentFullName),
            'studmidname' => trim($studentMiddleName),
            'studlastname' => trim($studentLastName),
            'studyear' => $studentYear
        ]);

        if($res){
            $_SESSION['input'] = [];
            $_SESSION['errors'] = [];
            $_SESSION['messages'] = [];
            header("Location: index.php?section=student&page=studentListByProgram&progid={$progid}&deptid={$deptid}&studcollid={$collidToUse}", true, 302);
            exit;
        } else {
            $_SESSION['messages']['createError'] = "Failed to create student entry";
            header("Location: $entryURL", true, 302);
            exit;
        }
    }

    $redirectURL = "index.php?section=student&page=studentListByProgram&progid={$progid}&deptid={$deptid}&studcollid={$studcollid}";
    header("Location: {$redirectURL}", true, 302);
    exit;
}


if($_POST && isset($_POST['clearChanges'])){
    $_SESSION['input']['studentFullName'] = null;
    $_SESSION['input']['studentMiddleName'] = null;
    $_SESSION['input']['studentLastName'] = null;
    $_SESSION['input']['studentYear'] = null;
    $_SESSION['messages']['updateSuccess'] = "";
    $_SESSION['messages']['updateError'] = "";    

    $_SESSION['errors']['studentFullName'] = "";
    $_SESSION['errors']['studentMiddleName'] = "";
    $_SESSION['errors']['studentLastName'] = "";
    $_SESSION['errors']['studentYear'] = "";

    header("Location: $entryURL", true, 302);
    exit;
}


if($_POST && isset($_POST['saveStudentChanges'])){
    $newStudid = $_POST['studid'] ?? null;
    $origStudid = $_POST['origStudid'] ?? null;
    $studentFullName = $_POST['studentFullName'] ?? '';
    $studentMiddleName = $_POST['studentMiddleName'] ?? '';
    $studentLastName = $_POST['studentLastName'] ?? '';
    $studentYear = $_POST['studentYear'] ?? null;
    $progid = $_POST['progid'] ?? null;
    $deptid = $_POST['deptid'] ?? null;
    $studcollid = $_POST['studcollid'] ?? null;

    $_SESSION['input']['studentID'] = $newStudid;
    $_SESSION['input']['studentFullName'] = $studentFullName;
    $_SESSION['input']['studentMiddleName'] = $studentMiddleName;
    $_SESSION['input']['studentLastName'] = $studentLastName;
    $_SESSION['input']['studentYear'] = $studentYear;

    if(!isset($_SESSION['errors']) || !is_array($_SESSION['errors'])){
        $_SESSION['errors'] = [];
    }

    if(filter_var($newStudid, FILTER_VALIDATE_INT) === false){
        $_SESSION['errors']['studentID'] = "Invalid Student ID";
    } else {
        $_SESSION['errors']['studentID'] = "";
    }

    if(filter_input(INPUT_POST,'studentFullName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['studentFullName'] = "Invalid Full Name entry or format";
    } else {
        $_SESSION['errors']['studentFullName'] = "";
    }

    if(filter_input(INPUT_POST,'studentMiddleName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]*$/"]]) === false){
        $_SESSION['errors']['studentMiddleName'] = "Invalid Middle Name entry or format";
    } else {
        $_SESSION['errors']['studentMiddleName'] = "";
    }

    if(filter_input(INPUT_POST,'studentLastName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['studentLastName'] = "Invalid Last Name entry or format";
    } else {
        $_SESSION['errors']['studentLastName'] = "";
    }

    if(filter_var($studentYear, FILTER_VALIDATE_INT) === false){
        $_SESSION['errors']['studentYear'] = "Invalid Year entry or format";
    } else {
        $_SESSION['errors']['studentYear'] = "";
    }

    if(empty($_SESSION['errors']['studentID']) && empty($_SESSION['errors']['studentFullName']) && empty($_SESSION['errors']['studentMiddleName']) && empty($_SESSION['errors']['studentLastName']) && empty($_SESSION['errors']['studentYear'])){
        
        $whereId = $origStudid ?? $newStudid;
        $upd = $db->prepare("UPDATE students SET studid = :newid, studfirstname = :fn, studmidname = :mn, studlastname = :ln, studyear = :yr WHERE studid = :origid");
        $res = $upd->execute(['newid'=>$newStudid,'fn'=>$studentFullName,'mn'=>$studentMiddleName,'ln'=>$studentLastName,'yr'=>$studentYear,'origid'=>$whereId]);
        if($res){
            $_SESSION['messages']['updateSuccess'] = "Student updated successfully";
            $_SESSION['input'] = [];
            $_SESSION['errors'] = [];
        } else {
            $_SESSION['messages']['updateError'] = "Failed to update student";
        }
    }
    header("Location: $entryURL", true, 302);
    exit;
}

if($_POST && isset($_POST['confirmDeleteStudent'])){
    $studid = $_POST['studid'] ?? null;
    $progid = $_POST['progid'] ?? null;
    $deptid = $_POST['deptid'] ?? null;
    $studcollid = $_POST['studcollid'] ?? null;
    
    if($studid){
        $del = $db->prepare("DELETE FROM students WHERE studid = :studid");
        $res = $del->execute(['studid' => $studid]);
        if($res) {
            $_SESSION['messages']['deleteSuccess'] = "Student deleted";
        } else {
            $_SESSION['messages']['deleteError'] = "Failed to delete student";
        }
    }

    $redirectURL = "index.php?section=student&page=studentListByProgram&progid=$progid&deptid=$deptid&studcollid=$studcollid";
    header("Location: $redirectURL", true, 302);
    exit;
}
