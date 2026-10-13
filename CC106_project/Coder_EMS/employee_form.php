<?php
require 'config.php';
require_admin();
$page_title = 'Employee form';
$id = (int) ($_GET['id'] ?? 0);
$d = ['emp_no' => '', 'first_name' => '', 'last_name' => '', 'email' => '', 'phone' => '', 'hire_date' => date('Y-m-d'), 'salary' => '', 'status' => 'Active', 'department_id' => '', 'position_id' => ''];
$errors = [];
if ($id) {
    $s = $pdo->prepare('SELECT * FROM employees WHERE employee_id=?');
    $s->execute([$id]);
    $found = $s->fetch();
    if (!$found) {
        http_response_code(404);
        exit('Employee not found.');
    }
    $d = $found;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($d as $k => $v)
        if (isset($_POST[$k]))
            $d[$k] = trim((string) $_POST[$k]);
    if (!preg_match('/^EMP-\d{4,}$/', $d['emp_no']))
        $errors[] = 'Employee ID must look like EMP-0001.';
    if (!preg_match("/^[A-Za-z .'-]{2,60}$/", $d['first_name']))
        $errors[] = 'Enter a valid first name.';
    if (!preg_match("/^[A-Za-z .'-]{2,60}$/", $d['last_name']))
        $errors[] = 'Enter a valid last name.';
    if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL))
        $errors[] = 'Enter a valid email address.';
    if (!preg_match('/^09\d{9}$/', $d['phone']))
        $errors[] = 'Phone must be 11 digits starting with 09.';
    $dt = DateTime::createFromFormat('!Y-m-d', $d['hire_date']);
    if (!$dt || $dt->format('Y-m-d') !== $d['hire_date'] || $dt > new DateTime('today'))
        $errors[] = 'Hire date is invalid or in the future.';
    if (!is_numeric($d['salary']) || (float) $d['salary'] < 0)
        $errors[] = 'Salary must be zero or higher.';
    if (!in_array($d['status'], ['Active', 'On Leave', 'Resigned'], true))
        $errors[] = 'Invalid status.';
    if (!(int) $d['department_id'] || !(int) $d['position_id'])
        $errors[] = 'Choose a department and position.';
    if (!$errors) {
        $vals = [$d['emp_no'], $d['first_name'], $d['last_name'], $d['email'], $d['phone'], $d['hire_date'], $d['salary'], $d['status'], (int) $d['department_id'], (int) $d['position_id']];
        try {
            if ($id) {
                $vals[] = $id;
                $pdo->prepare('UPDATE employees SET emp_no=?,first_name=?,last_name=?,email=?,phone=?,hire_date=?,salary=?,status=?,department_id=?,position_id=? WHERE employee_id=?')->execute($vals);
                flash('Employee record updated.');
            } else {
                $pdo->prepare('INSERT INTO employees(emp_no,first_name,last_name,email,phone,hire_date,salary,status,department_id,position_id) VALUES(?,?,?,?,?,?,?,?,?,?)')->execute($vals);
                flash('Employee record created.');
            }
            header('Location: employees.php');
            exit;
        } catch (PDOException $ex) {
            $errors[] = $ex->getCode() === '23000' ? 'Employee ID or email already exists.' : 'Unable to save this employee record.';
        }
    }
}
$depts = $pdo->query('SELECT * FROM departments ORDER BY department_name')->fetchAll();
$positions = $pdo->query('SELECT * FROM positions ORDER BY position_title')->fetchAll();
include 'includes/header.php'; ?>
<section class="page-head">
    <div>
        <p class="eyebrow">EMPLOYEE RECORDS</p>
        <h1><?= $id ? 'Edit employee' : 'Add employee' ?></h1>
        <p class="muted">Fields marked required must be completed.</p>
    </div><a class="btn outline" href="employees.php">← Back to employees</a>
</section>
<section class="panel narrow"><?php if ($errors): ?>
        <div class="alert error">
            <ul><?php foreach ($errors as $er): ?>
                    <li><?= e($er) ?></li><?php endforeach; ?>
            </ul>
        </div><?php endif; ?>
    <form method="post" class="form-grid"><input type="hidden" name="csrf"
            value="<?= e(csrf_token()) ?>"><?php foreach ([['emp_no', 'Employee ID', 'text'], ['first_name', 'First name', 'text'], ['last_name', 'Last name', 'text'], ['email', 'Email', 'email'], ['phone', 'Phone number', 'tel'], ['hire_date', 'Hire date', 'date'], ['salary', 'Monthly salary (₱)', 'number']] as [$key, $label, $type]): ?>
            <div><label><?= $label ?><input name="<?= $key ?>" type="<?= $type ?>" value="<?= e($d[$key]) ?>"
                        <?= $key === 'salary' ? 'min="0" step="0.01"' : '' ?> required></label></div><?php endforeach; ?>
        <div><label>Status<select name="status"><?php foreach (['Active', 'On Leave', 'Resigned'] as $s): ?>
                        <option <?= $d['status'] === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?>
                </select></label></div>
        <div><label>Department<select name="department_id" required>
                    <option value="">Select department</option><?php foreach ($depts as $x): ?>
                        <option value="<?= $x['department_id'] ?>"
                            <?= $d['department_id'] == $x['department_id'] ? 'selected' : '' ?>><?= e($x['department_name']) ?>
                        </option><?php endforeach; ?>
                </select></label></div>
        <div><label>Position<select name="position_id" required>
                    <option value="">Select position</option><?php foreach ($positions as $x): ?>
                        <option value="<?= $x['position_id'] ?>" <?= $d['position_id'] == $x['position_id'] ? 'selected' : '' ?>>
                            <?= e($x['position_title']) ?></option><?php endforeach; ?>
                </select></label></div>
        <div class="full actions"><button class="btn primary"><?= $id ? 'Save changes' : 'Create employee' ?></button><a
                class="btn outline" href="employees.php">Cancel</a></div>
    </form>
</section><?php include 'includes/footer.php'; ?>