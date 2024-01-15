<?php
  $page_title = 'My working';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(3);
?>
<?php
$sales = find_all_sale();
?>
<?php include_once('layouts/header.php'); ?>
<?php if (isset($_REQUEST['status']))
          status_update($_REQUEST['status'], $_REQUEST['id'])  
?>
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>My worklist</span>
          </strong>
        </div>
        <div class="panel-body">
          <table class="table table-bordered table-striped", style="border:gray 2px solid ">
            <thead style="background-color:#bec9e7;" >
              <tr>
                <th class="text-center" style="width: 50px;">OrderId</th>
                <th> Product name </th>
                <th class="text-center" style="width: 15%;"> Quantity</th>
                <th class="text-center" style="width: 15%;"> Total </th>
                <th class="text-center" style="width: 15%;"> Date </th>
                <th class="text-center" style="width: 100px;"> Status </th>
                <th>Action</th>
             </tr>
            </thead>
           <tbody>
             <?php foreach ($sales as $sale):?>
             <tr>
             <td class="text-center"><?php echo remove_junk($sale['id']);?></td>
               <td><?php echo remove_junk($sale['name']); ?></td>
               <td class="text-center"><?php echo (int)$sale['qty']; ?></td>
               <td class="text-center"><?php echo remove_junk($sale['price']); ?></td>
               <td class="text-center"><?php echo $sale['date']; ?></td>
               <td>
               <?php
                  if ($sale['Status'] == 0) {
                    echo '<div style="background-color: #f1fb6a; border-radius: 10px; text-align: center;">Pending</div>';
                  } elseif ($sale['Status'] == 1) {
                    echo '<div style="background-color: #4fda5f; color: white; border-radius: 10px; text-align: center;">Accept</div>';
                  } elseif ($sale['Status'] == 2) {
                    echo '<div style="background-color: #f75a45; color: white; border-radius: 10px; text-align: center;">Reject</div>';
                  }
                ?>
               </td>
               <td>  
                  <select style="border-radius:10px;background-color:#bec9e7" onchange="status_update(this.options[this.selectedIndex].value,<?php echo $sale['id']?>)">  
                    <option value="">Update Status</option>  
                    <option value="0">Pending</option>  
                    <option value="1">Accept</option>  
                    <option value="2">Reject</option>  
                  </select>  
                </td> 
             </tr>
             
             <?php endforeach;?>
           </tbody>
         </table>
        </div>
      </div>
    </div>
  </div>
  <script>
    function status_update(value,id) {
      $.ajax({
          method: "POST",
          url: 'MyWorking.php',
          data: { id: id, status: value}
      })
      location.reload();
    }
  </script>
<?php include_once('layouts/footer.php'); ?>
