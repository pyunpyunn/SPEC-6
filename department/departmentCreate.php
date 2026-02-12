<?php
session_start();
session_regenerate_id();
require_once("data/db.php");
// show any messages/errors
?>
<h1>Department Create</h1>
<span>
    <?php echo $_SESSION['messages']['createSuccess'] ?? null; ?>
    <?php echo $_SESSION['messages']['createError'] ?? null; ?>
</span>
<form action="index.php?section=department&page=processDepartmentData" method="post">
    <table>
        <tr>
            <td style="width: 10em;">Department ID:</td>
            <td style="width: 30em;"><input type="text" id="deptID" name="deptID" value="<?= $_SESSION['input']['deptID'] ?? null; ?>" class="data-input"></td>
            <td><span><?php echo $_SESSION['errors']['deptID'] ?? null; ?></span></td>
        </tr>
        <tr>
            <td>Department Full Name:</td>
            <td><input type="text" id="deptFullName" name="deptFullName" value="<?= $_SESSION['input']['deptFullName'] ?? null; ?>" class="data-input"></td>
            <td><span><?php echo $_SESSION['errors']['deptFullName'] ?? null; ?></span></td>
        </tr>
        <tr>
            <td>Department Short Name:</td>
            <td><input type="text" id="deptShortName" name="deptShortName" value="<?= $_SESSION['input']['deptShortName'] ?? null; ?>" class="data-input"></td>
            <td><span><?php echo $_SESSION['errors']['deptShortName'] ?? null; ?></span></td>
        </tr>
        <tr>
            <td>College ID:</td>
            <td><input type="text" id="deptCollID" name="deptCollID" value="<?= $_SESSION['input']['deptCollID'] ?? null; ?>" class="data-input"></td>
            <td><span><?php echo $_SESSION['errors']['deptCollID'] ?? null; ?></span></td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit" name="saveNewDepartmentEntry" class="btn">Save New Department Entry</button>
                <button type="submit" name="clearEntries" class="btn">Reset Form</button>
                <a href="index.php?section=department&page=departmentList&deptcollid=<?php echo urlencode($_SESSION['input']['deptCollID'] ?? ''); ?>" class="btn btn-danger">Exit</a>
            </td>
        </tr>
    </table>
</form>
