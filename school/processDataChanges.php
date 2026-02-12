<?php
require_once("data/db.php");

session_start();
session_regenerate_id();

$entryURL = $_SERVER['HTTP_REFERER'];

if($_POST && isset($_POST['clearChanges'])){
    $_SESSION['errors']['schoolFullName'] = "";
    $_SESSION['errors']['schoolShortName'] = "";
    $_SESSION['messages']['updateSuccess'] = "";
    $_SESSION['messages']['updateError'] = "";

    header("Location: $entryURL", true, 301);
}

if($_POST && isset($_POST['saveChanges'])){
    $schoolID = $_POST['schoolID'];
    $schoolFullName = $_POST['schoolFullName'];
    $schoolShortName = $_POST['schoolShortName'];

    $_SESSION['input']['schoolFullName'] = $schoolFullName;
    $_SESSION['input']['schoolShortName'] = $schoolShortName;

    if(isset($_SESSION['errors'])){
        $_SESSION['errors'] = [];
    }

    if(filter_input(INPUT_POST,'schoolFullName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['schoolFullName'] = "Invalid Full Name entry. Reverting to original value";
    } else {
        $_SESSION['errors']['schoolFullName'] = "";
    }

    if(filter_input(INPUT_POST,'schoolShortName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['schoolShortName'] = "Invalid Short Name entry. Reverting to original value";
    } else {
        $_SESSION['errors']['schoolShortName'] = "";
    }

    if(empty($_SESSION['errors']['schoolFullName']) && empty($_SESSION['errors']['schoolShortName'])){
        
        $dbStatement = $db->prepare('UPDATE colleges SET collfullname = ?, collshortname = ? WHERE collid = ?');
        $dbResult = $dbStatement->execute([
            $schoolFullName,
            $schoolShortName,
            $schoolID
        ]);

        if($dbResult){
            $_SESSION['messages']['updateSuccess'] = "School entry updated successfully";
            $_SESSION['messages']['updateError'] = "";
        } else {
            $_SESSION['messages']['updateError'] = "Failed to update school entry";
            $_SESSION['messages']['updateSuccess'] = "";
        }

        header("Location: $entryURL", true, 301);

    } else {
        header("Location: $entryURL", true, 301);
    }
}

if($_POST && isset($_POST['confirmDelete'])){
    $schoolID = $_POST['schoolID'] ?? null;
    if($schoolID){
        try {
            $db->beginTransaction();

            $delStudents = $db->prepare("DELETE FROM students WHERE studcollid = :collid");
            $delStudents->execute(['collid' => $schoolID]);

            $delPrograms = $db->prepare("DELETE FROM programs WHERE progcollid = :collid");
            $delPrograms->execute(['collid' => $schoolID]);

            $delDepartments = $db->prepare("DELETE FROM departments WHERE deptcollid = :collid");
            $delDepartments->execute(['collid' => $schoolID]);

            $delCollege = $db->prepare("DELETE FROM colleges WHERE collid = :collid");
            $res = $delCollege->execute(['collid' => $schoolID]);

            $db->commit();

            if($res){
                $_SESSION['messages']['deleteSuccess'] = "School deleted";
                $_SESSION['messages']['deleteError'] = "";
            } else {
                $_SESSION['messages']['deleteError'] = "Failed to delete school";
                $_SESSION['messages']['deleteSuccess'] = "";
            }
        } catch (Exception $e) {
            if($db->inTransaction()) $db->rollBack();
            $_SESSION['messages']['deleteError'] = "Failed to delete school: " . $e->getMessage();
            $_SESSION['messages']['deleteSuccess'] = "";
        }
    }

    header("Location: index.php?section=school&page=schoolList", true, 302);
    exit;
}

?>