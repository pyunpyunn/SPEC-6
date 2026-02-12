<?php
require_once("data/db.php");
session_start();
session_regenerate_id();

$studid = $_GET['studid'] ?? null;
$stmt = $db->prepare("SELECT * FROM students WHERE studid = :studid");
$stmt->execute(['studid' => $studid]);
$s = $stmt->fetch();

if(!$s) {
    header("Location: index.php?section=student&page=studentList", true, 301);
    exit;
}

if(empty($_SESSION['input']['studentID'])){
    unset($_SESSION['errors']['studentID']);
    unset($_SESSION['errors']['studentFullName']);
    unset($_SESSION['errors']['studentMiddleName']);
    unset($_SESSION['errors']['studentLastName']);
    unset($_SESSION['errors']['studentYear']);
}
?>
<h1>Student Update</h1>
<span id="pageMessages">
    <?php echo $_SESSION['messages']['updateSuccess'] ?? null; ?>
    <?php echo $_SESSION['messages']['updateError'] ?? null; ?>
</span>
<form action="index.php?section=student&page=processStudentData" method="post">
    <input type="hidden" name="progid" value="<?php echo $s['studprogid']; ?>">
    <input type="hidden" name="deptid" value="<?php echo $s['studcolldeptid']; ?>">
    <input type="hidden" name="studcollid" value="<?php echo $s['studcollid']; ?>">
    <input type="hidden" name="origStudid" value="<?php echo $s['studid']; ?>">
    <table>
        <tr>
            <td style="width: 10em;">Student ID:</td>
            <td style="width: 30em;"><input type="text" id="studid" name="studid" value="<?php echo $_SESSION['input']['studentID'] ?? $s['studid']; ?>" data-original="<?php echo htmlspecialchars($s['studid']); ?>" class="data-input"></td>
            <td><span><?php echo $_SESSION['errors']['studentID'] ?? null; ?></span></td>
        </tr>
        <tr>
            <td>First Name:</td>
            <td><input type="text" id="studentFullName" name="studentFullName" value="<?php echo $_SESSION['input']['studentFullName'] ?? $s['studfirstname']; ?>" data-original="<?php echo htmlspecialchars($s['studfirstname']); ?>" autofocus class="data-input"></td>
            <td>
                <span>
                    <?php echo $_SESSION['errors']['studentFullName'] ?? null; ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>Middle Name:</td>
            <td><input type="text" id="studentMiddleName" name="studentMiddleName" value="<?php echo $_SESSION['input']['studentMiddleName'] ?? $s['studmidname']; ?>" data-original="<?php echo htmlspecialchars($s['studmidname']); ?>" class="data-input"></td>
            <td>
                <span>
                    <?php echo $_SESSION['errors']['studentMiddleName'] ?? null; ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>Last Name:</td>
            <td><input type="text" id="studentLastName" name="studentLastName" value="<?php echo $_SESSION['input']['studentLastName'] ?? $s['studlastname']; ?>" data-original="<?php echo htmlspecialchars($s['studlastname']); ?>" class="data-input"></td>
            <td>
                <span>
                    <?php echo $_SESSION['errors']['studentLastName'] ?? null; ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>Year:</td>
            <td><input type="text" id="studentYear" name="studentYear" value="<?php echo $_SESSION['input']['studentYear'] ?? $s['studyear']; ?>" data-original="<?php echo htmlspecialchars($s['studyear']); ?>" class="data-input"></td>
            <td>
                <span>
                    <?php echo $_SESSION['errors']['studentYear'] ?? null; ?>
                </span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit" name="saveStudentChanges" class="btn">Update Student Entry</button>
                <button type="button" id="resetFormBtn" class="btn">Reset Form</button>
                <a href="index.php?section=student&page=studentListByProgram&progid=<?php echo $s['studprogid']; ?>&deptid=<?php echo $s['studcolldeptid']; ?>&studcollid=<?php echo $s['studcollid']; ?>" class="btn btn-danger">Exit</a>
            </td>
        </tr>
    </table>
</form>
<script>
document.getElementById('resetFormBtn').addEventListener('click', function(){
    
    ['studid','studentFullName','studentMiddleName','studentLastName','studentYear'].forEach(function(id){
        var el = document.getElementById(id);
        if(el) el.value = '';
    });
    
    var form = document.currentScript ? document.currentScript.previousElementSibling : document.querySelector('form');
    if(form){
        var spans = form.querySelectorAll('span');
        spans.forEach(function(sp){ sp.innerText = ''; });
    }
    
    var pm = document.getElementById('pageMessages');
    if(pm) pm.innerText = '';
});
</script>
