<?php
require_once("data/db.php");
session_start();
session_destroy();

$limit = 10;

$progcollid = $_GET['progcollid'] ?? null;

$collegesStmt = $db->query("SELECT collid, collfullname FROM colleges");
$colleges = $collegesStmt->fetchAll();

$school = null;
$departments = [];
if ($progcollid){
    $schoolStmt = $db->prepare("SELECT * FROM colleges WHERE collid = :collid");
    $schoolStmt->execute(['collid' => $progcollid]);
    $school = $schoolStmt->fetch();
    
    $deptStmt = $db->prepare("SELECT deptid, deptfullname FROM departments WHERE deptcollid = :collid");
    $deptStmt->execute(['collid' => $progcollid]);
    $departments = $deptStmt->fetchAll();
}
?>

<h1>Programs</h1>
<form action="index.php?section=department&page=processSchoolChoice" method="post">
    <input type="hidden" name="returnTo" value="program">
    <table>
        <tr>
            <td>
                <select name="schoolID" id="select-school" class="school-select">
                    <option value="" selected hidden disabled>Select School</option>
                    <?php foreach ($colleges as $c): ?>
                        <option value="<?php echo $c['collid']; ?>" <?php if($progcollid && $progcollid == $c['collid']) echo 'selected'; ?>><?php echo $c['collfullname']; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="selectSchool" class="btn btn-info">Select School</button>
            </td>
        </tr>
    </table>
</form>

<?php if ($progcollid && $school): ?>
    <form id="department-form" action="index.php?section=program&page=processDepartmentChoice" method="post">
        <input type="hidden" name="progcollid" value="<?php echo htmlspecialchars($progcollid); ?>">
        <table>
            <tr>
                <td>
                    <select name="deptID" id="select-department" class="school-select">
                        <option value="" hidden disabled selected>Select Department</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?php echo $d['deptid']; ?>"><?php echo $d['deptfullname']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" id="selectDepartmentBtn" name="selectDepartment" class="btn btn-info">Select Department</button>
                </td>
            </tr>
        </table>
    </form>
<?php endif; ?>
<script>
(function(){
    var schoolSel = document.getElementById('select-school');
    var deptSel = document.getElementById('select-department');
    var selectDeptBtn = document.getElementById('selectDepartmentBtn');
    var deptForm = document.getElementById('department-form');

    if(schoolSel){
        schoolSel.addEventListener('change', function(){
            if(deptForm) deptForm.style.display = 'none'; 
            if(deptSel) deptSel.selectedIndex = 0;
            if(selectDeptBtn) selectDeptBtn.disabled = true;
        });
        schoolSel.addEventListener('input', function(){
            if(selectDeptBtn) selectDeptBtn.disabled = !this.value;
        });
    }

    if(deptSel){
        deptSel.addEventListener('input', function(){
            if(selectDeptBtn) selectDeptBtn.disabled = !this.value;
        });
    }

    if(selectDeptBtn) selectDeptBtn.disabled = !schoolSel || !schoolSel.value;
})();
</script>
