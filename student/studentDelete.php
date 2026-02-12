<?php
require_once("data/db.php");
session_start();
session_regenerate_id();

$studid = $_GET['studid'];
$stmt = $db->prepare("SELECT * FROM students WHERE studid = :studid");
$stmt->execute(['studid' => $studid]);
$s = $stmt->fetch();

if(!$s) {
    header("Location: index.php?section=student&page=studentList", true, 301);
    exit;
}
?>
<h1>Delete Student</h1>
<span>
    <?php echo $_SESSION['messages']['deleteSuccess'] ?? null; ?>
    <?php echo $_SESSION['messages']['deleteError'] ?? null; ?>
</span>
<form action="index.php?section=student&page=processStudentData" method="post">
    <input type="hidden" name="progid" value="<?php echo $s['studprogid']; ?>">
    <input type="hidden" name="deptid" value="<?php echo $s['studcolldeptid']; ?>">
    <input type="hidden" name="studcollid" value="<?php echo $s['studcollid']; ?>">
    <table>
        <tr>
            <td style="width: 10em;">Student ID:</td>
            <td style="width: 30em;"><input type="text" id="studid" name="studid" value="<?php echo $s['studid']; ?>" readonly class="data-input"></td>
        </tr>
        <tr>
            <td>First Name:</td>
            <td><input type="text" id="studentFullName" name="studentFullName" value="<?php echo $s['studfirstname']; ?>" readonly class="data-input"></td>
        </tr>
        <tr>
            <td>Middle Name:</td>
            <td><input type="text" id="studentMiddleName" name="studentMiddleName" value="<?php echo $s['studmidname']; ?>" readonly class="data-input"></td>
        </tr>
        <tr>
            <td>Last Name:</td>
            <td><input type="text" id="studentLastName" name="studentLastName" value="<?php echo $s['studlastname']; ?>" readonly class="data-input"></td>
        </tr>
        <tr>
            <td>Year:</td>
            <td><input type="text" id="studentYear" name="studentYear" value="<?php echo $s['studyear']; ?>" readonly class="data-input"></td>
        </tr>
        <tr>
            <td colspan="2">
                <a href="index.php?section=student&page=studentListByProgram&progid=<?php echo $s['studprogid']; ?>&deptid=<?php echo $s['studcolldeptid']; ?>&studcollid=<?php echo $s['studcollid']; ?>" class="btn btn-primary">
                    Cancel Operation
                </a>                
                <button type="submit" name="confirmDeleteStudent" class="btn btn-danger">
                    Confirm Delete
                </button>
            </td>
        </tr>
    </table>
</form>


