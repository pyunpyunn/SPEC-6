<?php
require_once("data/db.php");
session_start();
session_destroy();

 $studcollid = $_GET['studcollid'] ?? null;
 $deptid = $_GET['deptid'] ?? null;

$collegesStmt = $db->query("SELECT collid, collfullname FROM colleges");
$colleges = $collegesStmt->fetchAll();

$school = null;
$departments = [];
if ($studcollid){
    $schoolStmt = $db->prepare("SELECT * FROM colleges WHERE collid = :collid");
    $schoolStmt->execute(['collid' => $studcollid]);
    $school = $schoolStmt->fetch();
    
    $deptStmt = $db->prepare("SELECT deptid, deptfullname FROM departments WHERE deptcollid = :collid");
    $deptStmt->execute(['collid' => $studcollid]);
    $departments = $deptStmt->fetchAll();
}

$programs = [];
if ($deptid){
    $progStmt = $db->prepare("SELECT progid, progfullname FROM programs WHERE progcolldeptid = :deptid");
    $progStmt->execute(['deptid' => $deptid]);
    $programs = $progStmt->fetchAll();
}
?>

<h1>Students</h1>
<form action="index.php?section=student&page=processStudentSchoolChoice" method="post">
    <input type="hidden" name="returnTo" value="student">
    <table>
        <tr>
            <td>
                <select name="schoolID" id="select-school" class="school-select">
                    <option value="" selected hidden disabled>Select School</option>
                    <?php foreach ($colleges as $c): ?>
                        <option value="<?php echo $c['collid']; ?>" <?php if($studcollid && $studcollid == $c['collid']) echo 'selected'; ?>><?php echo $c['collfullname']; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="selectSchool" class="btn btn-info">Select School</button>
            </td>
        </tr>
        <tr>
            <td>
                
            </td>
        </tr>
    </table>
</form>

<!-- Select Department: only visible after school selected -->
 <?php if ($studcollid && $school): ?>
    <form action="index.php?section=student&page=processStudentDepartmentChoice" method="post">
        <input type="hidden" name="studcollid" value="<?php echo htmlspecialchars($studcollid); ?>">
        <table>
            <tr>
                <td>
                    <select name="deptID" id="select-department" class="school-select">
                        <option value="" hidden disabled selected>Select Department</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?php echo $d['deptid']; ?>" <?php if(isset($deptid) && $deptid == $d['deptid']) echo 'selected'; ?>><?php echo $d['deptfullname']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="selectDepartment" class="btn btn-info">Select Department</button>
                </td>
            </tr>
            <tr>
                <td>
                    
                </td>
            </tr>
        </table>
    </form>
    
    <!-- Select Program: visible after department selected on same page -->
    <?php if ($deptid): ?>
        <form action="index.php?section=student&page=processStudentProgramChoice" method="post">
            <input type="hidden" name="studcollid" value="<?php echo htmlspecialchars($studcollid); ?>">
            <input type="hidden" name="deptid" value="<?php echo htmlspecialchars($deptid); ?>">
            <table>
                <tr>
                    <td>
                        <select name="progID" id="select-program" class="school-select">
                            <option value="" hidden disabled selected>Select Program</option>
                            <?php foreach ($programs as $p): ?>
                                <option value="<?php echo $p['progid']; ?>"><?php echo $p['progfullname']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="selectProgram" class="btn btn-info">Select Program</button>
                    </td>
                </tr>
            </table>
        </form>
    <?php endif; ?>
<?php endif; ?>
