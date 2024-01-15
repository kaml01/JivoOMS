<?php
  $page_title = 'Edit User';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
?>
<?php
  $e_user = find_by_id('users',(int)$_GET['id']);
  $groups  = find_all('user_groups');
  if(!$e_user){
    $session->msg("d","Missing user id.");
    redirect('users.php');
  }
  
  $State=all_State();
  $MainGroup=MainGroup();
?>

<?php
//Update User basic info
  if(isset($_POST['update'])) {
    $req_fields = array('name','username','level','MainGroup','State','email');
    validate_fields($req_fields);
    if(empty($errors)){
            $id = (int)$e_user['id'];
            $name = remove_junk($db->escape($_POST['name']));
            $username = remove_junk($db->escape($_POST['username']));
            $level = (int)$db->escape($_POST['level']);
            $status   = remove_junk($db->escape($_POST['status']));
            $MainGroup = isset($_POST['MainGroup']) ? implode(',', $_POST['MainGroup']) : '';
            $State = isset($_POST['State']) ? implode(',', $_POST['State']) : '';
            $email   = remove_junk($db->escape($_POST['email']));
            $sql = "UPDATE users SET name ='{$name}', username ='{$username}',user_level='{$level}',status='{$status}',MainGroup='{$MainGroup}',State='{$State}',email='{$email}' WHERE id='{$db->escape($id)}'";
         $result = $db->query($sql);
          if($result && $db->affected_rows() === 1){
            $session->msg('s',"Acount Updated ");
            redirect('edit_user.php?id='.(int)$e_user['id'], false);
          } else {
            $session->msg('d',' Sorry failed to updated!');
            redirect('edit_user.php?id='.(int)$e_user['id'], false);
          }
    } else {
      $session->msg("d", $errors);
      redirect('edit_user.php?id='.(int)$e_user['id'],false);
    }
  }
?>
<?php
// Update user password
if(isset($_POST['update-pass'])) {
  $req_fields = array('password');
  validate_fields($req_fields);
  if(empty($errors)){
           $id = (int)$e_user['id'];
           $new_password = remove_junk($db->escape($_POST['password']));
           $h_pass= sha1($new_password);
           $sql = "UPDATE users SET password='{$h_pass}' WHERE id='{$db->escape($id)}'";
           
       $result = $db->query($sql);
        if($result && $db->affected_rows() === 1){
          $session->msg('s',"User password has been updated ");
          redirect('edit_user.php?id='.(int)$e_user['id'], false);
        } else {
          $session->msg('d',' Sorry failed to updated user password!');
          redirect('edit_user.php?id='.(int)$e_user['id'], false);
        }
  } else {
    $session->msg("d", $errors);
    redirect('edit_user.php?id='.(int)$e_user['id'],false);
  }
}

?>
<?php include_once('layouts/header.php'); ?>
 <div class="row">
   <div class="col-md-12"> <?php echo display_msg($msg); ?> </div>
  <div class="col-md-6">
     <div class="panel panel-default">
       <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          Update <?php echo remove_junk(ucwords($e_user['name'])); ?> Account
        </strong>
       </div>
       <div class="panel-body">
          <form method="post" action="edit_user.php?id=<?php echo (int)$e_user['id'];?>" class="clearfix">
            <div class="form-group">
                  <label for="name" class="control-label">Name</label>
                  <input required type="name" class="form-control" name="name" value="<?php echo remove_junk(ucwords($e_user['name'])); ?>">
            </div>
            <div class="form-group">
                  <label for="username" class="control-label">Username</label>
                  <input required  type="text" class="form-control" name="username" value="<?php echo remove_junk(ucwords($e_user['username'])); ?>">
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input required type="email" class="form-control" name="email" value="<?php echo remove_junk(ucwords($e_user['email'])); ?>">
            </div>
            <div class="form-group">
              <label for="MainGroup">Main Group</label>
              <select required class="form-control" name="MainGroup[]" multiple>
                  <?php foreach ($MainGroup as $Group): ?>
                      <option <?php if ($Group['MainGroup'] === $e_user['MainGroup']) echo 'selected="selected"'; ?>>
                          <?php echo $Group['MainGroup']; ?>
                      </option>
                  <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
    <label for="State">State</label>
    <select required class="form-control" name="State[]" multiple>
        <?php
        // Define an associative array for state codes and their full names
        $stateMapping = array(
            'UP' => 'Uttar Pradesh',
            'DL' => 'Delhi',
            'PB' => 'Punjab',
            'MP' => 'Madhya Pradesh',
            'MH' => 'Maharashtra',
            'GJ' => 'Gujarat',
            'HR' => 'Haryana',
            'UK' => 'Uttarakhand',
            'WB' => 'West Bengal',
            'HP' => 'Himachal Pradesh',
            'JK' => 'Jammu and Kashmir',
            'KT' => 'Karnataka',
            'SK' => 'Sikkim',
            'AS' => 'Assam',
            'RJ' => 'Rajasthan',
            'TE' => 'Telangana',
            'AP' => 'Andhra Pradesh',
            'OR' => 'Odisha',
            'KE' => 'Kerala',
            'A&N' => 'Andaman and Nicobar Islands',
            'CH' => 'Chandigarh',
            'GO' => 'Goa',
            'AD' => 'Arunachal Pradesh',
            'BH' => 'Bihar',
            'ME' => 'Meghalaya',
            'TN' => 'Tamil Nadu',
            'PY' => 'Puducherry',
            'MZ' => 'Mizoram',
            'AR' => 'Arunachal Pradesh',
            'MN' => 'Manipur'
            // Add more states as needed
        );

        foreach ($State as $state):
            // Use the state code to get the full name from the array
            $fullName = isset($stateMapping[$state['State']]) ? $stateMapping[$state['State']] : $state['State'];
        ?>
            <option value="<?php echo $state['State']; ?>" <?php if ($state['State'] === $e_user['State']) echo 'selected="selected"'; ?>>
                <?php echo ucwords($fullName); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>


            <div class="form-group">
              <label for="level">User Role</label>
                <select required class="form-control" name="level">
                  <?php foreach ($groups as $group ):?>
                   <option <?php if($group['group_level'] === $e_user['user_level']) echo 'selected="selected"';?> value="<?php echo $group['group_level'];?>"><?php echo ucwords($group['group_name']);?></option>
                <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
              <label for="status">Status</label>
                <select required class="form-control" name="status">
                  <option <?php if($e_user['Status'] === '1') echo 'selected="selected"';?>value="1">Active</option>
                  <option <?php if($e_user['Status'] === '0') echo 'selected="selected"';?> value="0">Deactive</option>
                </select>
            </div>
            <div class="form-group clearfix">
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
            </div>
        </form>
       </div>
     </div>
  </div>
  <!-- Change password form -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          Change <?php echo remove_junk(ucwords($e_user['name'])); ?> password
        </strong>
      </div>
      <div class="panel-body">
        <form action="edit_user.php?id=<?php echo (int)$e_user['id'];?>" method="post" class="clearfix">
          <div class="form-group">
              <label for="password" class="control-label">Change Password</label>
              <input type="password" class="form-control" name="password">
          </div>
          <div class="form-group clearfix">
                  <button type="submit" name="update-pass" class="btn btn-primary pull-right">Change</button>
          </div>
        </form>
      </div>
    </div>
  </div>

 </div>
<?php include_once('layouts/footer.php'); ?>
