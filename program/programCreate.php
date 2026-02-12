<?php
require_once("data/db.php");
session_start();
session_regenerate_id();

$progcollid = $_GET['progcollid'] ?? null;
$deptid = $_GET['deptid'] ?? null;
?>
<h1>Program Create</h1>
<span>
    <?php echo $_SESSION['messages']['createSuccess'] ?? null; ?>
    <?php echo $_SESSION['messages']['createError'] ?? null; ?>
</span>
<form action="index.php?section=program&page=processProgramData" method="post">
    <input type="hidden" name="progCollID" value="<?php echo htmlspecialchars($progcollid); ?>">
    <input type="hidden" name="progDeptID" value="<?php echo htmlspecialchars($deptid); ?>">
    <table>
        <tr>
            <td style="width: 10em;">Program ID:</td>
            <td style="width: 30em;"><input type="text" id="progID" name="progID" value="<?= $_SESSION['input']['progID'] ?? null; ?>" class="data-input"></td>
            <td><span><?php echo $_SESSION['errors']['progID'] ?? null; ?></span></td>
        </tr>
        <tr>
            <td>Program Full Name:</td>
            <td><input type="text" id="progFullName" name="progFullName" value="<?= $_SESSION['input']['progFullName'] ?? null; ?>" class="data-input"></td>
            <td><span><?php echo $_SESSION['errors']['progFullName'] ?? null; ?></span></td>
        </tr>
        <tr>
            <td>Program Short Name:</td>
            <td><input type="text" id="progShortName" name="progShortName" value="<?= $_SESSION['input']['progShortName'] ?? null; ?>" class="data-input"></td>
            <td><span><?php echo $_SESSION['errors']['progShortName'] ?? null; ?></span></td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit" name="saveNewProgramEntry" class="btn">Save New Program Entry</button>
                <button type="submit" name="clearEntries" class="btn">Reset Form</button>
                <a href="index.php?section=program&page=programListByDept&progcollid=<?php echo htmlspecialchars($progcollid); ?>&deptid=<?php echo htmlspecialchars($deptid); ?>" class="btn btn-danger">Exit</a>
            </td>
        </tr>
    </table>
</form>
