<title>Clients</title>
    <style>
        body {font-family: Arial, sans-serif; background-color: #f8f8f8;}

        h2 {color: #333;}

        form {margin-top: 20px;}

        label {display: block; margin-bottom: 5px;}

        input[type="text"] { padding: 8px; width: 200px;  }

        input[type="submit"] { padding: 8px 16px;background-color: #007bff; color: #fff; border: none;  cursor: pointer;}

        input[type="submit"]:hover { background-color: #0056b3; }

        table { width: 100%; border-collapse: collapse;   margin-top: 20px;}

        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }

        th {background-color: #f2f2f2; } 
            .btn {padding: 8px 16px; background-color: #007bff;
                color: #fff; border: none; cursor: pointer;
                 text-decoration: none;display: inline-block;margin-top: 20px;}
    </style>

<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>

<?php
// connects to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clients";

$conn = new mysqli($servername, $username, $password, $dbname);

// checks connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// checks if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // gets user input
    $name = $_POST['name'];

   
    if (empty($name)) {
        die("Please enter a valid name.");
    }

    // counts number of clients in the table
    $sql_count = "SELECT COUNT(*) AS count FROM clients";
    $result_count = $conn->query($sql_count);

    if ($result_count->num_rows > 0) {
        $row_count = $result_count->fetch_assoc();
        $count = $row_count["count"] + 1; 
    } else {
        $count = 1; 
    }

    // creates client code 
    $code = strtolower(substr($name, 0, 3)) . sprintf('%03d', $count);

    // creates a client object
    $client = new stdClass();
    $client->name = $name;
    $client->code = $code;

    // inserts client object into table
    $sql = "INSERT INTO clients (name, code) VALUES ('$name', '$code')";

    if ($conn->query($sql) === TRUE) {
        echo "New client created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// displays table with client info
echo "<h2>Clients and Contacts</h2>";
$clientContactQuery = "SELECT clients.code, clients.name, COUNT(client_contacts.contact_email) AS num_contacts 
                      FROM clients 
                      LEFT JOIN client_contacts ON clients.code = client_contacts.code 
                      GROUP BY clients.code";
$clientContactResult = $conn->query($clientContactQuery);

if ($clientContactResult->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Client Code</th><th>Client Name</th><th>Number of Contacts</th></tr>";
    while ($row = $clientContactResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['code'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td style='text-align:center;'> " . $row['num_contacts'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo  "<h2>No clients found.</h2>";
}

$conn->close(); 
?>
<body>
    <h2>Add a new client</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="name">Name:</label>
        <input type="text" name="name" required>  <input type="submit" value="Submit">
     </form> <a class="btn btn-primary" href="/php/2.php" role="button">create contact</a>

