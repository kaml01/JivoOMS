<?php
$page_title = 'Sync SKU';
require_once('includes/load.php');
?>

<?php

$hostA = "103.89.44.146";
$dbNameA = "Jivo_All_Branches_Live";
$usernameA = "kamalpreet";
$passwordA = "kamal@5";

$hostB = "127.0.0.1";
$dbNameB = "jivooders";
$usernameB = "root";
$passwordB = "";

try {
    //phpinfo();
    // Create Database A Connection
    $connectionA = new PDO("sqlsrv:Server=$hostA;Database=$dbNameA", $usernameA, $passwordA);
    $connectionA->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //$queryA = "select itemname, onhand, deleted, u_veriety from OITM where itemcode like 'FG%'";
    $queryA = "select itemname, SalFactor2, U_TaxRate, deleted, u_veriety from OITM where itemcode like 'FG%'";
    $resultSetA = $connectionA->query($queryA);

    // Loop through the ResultSet and Insert Data into products Table in Database 
    while ($rowA = $resultSetA->fetch(PDO::FETCH_ASSOC)) {
        $name = $rowA['itemname'];
        $name = str_replace("'", "''", $name);
        $Pcs = $rowA['SalFactor2'];
        $tax=$rowA['U_TaxRate'];
        $deleted = $rowA['deleted'];
        $Variety = $rowA['u_veriety'];
        
        $checkname = "SELECT * FROM tbl_products WHERE name = '{$name}'";
        $existingRecord =$db->query($checkname)->fetch_assoc();

        if ($existingRecord) {
            // If the record already exists, update it
            $updateSql = "UPDATE tbl_products SET ";
            $updateSql .= "Pcs = '{$Pcs}', TAX = '{$tax}' , deleted = '{$deleted}', Variety = '{$Variety}'";
            $updateSql .= " WHERE name = '{$name}' and deleted='N'";
            $db->query($updateSql);

        } else {
            // If the record does not exist, insert a new one
            $insertSql = "INSERT INTO tbl_products (name, Pcs,TAX, deleted, Variety, date) VALUES ";
            $insertSql .= "('{$name}','{$Pcs}', '{$tax}','{$deleted}','{$Variety}',now())";
            $db->query($insertSql);
        }
    }

    echo "Data transfer completed successfully." . PHP_EOL;
} catch (PDOException $e) {
    echo "Errorr: " . $e->getMessage() . PHP_EOL;
}

// Close the Connections
$connectionA = null;
//$connectionB = null;
?>
