<?php

require 'config.php';
require_admin();
if(
    $_SERVER['REQUEST_METHOD']!=='POST'){
        http_response_code(405);
        exit('Method not allowed.');
    
    }csrf_check();
    $id=(int)($_POST['id']??0);
    $st=$pdo->prepare('DELETE FROM employees WHERE employee_id=?');
    $st->execute([$id]);
    flash($st->rowCount()?'Employee record deleted.':'Employee record not found.');
    header('Location: index.php');
exit;
