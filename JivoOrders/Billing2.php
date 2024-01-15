<?php
$page_title = 'Factory All sale';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(5);

$salesreport = find_all_sale3();
?>

<?php
if (isset($_POST['PickListtoDB'])) {
  if (isset($_POST['order_id'])) {
    $my_user = $_SESSION['user_id'];
    $sales_id = (int)$_POST['order_id'];
    $query = "insert into sales_audit (salesid,personid,dttm,status_to) values('{$sales_id}','{$my_user}',now(),'6')";
    $db->query($query);
    echo "Success";
    $session->msg('s', "Pick List Done");
    redirect('Factory_Worklist.php', false);
  } else {
     echo "Error: Order ID not provided.";
    $session->msg("d", $errors);
    redirect('Factory_Worklist.php', false);
  }
} 
if (isset($_POST['DispatchtoDB'])) {
  if (isset($_POST['order_id'])) {
    $my_user = $_SESSION['user_id'];
    $sales_id = (int)$_POST['order_id'];
    $query = "insert into sales_audit (salesid,personid,dttm,status_to) values('{$sales_id}','{$my_user}',now(),'7')";
    $db->query($query);
    echo "Success";
    $session->msg('s', "Dispatched");
    redirect('Factory_Worklist.php', false);
  } else {
     echo "Error: Order ID not provided.";
    $session->msg("d", $errors);
    redirect('Factory_Worklist.php', false);
  }
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-6"> 
    <?php echo display_msg($msg); ?>
  </div>
</div>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"></head>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All Sales</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" >OrderId</th>
              <th class="text-center" >Date </th>
              <th class="text-center" >Party Name </th>
              <th class="text-center" >User Name </th>
              <th class="text-center" >Delivery Date </th>
              <th class="text-center" >Action</th>
            </tr>
          </thead>
          <tbody> 
          <?php foreach ($salesreport as $sale) :   
             $statusTo = getStatusTo($sale['SalesId']);?>
            <?php if ($statusTo ==5 ||$statusTo ==6) : ?>
              <tr>
                <td class="text-center" style="width: 8%; "><?php echo remove_junk($sale['SalesId']); ?></td>
                <td class="text-center" ><?php echo remove_junk($sale['Date']); ?></td>
                <td class="text-center" >
                  <a href="#" onclick="showPopup('<?php echo remove_junk($sale['SalesId']); ?>')">
                    <?php echo remove_junk($sale['CardName']); ?>
                  </a>
                </td>
                <td class="text-center"><?php echo remove_junk($sale['name']); ?></td>
                <td class="text-center"><?php echo remove_junk($sale['DeliveryDate']); ?></td>
                
                <td class="text-center">
                <?php
                    if ($statusTo == 5) {
                        echo '
                        <form method="post" action="">
                            <input type="hidden" name="order_id" value="' . $sale['SalesId'] . '">                                
                            <div style="display:flex;justify-content:center"">
                            <button style="margin:0px 5px 5px 0px" type="submit" name="PickListtoDB" title="Pick List" class="btn btn-success"><span class="glyphicon glyphicon-list-alt"></span></button>
                            <button style="margin:0px 5px 5px 0px" type="submit" name="Dispatch" class="btn btn-disable" style="color:black" disabled><span <i class="fa fa-truck"></i></span></button>
                            </div> 
                        </form>';
                    }elseif ($statusTo == 6) {
                      echo '
                      <form method="post" action="" enctype="multipart/form-data">
                      <input type="hidden" name="order_id" value="' . $sale['SalesId'] . '">                     
                      <div style="display:flex;justify-content:center">
                          <button style="margin:0px 5px 5px 0px" type="submit" name="Pick-List" title="Pick List" class="btn btn-disable" disabled><span class="glyphicon glyphicon-list-alt"></span></button>
                          <button style="margin:0px 5px 5px 0px" type="submit" title="Dispatch" name="DispatchtoDB" style="color:black" class="btn btn-danger"><i class="fa fa-truck"></i></span></button>
                      </div>
                  </form>';
                  }
                    ?>
                </td>   
              </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody> 
        </table>
      </div>
    </div>
  </div>
</div>
<div id="popup" class="popup">
  <div class="popup-content">
    <span class="close" onclick="closePopup()">&times;</span>
    <p id="popupSalesId"></p>
    <div id="productDetails"></div>
  </div>
</div>
<style>
  /* Add this to your CSS */

  .popup {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.7);
}

.popup-content {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #fefefe;
  padding: 20px;
  border-radius: 5px;
  text-align: center;
}

.close {
  color: black;
  float: right;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
}

.close:hover {
  color: black;
}
</style>
<script>
  function displayFileName() {
        var fileInput = document.getElementById('file');
        var fileNameDisplay = document.getElementById('fileNameDisplay');

        if (fileInput.files.length > 0) {
            fileNameDisplay.textContent = fileInput.files[0].name;
        } else {
            fileNameDisplay.textContent = '';
        }
    }
</script>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
  function showPopup(salesId) {
    var popup = document.getElementById('popup');
    var popupSalesId = document.getElementById('popupSalesId');
    var productDetailsContainer = $('#productDetails');

    // Set the SalesId in the popup
    popupSalesId.innerHTML = "SalesId: " + salesId;

    // Make an AJAX request to fetch product details
    $.ajax({
      type: 'POST',
      url: 'get_product_details.php',
      data: { salesId: salesId },
      success: function(response) {
        // Clear the previous content and display the product details in the popup
        productDetailsContainer.html(response);

        // Check the status of the "Accept" button
        var acceptButton = $('#acceptButton');
        var action2Buttons = $('.action2-buttons');

        // Check if the "Accept" button is present
        if (acceptButton.length) {
          var isAccepted = !acceptButton.hasClass('btn-disable');
          action2Buttons.prop('disabled', !isAccepted);
        }

        popup.style.display = 'block';
      },
      error: function(xhr, status, error) {
        console.error("AJAX Error:", status, error);
      }
    });
  }
 
  function closePopup() {
    var popup = document.getElementById('popup');
    var productDetailsContainer = $('#productDetails');

    // Clear the product details when closing the popup
    productDetailsContainer.empty();
    popup.style.display = 'none';
  }
</script>


<?php include_once('layouts/footer.php'); ?>
