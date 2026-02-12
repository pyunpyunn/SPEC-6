<?php
require_once("data/db.php");
session_start();
session_regenerate_id();


$programID = $_GET['progid'] ?? null;


$dbStatement = $db->prepare("
    SELECT p.*, c.collfullname, c.collshortname, d.deptfullname, d.deptshortname
    FROM programs p
    JOIN colleges c ON p.progcollid = c.collid
    JOIN departments d ON p.progcolldeptid = d.deptid
    WHERE p.progid = :programID
");
$dbStatement->execute(['programID' => $programID]);
$program = $dbStatement->fetch();


$collegesStmt = $db->query("SELECT collid, collfullname FROM colleges");
$colleges = $collegesStmt->fetchAll();

$departmentsStmt = $db->query("SELECT deptid, deptfullname, deptcollid FROM departments");
$departments = $departmentsStmt->fetchAll();
?>

<h1>Program Update</h1>

<span style="color:green;">
    <?php echo $_SESSION['messages']['updateSuccess'] ?? null; ?>
</span>
<span style="color:red;">
    <?php echo $_SESSION['messages']['updateError'] ?? null; ?>
</span>

<form action="index.php?section=program&page=processProgramChanges" method="post">
    <table>
        <tr>
            <td style="width: 10em;">Program ID:</td>
            <td style="width: 30em;">
                <input type="text" name="progid" value="<?php echo $program['progid']; ?>" readonly class="data-input">
            </td>
        </tr>
        <tr>
            <td>Program Full Name:</td>
            <td>
                <input type="text" name="progfullname" value="<?php echo $program['progfullname']; ?>" class="data-input">
            </td>
            <td>
                <span><?php echo $_SESSION['errors']['progfullname'] ?? null; ?></span>
            </td>
        </tr>
        <tr>
            <td>Program Short Name:</td>
            <td>
                <input type="text" name="progshortname" value="<?php echo $program['progshortname']; ?>" class="data-input">
            </td>
            <td>
                <span><?php echo $_SESSION['errors']['progshortname'] ?? null; ?></span>
            </td>
        </tr>
        <tr>
            <td>College:</td>
            <td>
                <select name="progcollid" class="school-select">
                    <?php foreach($colleges as $college): ?>
                        <option value="<?php echo $college['collid']; ?>"
                            <?php if($college['collid'] == $program['progcollid']) echo 'selected'; ?>>
                            <?php echo $college['collfullname']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Department:</td>
            <td>
                <select name="progcolldeptid" class="school-select">
                    <?php foreach($departments as $dept): ?>
                        <option value="<?php echo $dept['deptid']; ?>"
                            <?php if($dept['deptid'] == $program['progcolldeptid']) echo 'selected'; ?>>
                            <?php echo $dept['deptfullname']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit" name="saveChanges" class="btn btn-primary">
                    Update Program
                </button>
                <button type="submit" name="clearChanges" class="btn btn-secondary">
                    Reset Form
                </button>
                <a href="index.php?section=program&page=programList" class="btn btn-danger">
                    Exit
                </a>
            </td>
        </tr>
    </table>
</form>
