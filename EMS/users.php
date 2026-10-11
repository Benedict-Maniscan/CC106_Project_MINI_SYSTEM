<?php
require 'config.php';
require_admin();

if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();$id=(int)($_POST['id']??0);
$action=$_POST['action']??'';
if($id===(int)$_SESSION['user_id'])flash('You cannot change or delete your own account here.');
elseif($action==='role'&&in_array($_POST['role']??'', ['Admin','User'],true)){$pdo->prepare('UPDATE users SET role=? WHERE user_id=?')->execute([$_POST['role'],$id]);flash('User role updated.');
}elseif($action==='delete'){$pdo->prepare('DELETE FROM users WHERE user_id=?')->execute([$id]);flash('User account deleted.');
}header('Location: users.php');
exit;}$users=$pdo->query('SELECT user_id,username,full_name,email,role,created_at FROM users ORDER BY created_at DESC')->fetchAll();
include 'includes/header.php';
?>

<section class="panel">
    <div class="panel-head">
        <h1>User Accounts</h1>
    </div>
    
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Full name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
                
            
            <tbody>
                <?php foreach($users as $u):?>
                <tr>
                    <td><?=e($u['username'])?></td>
                    <td><?=e($u['full_name'])?></td>
                    <td><?=e($u['email'])?></td>
                    <td><?=e($u['role'])?></td>
                    <td><?=e($u['created_at'])?></td>
                    <td><?php if((int)$u['user_id']!==(int)$_SESSION['user_id']):?>
                        <form method="post" class="inline">
                            <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
                            <input type="hidden" name="id" value="<?=$u['user_id']?>">
                            <input type="hidden" name="action" value="role">
                            <input type="hidden" name="role" value="<?=$u['role']==='Admin'?'User':'Admin'?>">
                            
                            <button class="text-link">
                                <?=$u['role']==='Admin'?'Make User':'Make Admin'?>
                            </button>
                        </form>
                        
                        <form method="post" class="inline" onsubmit="return confirm('Delete this user account?')">
                            <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
                            <input type="hidden" name="id" value="<?=$u['user_id']?>">
                            <input type="hidden" name="action" value="delete">
                            
                            <button class="text-danger">
                                Delete
                            </button>
                        </form>
                        
                        <?php else:?>Current account<?php endif;?>
                        
                    </td>
                </tr>
                
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
    
</section>

<?php include 'includes/footer.php';?>
