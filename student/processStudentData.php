<?php
require_once("data/db.php");

session_start();
session_regenerate_id();

$entryURL = $_SERVER['HTTP_REFERER'];

// Ensure session containers exist to avoid undefined index warnings
if(!isset($_SESSION['errors']) || !is_array($_SESSION['errors'])){
    $_SESSION['errors'] = [];
}
if(!isset($_SESSION['input']) || !is_array($_SESSION['input'])){
    $_SESSION['input'] = [];
}
if(!isset($_SESSION['messages']) || !is_array($_SESSION['messages'])){
    $_SESSION['messages'] = [];
}

if($_POST && isset($_POST['clearEntries'])){
    $_SESSION['input']['schoolID'] = null;
    $_SESSION['input']['studentFullName'] = null;
    $_SESSION['input']['studentMiddleName'] = null;
    $_SESSION['input']['studentLastName'] = null;
    $_SESSION['input']['studentYear'] = null;
    $_SESSION['messages']['createSuccess'] = "";
    $_SESSION['messages']['createError'] = "";    

    $_SESSION['errors']['schoolID'] = "";
    $_SESSION['errors']['studentFullName'] = "";
    $_SESSION['errors']['studentMiddleName'] = "";
    $_SESSION['errors']['studentLastName'] = "";
    $_SESSION['errors']['studentYear'] = "";

    header("Location: $entryURL", true, 301);
}

if($_POST && isset($_POST['saveNewStudentEntry'])){
    $schoolID = $_POST['schoolID'];
    $studentFullName = $_POST['studentFullName'];
    $studentMiddleName = $_POST['studentMiddleName'];
    $studentLastName = $_POST['studentLastName'];
    $studentYear = $_POST['studentYear'];
    $progid = $_POST['progid'];
    $deptid = $_POST['deptid'];
    $studcollid = $_POST['studcollid'];

    $_SESSION['input']['schoolID'] = $schoolID;
    $_SESSION['input']['studentFullName'] = $studentFullName;
    $_SESSION['input']['studentMiddleName'] = $studentMiddleName;
    $_SESSION['input']['studentLastName'] = $studentLastName;
    $_SESSION['input']['studentYear'] = $studentYear;

    if(!isset($_SESSION['errors']) || !is_array($_SESSION['errors'])){
        $_SESSION['errors'] = [];
    }

    // Determine which college id to use: prefer explicit studcollid (hidden), fall back to schoolID input
    $collidToUse = null;
    if(isset($studcollid) && $studcollid !== ''){
        $collidToUse = $studcollid;
    } elseif(isset($schoolID) && $schoolID !== ''){
        $collidToUse = $schoolID;
    }

    // Validate college id (must be integer and exist in colleges table)
    $validCollid = filter_var($collidToUse, FILTER_VALIDATE_INT);
    if($validCollid === false || $validCollid === null){
        $_SESSION['errors']['schoolID'] = "Invalid School ID entry or format";
    } else {
        // Check existence in DB
        $checkStmt = $db->prepare("SELECT collid FROM colleges WHERE collid = :collid");
        $checkStmt->execute(['collid' => $validCollid]);
        $exists = $checkStmt->fetch();
        if(!$exists){
            $_SESSION['errors']['schoolID'] = "Selected School ID does not exist";
        } else {
            $_SESSION['errors']['schoolID'] = "";
            // ensure we use the validated integer
            $collidToUse = (int)$validCollid;
        }
    }

    // Validate Student Full Name (letters, spaces, hyphens)
    if(filter_input(INPUT_POST,'studentFullName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['studentFullName'] = "Invalid Full Name entry or format";
    } else {
        $_SESSION['errors']['studentFullName'] = "";
    }

    // Validate Student Middle Name (letters, spaces, hyphens)
    if(filter_input(INPUT_POST,'studentMiddleName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]*$/"]]) === false){
        $_SESSION['errors']['studentMiddleName'] = "Invalid Middle Name entry or format";
    } else {
        $_SESSION['errors']['studentMiddleName'] = "";
    }

    // Validate Student Last Name (letters, spaces, hyphens)
    if(filter_input(INPUT_POST,'studentLastName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['studentLastName'] = "Invalid Last Name entry or format";
    } else {
        $_SESSION['errors']['studentLastName'] = "";
    }

    // Validate Student Year (numeric)
    if(filter_input(INPUT_POST,'studentYear', FILTER_VALIDATE_INT) === false){
        $_SESSION['errors']['studentYear'] = "Invalid Year entry or format";
    } else {
        $_SESSION['errors']['studentYear'] = "";
    }

    if(empty($_SESSION['errors']['schoolID']) && empty($_SESSION['errors']['studentFullName']) && empty($_SESSION['errors']['studentMiddleName']) && empty($_SESSION['errors']['studentLastName']) && empty($_SESSION['errors']['studentYear'])){
        // Determine next student ID since `studid` is not auto-increment in schema
        $nextStmt = $db->query("SELECT COALESCE(MAX(studid), 0) + 1 AS nextid FROM students");
        $nextRow = $nextStmt->fetch();
        $nextId = $nextRow['nextid'] ?? 1;

        // Trim and normalize names
        $studentFullName = trim($studentFullName);
        $studentMiddleName = trim($studentMiddleName);
        $studentLastName = trim($studentLastName);

        // Check for existing student (prevent duplicates by name + program + college)
        $dupStmt = $db->prepare("SELECT studid FROM students WHERE studfirstname = :fn AND (studmidname = :mn OR (studmidname IS NULL AND :mn = '')) AND studlastname = :ln AND studprogid = :progid AND studcollid = :collid LIMIT 1");
        $dupStmt->execute([
            'fn' => $studentFullName,
            'mn' => $studentMiddleName ?? '',
            'ln' => $studentLastName,
            'progid' => $progid,
            'collid' => $collidToUse
        ]);
        $existing = $dupStmt->fetch();
        if($existing){
            $_SESSION['messages']['createError'] = "Student already exists (ID: {$existing['studid']}). No new record created.";
            header("Location: $entryURL", true, 301);
            exit;
        }

        // Insert student data using actual DB column names
        $dbStatement = $db->prepare("INSERT INTO students (studid, studcollid, studcolldeptid, studprogid, studfirstname, studmidname, studlastname, studyear) VALUES (:studid, :studcollid, :studcolldeptid, :studprogid, :studfirstname, :studmidname, :studlastname, :studyear)");
        $dbResult = $dbStatement->execute([
            'studid' => $nextId,
            'studcollid' => $collidToUse,
            'studcolldeptid' => $deptid,
            'studprogid' => $progid,
            'studfirstname' => $studentFullName,
            'studmidname' => $studentMiddleName,
            'studlastname' => $studentLastName,
            'studyear' => $studentYear
        ]);

        if($dbResult){
            // On success clear session and redirect to list for the program (use validated college id)
            $_SESSION['input'] = [];
            $_SESSION['errors'] = [];
            $_SESSION['messages'] = [];
            $redirColl = isset($collidToUse) ? $collidToUse : $studcollid;
            header("Location: index.php?section=student&page=studentListByProgram&progid={$progid}&deptid={$deptid}&studcollid={$redirColl}", true, 301);
            exit;
        } else {
            $_SESSION['messages']['createError'] = "Failed to create student entry";
            header("Location: $entryURL", true, 301);
        }
    } else {
        header("Location: $entryURL", true, 301);
    }
}

// Clear changes form (reset)
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

    header("Location: $entryURL", true, 301);
}

// Update student
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

    // Store input in session for repopulation (including student id)
    $_SESSION['input']['studentID'] = $newStudid;
    $_SESSION['input']['studentFullName'] = $studentFullName;
    $_SESSION['input']['studentMiddleName'] = $studentMiddleName;
    $_SESSION['input']['studentLastName'] = $studentLastName;
    $_SESSION['input']['studentYear'] = $studentYear;

    if(!isset($_SESSION['errors']) || !is_array($_SESSION['errors'])){
        $_SESSION['errors'] = [];
    }

    // Validate Student ID
    if(filter_var($newStudid, FILTER_VALIDATE_INT) === false){
        $_SESSION['errors']['studentID'] = "Invalid Student ID";
    } else {
        $_SESSION['errors']['studentID'] = "";
    }

    // Validate Student Full Name (letters, spaces, hyphens)
    if(filter_input(INPUT_POST,'studentFullName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['studentFullName'] = "Invalid Full Name entry or format";
    } else {
        $_SESSION['errors']['studentFullName'] = "";
    }

    // Validate Student Middle Name (letters, spaces, hyphens)
    if(filter_input(INPUT_POST,'studentMiddleName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]*$/"]]) === false){
        $_SESSION['errors']['studentMiddleName'] = "Invalid Middle Name entry or format";
    } else {
        $_SESSION['errors']['studentMiddleName'] = "";
    }

    // Validate Student Last Name (letters, spaces, hyphens)
    if(filter_input(INPUT_POST,'studentLastName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['studentLastName'] = "Invalid Last Name entry or format";
    } else {
        $_SESSION['errors']['studentLastName'] = "";
    }

    // Validate Student Year (numeric)
    if(filter_var($studentYear, FILTER_VALIDATE_INT) === false){
        $_SESSION['errors']['studentYear'] = "Invalid Year entry or format";
    } else {
        $_SESSION['errors']['studentYear'] = "";
    }

    if(empty($_SESSION['errors']['studentID']) && empty($_SESSION['errors']['studentFullName']) && empty($_SESSION['errors']['studentMiddleName']) && empty($_SESSION['errors']['studentLastName']) && empty($_SESSION['errors']['studentYear'])){
        // if origStudid not provided, fall back to newStudid
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
    header("Location: $entryURL", true, 301);
}

// Delete student
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
    header("Location: $redirectURL", true, 301);
}

