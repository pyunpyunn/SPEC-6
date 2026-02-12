<?php
require_once("data/db.php");
session_start();
session_regenerate_id();

$entryURL = $_SERVER['HTTP_REFERER'];

if($_POST && isset($_POST['clearEntries'])){
    $_SESSION['input']['progID'] = null;
    $_SESSION['input']['progFullName'] = null;
    $_SESSION['input']['progShortName'] = null;
    $_SESSION['input']['progCollID'] = null;
    $_SESSION['input']['progDeptID'] = null;
    $_SESSION['messages']['createSuccess'] = "";
    $_SESSION['messages']['createError'] = "";
    $_SESSION['errors']['progID'] = "";
    $_SESSION['errors']['progFullName'] = "";
    $_SESSION['errors']['progShortName'] = "";
    $_SESSION['errors']['progCollID'] = "";
    $_SESSION['errors']['progDeptID'] = "";
    header("Location: $entryURL", true, 301);
}

if($_POST && isset($_POST['saveNewProgramEntry'])){
    $progID = $_POST['progID'];
    $progFullName = $_POST['progFullName'];
    $progShortName = $_POST['progShortName'];
    $progCollID = $_POST['progCollID'];
    $progDeptID = $_POST['progDeptID'];

    $_SESSION['input']['progID'] = $progID;
    $_SESSION['input']['progFullName'] = $progFullName;
    $_SESSION['input']['progShortName'] = $progShortName;
    $_SESSION['input']['progCollID'] = $progCollID;
    $_SESSION['input']['progDeptID'] = $progDeptID;

    if(!isset($_SESSION['errors']) || !is_array($_SESSION['errors'])) $_SESSION['errors'] = [];

    if(filter_var($progID, FILTER_VALIDATE_INT) === false){
        $_SESSION['errors']['progID'] = "Invalid ID entry or format";
    } else $_SESSION['errors']['progID'] = "";

    if(filter_var($progFullName, FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\\s\\-]+$/"]]) === false){
        $_SESSION['errors']['progFullName'] = "Invalid Full Name entry or format";
    } else $_SESSION['errors']['progFullName'] = "";

    if(filter_var($progShortName, FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\\s\\-]+$/"]]) === false){
        $_SESSION['errors']['progShortName'] = "Invalid Short Name entry or format";
    } else $_SESSION['errors']['progShortName'] = "";

    if(filter_var($progCollID, FILTER_VALIDATE_INT) === false){
        $_SESSION['errors']['progCollID'] = "Invalid College ID";
    } else $_SESSION['errors']['progCollID'] = "";

    if(filter_var($progDeptID, FILTER_VALIDATE_INT) === false){
        $_SESSION['errors']['progDeptID'] = "Invalid Department ID";
    } else $_SESSION['errors']['progDeptID'] = "";

    if(empty($_SESSION['errors']['progID']) && empty($_SESSION['errors']['progFullName']) && empty($_SESSION['errors']['progShortName']) && empty($_SESSION['errors']['progCollID']) && empty($_SESSION['errors']['progDeptID'])){
        $stmt = $db->prepare("INSERT INTO programs (progid, progfullname, progshortname, progcollid, progcolldeptid) VALUES (:progid, :progfullname, :progshortname, :progcollid, :progcolldeptid)");
        $res = $stmt->execute(['progid'=>$progID,'progfullname'=>$progFullName,'progshortname'=>$progShortName,'progcollid'=>$progCollID,'progcolldeptid'=>$progDeptID]);
        if($res) $_SESSION['messages']['createSuccess'] = "Program entry created successfully";
        else $_SESSION['messages']['createError'] = "Failed to create program entry";
        header("Location: $entryURL", true, 301);
    } else header("Location: $entryURL", true, 301);
}

// Clear changes form (reset)
if($_POST && isset($_POST['clearChanges'])){
    $_SESSION['input']['progFullName'] = null;
    $_SESSION['input']['progShortName'] = null;
    $_SESSION['messages']['updateSuccess'] = "";
    $_SESSION['messages']['updateError'] = "";
    $_SESSION['errors']['progFullName'] = "";
    $_SESSION['errors']['progShortName'] = "";
    header("Location: $entryURL", true, 301);
}

if($_POST && isset($_POST['saveProgramChanges'])){
    $progID = $_POST['progID'] ?? null;
    $progFullName = $_POST['progFullName'] ?? '';
    $progShortName = $_POST['progShortName'] ?? '';
    $progCollID = $_POST['progCollID'] ?? null;
    $progDeptID = $_POST['progDeptID'] ?? null;

    // Store input in session
    $_SESSION['input']['progFullName'] = $progFullName;
    $_SESSION['input']['progShortName'] = $progShortName;

    if(!isset($_SESSION['errors']) || !is_array($_SESSION['errors'])){
        $_SESSION['errors'] = [];
    }

    // Validate Program Full Name
    if(filter_var($progFullName, FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['progFullName'] = "Invalid Full Name entry or format";
    } else {
        $_SESSION['errors']['progFullName'] = "";
    }

    // Validate Program Short Name
    if(filter_var($progShortName, FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['progShortName'] = "Invalid Short Name entry or format";
    } else {
        $_SESSION['errors']['progShortName'] = "";
    }

    if(empty($_SESSION['errors']['progFullName']) && empty($_SESSION['errors']['progShortName'])){
        $stmt = $db->prepare("UPDATE programs SET progfullname = :progfullname, progshortname = :progshortname WHERE progid = :progid");
        $res = $stmt->execute(['progfullname'=>$progFullName,'progshortname'=>$progShortName,'progid'=>$progID]);
        if($res){
            $_SESSION['messages']['updateSuccess'] = "Program entry updated successfully";
            $_SESSION['input'] = [];
            $_SESSION['errors'] = [];
        } else {
            $_SESSION['messages']['updateError'] = "Failed to update program entry";
        }
    }
    header("Location: $entryURL", true, 301);
}


if($_POST && isset($_POST['confirmDeleteProgram'])){
    $progid = $_POST['progid'];
    // delete students under this program
    $delStudents = $db->prepare("DELETE FROM students WHERE studprogid = :progid");
    $delStudents->execute(['progid'=>$progid]);
    // delete program
    $delProg = $db->prepare("DELETE FROM programs WHERE progid = :progid");
    $res = $delProg->execute(['progid'=>$progid]);
    if($res) $_SESSION['messages']['deleteSuccess'] = "Program deleted";
    else $_SESSION['messages']['deleteError'] = "Failed to delete program";
    header("Location: $entryURL", true, 301);
}
