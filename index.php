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
$sourceTable = 'gibbonfinancefee';
$destinationTable = 'fees';

// Specific columns to select from the source table
$columnsToSelect = [ 'name', 'nameShort', 'description', 'fee', 'gibbonFinanceFeeCategoryID']; // Adjust this to the actual columns you need

// Select data from the source table
try {
    $selectQuery = "SELECT " . implode(', ', $columnsToSelect) . " FROM $sourceTable";
    $stmt = $sourceDb->query($selectQuery);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($rows)) {
        // Prepare insert query for the destination table
        $insertQuery = "INSERT INTO $destinationTable ( name, short_name, description, amount, category_id) VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $destDb->prepare($insertQuery);

        // Process each row and transform the data
        foreach ($rows as $row) {
            $name = $row['name'];
            $nameShort = $row['nameShort'];
            $description = $row['description'];
            $amount = $row['fee'];
            $category_id = '';

            // Switch case to map class_id to class name
            switch ($row['gibbonFinanceFeeCategoryID']) {
				case '0001' :
					$category_id = 1; // Default case if class_id doesn't match
                case '0002':
                    $category_id = 2;
                    break;
                case '0003':
                    $category_id = 3;
                    break;
                case '0005':
                    $category_id = 4;
				case '0006':
					$category_id = 5;
				case '0007':
					$category_id = 6;
				case '0008':
					$category_id = 7;
				case '0009':
					$category_id = 8;
				case '0010':
					$category_id = 9;
                    break;
                default:
                    $category_id = 'Unknown'; // Default case if class_id doesn't match
            }

            // Insert transformed data into the destination table
            $insertStmt->execute([$name, $nameShort, $description, $amount, $category_id]);
        }

        echo "Data transferred successfully with class mapping!";
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
