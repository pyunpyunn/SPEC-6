<?php
require_once("data/db.php");
session_start();
session_regenerate_id();

$progid = $_GET['progid'] ?? null;
$stmt = $db->prepare("SELECT * FROM programs WHERE progid = :progid");
$stmt->execute(['progid' => $progid]);
$prog = $stmt->fetch();

if(!$prog) {
    header("Location: index.php?section=program&page=programList", true, 301);
    exit;
}
// Clear program-specific session errors on initial GET (unless user submitted form)
if(empty($_SESSION['input']['progFullName'])){
    unset($_SESSION['errors']['progFullName']);
    unset($_SESSION['errors']['progShortName']);
}
?>
<h1>Program Update</h1>
<span id="pageMessages">
    <?php echo $_SESSION['messages']['updateSuccess'] ?? null; ?>
    <?php echo $_SESSION['messages']['updateError'] ?? null; ?>
</span>
<form action="index.php?section=program&page=processProgramData" method="post">
    <input type="hidden" name="progCollID" value="<?php echo htmlspecialchars($prog['progcollid']); ?>">
    <input type="hidden" name="progDeptID" value="<?php echo htmlspecialchars($prog['progcolldeptid']); ?>">
    <table>
        <tr>
            <td style="width: 10em;">Program ID:</td>
            <td style="width: 30em;"><input type="text" id="progID" name="progID" value="<?php echo $prog['progid']; ?>" readonly data-original="<?php echo htmlspecialchars($prog['progid']); ?>" class="data-input"></td>
            <td><span><?php echo $_SESSION['errors']['progID'] ?? null; ?></span></td>
        </tr>
        <tr>
            <td>Program Full Name:</td>
            <td><input type="text" id="progFullName" name="progFullName" value="<?php echo $_SESSION['input']['progFullName'] ?? $prog['progfullname']; ?>" class="data-input"></td>
            <td><span><?php echo $_SESSION['errors']['progFullName'] ?? null; ?></span></td>
        </tr>
        <tr>
            <td>Program Short Name:</td>
            <td><input type="text" id="progShortName" name="progShortName" value="<?php echo $_SESSION['input']['progShortName'] ?? $prog['progshortname']; ?>" class="data-input"></td>
            <td><span><?php echo $_SESSION['errors']['progShortName'] ?? null; ?></span></td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit" name="saveProgramChanges" class="btn">Update Program Entry</button>
                <button type="button" id="resetProgramBtn" class="btn">Reset Form</button>
                <a href="index.php?section=program&page=programListByDept&progcollid=<?php echo htmlspecialchars($prog['progcollid']); ?>&deptid=<?php echo htmlspecialchars($prog['progcolldeptid']); ?>" class="btn btn-danger">Back</a>
            </td>
        </tr>
    </table>
</form>
<script>
document.getElementById('resetProgramBtn').addEventListener('click', function(){
    ['progFullName','progShortName'].forEach(function(id){
        var el = document.getElementById(id);
        if(el) el.value = '';
    });
    var form = document.currentScript ? document.currentScript.previousElementSibling : document.querySelector('form');
    if(form){
        var spans = form.querySelectorAll('span');
        spans.forEach(function(sp){ sp.innerText = ''; });
    }
    var pm = document.getElementById('pageMessages'); if(pm) pm.innerText = '';
});
</script>
