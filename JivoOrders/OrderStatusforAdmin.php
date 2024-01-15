<?php
  $page_title = 'Order Status';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(3);
?>
<?php 
$salesreport = find_all_saleforAdmin();
?>
<?php include_once('layouts/header.php'); ?>

<style>
 .statusbox{
  height:25px; 
  width:100px;
  border:1px solid black;
  margin:5px;
  color:Brown;
  font-size: small;
  text-align: center;
  }

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
              <th class="text-center" style="width: 10%;">OrderId</th>
              <th class="text-center" style="width: 15%;">Date </th>
              <th class="text-center" style="width: 15%;">Party Name </th>
              <th class="text-center" style="width: 15%;">Delivery Date </th>
              <th class="text-center" style="width: 15%;"> Status </th>
            </tr> 
          </thead>
          <tbody>
          <?php foreach ($salesreport as $sale) :
            $statusTo = getStatusTo($sale['SalesId']);?>
              <tr> 
                <td class="text-center"><?php echo remove_junk($sale['SalesId']); ?></td>
                <td class="text-center"><?php echo remove_junk($sale['Date']); ?></td>
                <td class="text-center">
                  <a href="#" onclick="showPopup('<?php echo remove_junk($sale['SalesId']); ?>')">
                    <?php echo remove_junk($sale['CardName']); ?>
                  </a>
                </td>                
                <td class="text-center"><?php echo remove_junk($sale['DeliveryDate']); ?></td>
                <td style="display: flex;justify-content:center;">
                  <?php
                       if ($statusTo == 1 || $statusTo == 2 ||$statusTo == 3) {
                          echo '
                            <div class="statusbox" style="background-color:lightyellow" id="WaitingBilling" title="Billing Done">Waiting</div>
                            ';
                        }else if ($statusTo == 4) {
                          echo '
                            <div class="statusbox" style="background-color:#FFF9A0" id="PI" title="PI Sent">PI Done</div>
                            ';
                        }else if ($statusTo == 5) {
                          echo '
                            <div class="statusbox" style="background-color:#FFFA7E" id="Payment" title="Payment Done">Payment Done</div>
                            ';
                        }else if ($statusTo == 6) {
                          echo '
                            <div class="statusbox" style="background-color:#FFF33F" id="PickList" title="PickList Done">PickList Done</div>
                            ';
                        }else if ($statusTo == 7) {
                          echo '
                            <div class="statusbox" style="background-color:lightgreen" id="Dispatch" title="Dispatched">Dispatched</div>';
                        }else if ($statusTo == 8) {
                          echo '
                            <div class="statusbox" style="background-color:#EB7F59" id="Rejected" title="Dispatched">Rejected</div>';
                        } ?>
                </td>
              </tr>
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
