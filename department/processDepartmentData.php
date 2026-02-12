<?php
require_once("data/db.php");
session_start();
session_regenerate_id();

$entryURL = $_SERVER['HTTP_REFERER'];

if($_POST && isset($_POST['clearEntries'])){
    $_SESSION['input']['deptID'] = null;
    $_SESSION['input']['deptFullName'] = null;
    $_SESSION['input']['deptShortName'] = null;
    $_SESSION['input']['deptCollID'] = null;
    $_SESSION['messages']['createSuccess'] = "";
    $_SESSION['messages']['createError'] = "";
    $_SESSION['errors']['deptID'] = "";
    $_SESSION['errors']['deptFullName'] = "";
    $_SESSION['errors']['deptShortName'] = "";
    $_SESSION['errors']['deptCollID'] = "";
    header("Location: $entryURL", true, 301);
}

if($_POST && isset($_POST['saveNewDepartmentEntry'])){
    $deptID = $_POST['deptID'];
    $deptFullName = $_POST['deptFullName'];
    $deptShortName = $_POST['deptShortName'];
    $deptCollID = $_POST['deptCollID'];

    $_SESSION['input']['deptID'] = $deptID;
    $_SESSION['input']['deptFullName'] = $deptFullName;
    $_SESSION['input']['deptShortName'] = $deptShortName;
    $_SESSION['input']['deptCollID'] = $deptCollID;

    if(!isset($_SESSION['errors']) || !is_array($_SESSION['errors'])) $_SESSION['errors'] = [];

    if(filter_var($deptID, FILTER_VALIDATE_INT) === false){
        $_SESSION['errors']['deptID'] = "Invalid ID entry or format";
    } else $_SESSION['errors']['deptID'] = "";

    if(filter_var($deptFullName, FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['deptFullName'] = "Invalid Full Name entry or format";
    } else $_SESSION['errors']['deptFullName'] = "";

    if(filter_var($deptShortName, FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['deptShortName'] = "Invalid Short Name entry or format";
    } else $_SESSION['errors']['deptShortName'] = "";

    if(filter_var($deptCollID, FILTER_VALIDATE_INT) === false){
        $_SESSION['errors']['deptCollID'] = "Invalid College ID";
    } else {
        // check college exists
        $chk = $db->prepare("SELECT collid FROM colleges WHERE collid = :collid");
        $chk->execute(['collid' => $deptCollID]);
        if(!$chk->fetch()) $_SESSION['errors']['deptCollID'] = "Selected College does not exist";
        else $_SESSION['errors']['deptCollID'] = "";
    }

    if(empty($_SESSION['errors']['deptID']) && empty($_SESSION['errors']['deptFullName']) && empty($_SESSION['errors']['deptShortName']) && empty($_SESSION['errors']['deptCollID'])){
        $stmt = $db->prepare("INSERT INTO departments (deptid, deptfullname, deptshortname, deptcollid) VALUES (:deptid, :deptfullname, :deptshortname, :deptcollid)");
        $res = $stmt->execute(['deptid'=>$deptID,'deptfullname'=>$deptFullName,'deptshortname'=>$deptShortName,'deptcollid'=>$deptCollID]);
        if($res) $_SESSION['messages']['createSuccess'] = "Department entry created successfully";
        else $_SESSION['messages']['createError'] = "Failed to create department entry";
        header("Location: $entryURL", true, 301);
    } else header("Location: $entryURL", true, 301);
}

// Clear changes form (reset)
if($_POST && isset($_POST['clearChanges'])){
    $_SESSION['input']['deptFullName'] = null;
    $_SESSION['input']['deptShortName'] = null;
    $_SESSION['input']['deptCollID'] = null;
    $_SESSION['messages']['updateSuccess'] = "";
    $_SESSION['messages']['updateError'] = "";
    $_SESSION['errors']['deptFullName'] = "";
    $_SESSION['errors']['deptShortName'] = "";
    $_SESSION['errors']['deptCollID'] = "";
    header("Location: $entryURL", true, 301);
}

if($_POST && isset($_POST['saveDepartmentChanges'])){
    $deptID = $_POST['deptID'] ?? null;
    $deptFullName = $_POST['deptFullName'] ?? '';
    $deptShortName = $_POST['deptShortName'] ?? '';
    $deptCollID = $_POST['deptCollID'] ?? $_POST['collid'] ?? null;

    // Store input in session
    $_SESSION['input']['deptFullName'] = $deptFullName;
    $_SESSION['input']['deptShortName'] = $deptShortName;
    $_SESSION['input']['deptCollID'] = $deptCollID;

    if(!isset($_SESSION['errors']) || !is_array($_SESSION['errors'])){
        $_SESSION['errors'] = [];
    }

    // Validate Department Full Name
    if(filter_var($deptFullName, FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['deptFullName'] = "Invalid Full Name entry or format";
    } else {
        $_SESSION['errors']['deptFullName'] = "";
    }

    // Validate Department Short Name
    if(filter_var($deptShortName, FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['deptShortName'] = "Invalid Short Name entry or format";
    } else {
        $_SESSION['errors']['deptShortName'] = "";
    }

    // Validate College ID
    if(filter_var($deptCollID, FILTER_VALIDATE_INT) === false){
        $_SESSION['errors']['deptCollID'] = "Invalid College ID";
    } else {
        $_SESSION['errors']['deptCollID'] = "";
    }

    if(empty($_SESSION['errors']['deptFullName']) && empty($_SESSION['errors']['deptShortName']) && empty($_SESSION['errors']['deptCollID'])){
        $stmt = $db->prepare("UPDATE departments SET deptfullname = :deptfullname, deptshortname = :deptshortname WHERE deptid = :deptid");
        $res = $stmt->execute(['deptfullname'=>$deptFullName,'deptshortname'=>$deptShortName,'deptid'=>$deptID]);
        if($res){
            $_SESSION['messages']['updateSuccess'] = "Department entry updated successfully";
            $_SESSION['input'] = [];
            $_SESSION['errors'] = [];
        } else {
            $_SESSION['messages']['updateError'] = "Failed to update department entry";
        }
    }
    header("Location: $entryURL", true, 301);
}

if($_POST && isset($_POST['confirmDeleteDepartment'])){
    $deptid = $_POST['deptid'];
    // consider hierarchy: delete programs and students under this department
    // First delete students of programs under this dept
    $delStudents = $db->prepare("DELETE s FROM students s JOIN programs p ON s.studprogid = p.progid WHERE p.progcolldeptid = :deptid");
    $delStudents->execute(['deptid' => $deptid]);
    // delete programs under dept
    $delPrograms = $db->prepare("DELETE FROM programs WHERE progcolldeptid = :deptid");
    $delPrograms->execute(['deptid' => $deptid]);
    // delete department
    $delDept = $db->prepare("DELETE FROM departments WHERE deptid = :deptid");
    $res = $delDept->execute(['deptid' => $deptid]);
    if($res) $_SESSION['messages']['updateSuccess'] = "Department deleted";
    else $_SESSION['messages']['updateError'] = "Failed to delete department";
    header("Location: $entryURL", true, 301);
}
