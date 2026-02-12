<?php
session_start();
session_regenerate_id();

$progid = $_GET['progid'] ?? null;
$deptid = $_GET['deptid'] ?? null;
$studcollid = $_GET['studcollid'] ?? null;
?>

<h1>Student Create</h1>
<span>
    <?php echo $_SESSION['messages']['createSuccess'] ?? null; ?>
    <?php echo $_SESSION['messages']['createError'] ?? null; ?>
</span>
<form action="index.php?section=student&page=processStudentData" method="post">
    <input type="hidden" name="progid" value="<?php echo htmlspecialchars($progid); ?>">
    <input type="hidden" name="deptid" value="<?php echo htmlspecialchars($deptid); ?>">
    <input type="hidden" name="studcollid" value="<?php echo htmlspecialchars($studcollid); ?>">
    
    <table>
        <tr>
            <td style="width: 10em;">School ID:</td>
            <td style="width: 30em;"><input type="text" id="schoolID" name="schoolID" value="" class="data-input"></td>
            <td>
                <span>
                    <?php echo $_SESSION['errors']['schoolID'] ?? null; ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>Student Full Name:</td>
            <td><input type="text" id="studentFullName" name="studentFullName" value="" class="data-input"></td>
            <td>
                <span>
                    <?php echo $_SESSION['errors']['studentFullName'] ?? null; ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>Student Middle Name:</td>
            <td><input type="text" id="studentMiddleName" name="studentMiddleName" value="" class="data-input"></td>
            <td>
                <span>
                    <?php echo $_SESSION['errors']['studentMiddleName'] ?? null; ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>Student Last Name:</td>
            <td><input type="text" id="studentLastName" name="studentLastName" value="" class="data-input"></td>
            <td>
                <span>
                    <?php echo $_SESSION['errors']['studentLastName'] ?? null; ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>Student Year:</td>
            <td><input type="text" id="studentYear" name="studentYear" value="" class="data-input"></td>
            <td>
                <span>
                    <?php echo $_SESSION['errors']['studentYear'] ?? null; ?>
                </span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit" name="saveNewStudentEntry" class="btn btn-primary">
                    Save New Student Entry
                </button>
                <button type="submit" name="clearEntries" class="btn">
                    Reset Form
                </button>
                <a href="index.php?section=student&page=studentListByProgram&progid=<?php echo htmlspecialchars($progid); ?>&deptid=<?php echo htmlspecialchars($deptid); ?>&studcollid=<?php echo htmlspecialchars($studcollid); ?>" class="btn btn-danger">
                    Exit
                </a>
            </td>
        </tr>
    </table>
</form>
