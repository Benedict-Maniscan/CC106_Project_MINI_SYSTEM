<?php
require 'config.php';
require_login();

$st=$pdo->query('SELECT e.*,d.department_name,
    p.position_title 
FROM employees e JOIN departments d ON d.department_id=e.department_id 
JOIN positions p ON p.position_id=e.position_id 
ORDER BY d.department_name,e.last_name');

$rows=$st->fetchAll();
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Employee Report</title>
        
    <style>
        body{
            font:14px Arial;
            margin:30px;
            color:#222
            }
            
            h1{
                letter-spacing:2px
            }
            
            table{
                width:100%;
                border-collapse:collapse
            }th,
            
            td{
                padding:8px;
                border-bottom:1px solid #ddd;
                text-align:left
            }
            
            th{
                background:#222;
                color:#d5b86a
            }
            
            @media print{
                button{
                    display:none
                }
            }

    </style>
    
</head>
<body>
    <button onclick="window.print()">Print / Save as PDF</button>
    <h1>MAISON — EMPLOYEE REPORT</h1>
    <p>Generated <?=date('F d, Y h:i A')?> · <?=count($rows)?> records</p>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Hire date</th>
                    <th>Status</th>
                    
                    <?php if(is_admin()):?>
                        <th>Salary</th>
                        
                    <?php endif;?>
                </tr>
            </thead>
            
            <tbody>
                <?php foreach($rows as $r):?>
                <tr>
                    <td><?=e($r['emp_no'])?></td>
                    <td><?=e($r['first_name'].' '.$r['last_name'])?></td>
                    <td><?=e($r['department_name'])?></td>
                    <td><?=e($r['position_title'])?></td>
                    <td><?=e($r['hire_date'])?></td>
                    <td><?=e($r['status'])?></td>
                    
                    <?php if(is_admin()):?>                  
                    <td>₱<?=number_format($r['salary'],2)?></td>
                        
                    <?php endif;?>
                </tr><?php endforeach;?>
            </tbody>
        </table>
        
        <p>Confidential — for authorized personnel only.</p>
</body>

</html>
