<?php
  $page_title = 'Admin Home Page';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   //page_require_level(1);
?> 
<?php
 $c_product       = count_by_id('tbl_products');
 $c_sale          = count_by_Salesid('salesreport');
 $saledetail      = TotalSaleDetails();
 $salevalue       = TotalValueOfOrder();
 
?> 
<?php include_once('layouts/header.php'); ?>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"></head>
<div class="row" style="margin-left:200px" >
    <div>
    <a href="product.php" >
        <div class="col-md-4" style="height:129px">
           <div class="panel panel-box clearfix" style="border:2px solid #182667;">
             <div class="panel-icon pull-left bg-blue2">
              <i class="glyphicon glyphicon-shopping-cart"></i>
            </div>
            <div class="panel-value pull-right">
              <h2 class="margin-top"> <?php  echo $c_product['total']; ?> </h2>
              <p class="text-muted">Products</p>
            </div>
           </div>
        </div>
    	</a>
    </div>
    <div>
    <a href="sales.php" style="color:black;">
        <div class="col-md-4" style="height:129px">
           <div class="panel panel-box clearfix" style="border:2px solid #182667;">
             <div class="panel-icon pull-left bg-green" style="color:white;font-size:45px">#
             <!-- <i class="fa fa-inr" aria-hidden="true"></i> -->
            </div>
            <div class="panel-value pull-right">
              <h2 class="margin-top"> <?php  echo $c_sale['total']; ?></h2>
              <p class="text-muted">Total Sales</p>
            </div>
           </div>
        </div>
    	</a> 
    </div>
</div>

<div class="row" style="margin-top:50px;margin-left:150px">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th">Order Details</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>No. of orders</th>
                            <th>Value (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($saledetail as $sale): ?>
                        <tr>
                          <td><?php echo $sale['status_to']; ?></td>
                          <td><?php echo $sale['count']; ?></td>
                          <td>
                              <?php
                                  $valueKey = $sale['status_to'];
                                  $matchingValue = null;

                                  // Find the matching value in $salevalue array
                                  foreach ($salevalue as $valueItem) {
                                      if ($valueItem['status_to'] === $valueKey) {
                                          $matchingValue = $valueItem['value'];
                                          break;
                                      }
                                  }
                                
                                  echo $matchingValue !== null ? $matchingValue : 'N/A';
                              ?>
                          </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
            </div>
        </div>
    </div>
    <div class="row" >
      <div class="col-md-4">
        <canvas id="pieChart"></canvas>
      </div>
    </div>
</div>
<?php
$Values = TotalValueOfOrder();
$pendingValue = 0;
$Total = 0;

foreach ($Values as $sale) {
    $status = $sale['status_to'];
    $value = $sale['value'];

    if ($status === 'Pending') {
        $pendingValue = $value;
    }if ($status === 'Dispatch' || $status === 'Rejected' || $status === 'Pending') {
        $Total += $value;
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    // Use PHP variables to populate JavaScript variables for pie chart values
    var pendingValue = <?php echo $pendingValue; ?>;
    var Total = <?php echo $Total; ?>;

    // Create the pie chart
    var ctx = document.getElementById('pieChart').getContext('2d');
    var pieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Total','Pending'],
            datasets: [{
                data: [Total,pendingValue],
                backgroundColor: ['#36A2EB', '#FF6384']
            }]
        },
        options: {
            plugins: {
                datalabels: {
                    color: 'white',
                    font: {
                        weight: 'bold'
                    },
                    anchor: 'start',
                    align: 'center',
                    offset: 10,
                    formatter: function (value, context) {
                        return context.chart.data.labels[context.dataIndex] + '\n' + value;
                    }
                }
            }
        }
    });
</script>

<?php include_once('layouts/footer.php'); ?>
