<?php

// Database credentials
$host = 'localhost';
$dbname = 'rsi'; // Source database
$username = 'root';
$password = '';

// Connect to the source database
try {
    $sourceDb = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $sourceDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	echo "Source database connection successful\n";
	echo "<br/>";
	echo "<br/>";
} catch (PDOException $e) {
    die("Source database connection failed: " . $e->getMessage());
}
// Connect to the destination database
$destDbName = 'royalstream'; // Destination database
try {
	$destDb = new PDO("mysql:host=$host;dbname=$destDbName", $username, $password);
    $destDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	echo "Destination database connection successful\n";
	echo "<br/>";
	} catch (PDOException $e) {
		die("Destination database connection failed: " . $e->getMessage());
		}
		

// Define the source and destination tables and columns
$sourceTable = 'gibbonfinancebudget';
$destinationTable = 'expenditure_budgets';

// Specific columns to select from the source table
$columnsToSelect = [ 'name', 'nameShort', 'category' ]; // Adjust this to the actual columns you need

// Select data from the source table
try {
    $selectQuery = "SELECT " . implode(', ', $columnsToSelect) . " FROM $sourceTable";
    $stmt = $sourceDb->query($selectQuery);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($rows)) {
        // Prepare insert query for the destination table
        $columnsToInsert = $columnsToSelect; // Assuming columns are the same in both tables
        $insertQuery = "INSERT INTO $destinationTable (" . implode(', ', $columnsToInsert) . ") VALUES (" . implode(', ', array_fill(0, count($columnsToInsert), '?')) . ")";
        $insertStmt = $destDb->prepare($insertQuery);

        // Insert data into the destination table
        foreach ($rows as $row) {
            $insertStmt->execute(array_values($row));
        }

        echo "Data transferred successfully!";
    } else {
        echo "No data found in the source table.";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Close the database connections
$sourceDb = null;
$destDb = null;
?>
