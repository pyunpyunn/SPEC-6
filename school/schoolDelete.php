<?php
    require_once("data/db.php");
    session_start();
    session_regenerate_id();

    $schoolID = $_GET['collid'];

    $dbStatement = $db->prepare("SELECT * FROM colleges WHERE collid = :schoolID");
    $dbStatement->execute(['schoolID' => $schoolID]);
    $school = $dbStatement->fetch();

    if(!$school) {
        header("Location: index.php?section=school&page=schoolList", true, 301);
        exit;
    }
?>
<h1>School Delete</h1>
<span>
    <?php echo $_SESSION['messages']['updateSuccess'] ?? null; ?>
    <?php echo $_SESSION['messages']['updateError'] ?? null; ?>
</span>
<form action="index.php?section=school&page=processDataChanges" method="post">
    <table>
        <tr>
            <td style="width: 10em;">School ID:</td>
            <td style="width: 30em;"><input type="text" id="schoolID" name="schoolID" value="<?php echo $school['collid']; ?>" readonly class="data-input"></td>
        </tr>
        <tr>
            <td>School Full Name:</td>
            <td><input type="text" id="schoolFullName" name="schoolFullName" value="<?php echo $school['collfullname']; ?>" readonly class="data-input"></td>
            <td>
                <span>
                    <?php echo $_SESSION['errors']['schoolFullName'] ?? null; ?>
                </span>
            </td>                
        </tr>
        <tr>
            <td>School Short Name:</td>
            <td><input type="text" id="schoolShortName" name="schoolShortName" value="<?php echo $school['collshortname']; ?>" readonly class="data-input"></td>
            <td>
                <span>
                    <?php echo $_SESSION['errors']['schoolShortName'] ?? null; ?>
                </span>
            </td>                
        </tr>
        <tr>
            <td colspan="2">
                <a href="index.php?section=school&page=schoolList" class="btn btn-primary">
                    Cancel Operation
                </a>                
                <button type="submit" name="confirmDelete" class="btn btn-danger">
                    Confirm Operation
                </button>
            </td>
        </tr>
    </table>
</form>    
