<?php
require_once("data/db.php");
session_start();
session_regenerate_id();

$studcollid = $_GET['studcollid'] ?? null;
$deptid = $_GET['deptid'] ?? null;

$school = null;
$department = null;
$programs = [];

// Get school info
if ($studcollid){
    $schoolStmt = $db->prepare("SELECT * FROM colleges WHERE collid = :collid");
    $schoolStmt->execute(['collid' => $studcollid]);
    $school = $schoolStmt->fetch();
}

// Get department info
if ($deptid){
    $deptStmt = $db->prepare("SELECT * FROM departments WHERE deptid = :deptid");
    $deptStmt->execute(['deptid' => $deptid]);
    $department = $deptStmt->fetch();
    
    // Get programs for this department
    $progStmt = $db->prepare("SELECT progid, progfullname FROM programs WHERE progcolldeptid = :deptid");
    $progStmt->execute(['deptid' => $deptid]);
    $programs = $progStmt->fetchAll();
}
?>

<h1>Students</h1>

<?php if ($school && $department): ?>
    <h2>School: <?php echo $school['collfullname']; ?></h2>
    <h2>Department: <?php echo $department['deptfullname']; ?></h2>
    
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
            <tr>
                <td>
                    
                </td>
            </tr>
        </table>
    </form>
    
    <div>
        <a href="index.php?section=student&page=studentList" class="btn btn-secondary">Back to Student Selection</a>
    </div>
<?php endif; ?>
