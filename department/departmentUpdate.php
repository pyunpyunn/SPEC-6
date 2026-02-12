<?php
require_once("data/db.php");
session_start();
session_regenerate_id();

$deptid = $_GET['deptid'] ?? null;
$stmt = $db->prepare("SELECT * FROM departments WHERE deptid = :deptid");
$stmt->execute(['deptid' => $deptid]);
$dept = $stmt->fetch();

if(!$dept) {
    header("Location: index.php?section=department&page=departmentList", true, 301);
    exit;
}
?>
<h1>Department Update</h1>
<span>
    <?php echo $_SESSION['messages']['updateSuccess'] ?? null; ?>
    <?php echo $_SESSION['messages']['updateError'] ?? null; ?>
</span>
<form action="index.php?section=department&page=processDepartmentData" method="post">
    <table>
        <tr>
            <td style="width: 10em;">Department ID:</td>
            <td style="width: 30em;"><input type="text" id="deptID" name="deptID" value="<?php echo $dept['deptid']; ?>" readonly class="data-input"></td>
        </tr>
        <tr>
            <td>Department Full Name:</td>
            <td><input type="text" id="deptFullName" name="deptFullName" value="<?php echo $_SESSION['input']['deptFullName'] ?? $dept['deptfullname']; ?>" class="data-input"></td>
            <td><span><?php echo $_SESSION['errors']['deptFullName'] ?? null; ?></span></td>
        </tr>
        <tr>
            <td>Department Short Name:</td>
            <td><input type="text" id="deptShortName" name="deptShortName" value="<?php echo $_SESSION['input']['deptShortName'] ?? $dept['deptshortname']; ?>" class="data-input"></td>
            <td><span><?php echo $_SESSION['errors']['deptShortName'] ?? null; ?></span></td>
        </tr>
        <tr>
            <td>College ID:</td>
            <td><input type="text" id="deptCollID" name="deptCollID" value="<?php echo $_SESSION['input']['deptCollID'] ?? $dept['deptcollid']; ?>" readonly class="data-input"></td>
            <td><span><?php echo $_SESSION['errors']['deptCollID'] ?? null; ?></span></td>
        </tr>
        <input type="hidden" name="collid" value="<?php echo $dept['deptcollid']; ?>">
        <tr>
            <td colspan="2">
                <button type="submit" name="saveDepartmentChanges" class="btn">Update Department Entry</button>
                <button type="submit" name="clearChanges" class="btn">Reset Form</button>
                <a href="index.php?section=department&page=departmentList&deptcollid=<?php echo $dept['deptcollid']; ?>" class="btn btn-danger">Exit</a>
            </td>
        </tr>
    </table>
</form>
