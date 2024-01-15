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
$dbNameB = "jivoorders";
$usernameB = "root";
$passwordB = "";

try {
    // Create Database A Connection
    $connectionA = new PDO("sqlsrv:Server=$hostA;Database=$dbNameA", $usernameA, $passwordA);
    $connectionA->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $queryA = "select CardCode,CardName,Address,State1,U_UNE_GRP1 from OCRD where CardType='C'";
    $resultSetA = $connectionA->query($queryA);

    // Loop through the ResultSet and Insert Data into products Table in Database 
    while ($rowA = $resultSetA->fetch(PDO::FETCH_ASSOC)) {
        $CardCode = $rowA['CardCode'];
        $CardName = $rowA['CardName'];
        $CardName = str_replace("'", "''", $CardName);
        $Address = $rowA['Address'];
        $Address = str_replace("'", "''", $Address);
        $State = $rowA['State1'];
        $MainGroup = $rowA['U_UNE_GRP1'];

        $checkname = "SELECT * FROM tbl_party WHERE CardCode = '{$CardCode}'";
        $existingRecord =$db->query($checkname)->fetch_assoc();

        if ($existingRecord) {
            $updateSql = "UPDATE tbl_party SET ";
            $updateSql .= " CardName = '{$CardName}', Address = '{$Address}', State = '{$State}', MainGroup = '{$MainGroup}'";
            $updateSql .= " WHERE CardCode = '{$CardCode}'";
            $db->query($updateSql);

        } else {
            $insertSql = "INSERT INTO tbl_party (CardCode, CardName, Address, State, MainGroup) VALUES ";
            $insertSql .= "('{$CardCode}','{$CardName}','{$Address}','{$State}','{$MainGroup}'";
            $insertSql .=")";
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
