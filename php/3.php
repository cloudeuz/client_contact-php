<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
<style>
        body { font-family: Arial, sans-serif; background-color: #f8f8f8;}
        
        label {display: block; margin-bottom: 1px;}
        
        input[type="text"] {padding: 1px; width: 165px;}

        input[type="submit"] {  padding: 8px 16px; background-color: #007bff;
            color: #fff;border: none; cursor: pointer;}

        input[type="submit"]:hover {  background-color: #0056b3;}
        
        table {width: 1%;border-collapse: collapse;margin-top: 1px;}

        th, td { border: 1px solid #ccc;padding: 1px; text-align: left;}

        th {background-color: #f2f2f2; } 
            .btn {padding: 8px 16px; background-color: #007bff;
                color: #fff; border: none; cursor: pointer;
                 text-decoration: none;display: inline-block;margin-top: 20px;}
    </style>

<?php
// connect to database
$conn = new mysqli("localhost", "root", "", "clients");

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// gets clients from the database
function getClients($conn) {
    $clients = array();
    $clientQuery = "SELECT code, name FROM clients";
    $clientResult = $conn->query($clientQuery);
    while ($clientRow = $clientResult->fetch_assoc()) {
        $clients[$clientRow['code']] = $clientRow['name'];
    }
    return $clients;
}

//  gets contacts from the database
function getContacts($conn) {
    $contacts = array();
    $contactQuery = "SELECT email FROM contacts";
    $contactResult = $conn->query($contactQuery);
    while ($contactRow = $contactResult->fetch_assoc()) {
        $contacts[] = $contactRow['email'];
    }
    return $contacts;
}
// displays form for linking/unlinking 
echo "<h2>Link/Unlink Contacts to Clients</h2>";
echo "<form method='post' action=''>";
echo "<label for='code'>Client Code:</label>";
echo "<select name='code' id='code'>";
$clients = getClients($conn);
foreach ($clients as $clientCode => $clientName) {
    echo "<option value='$clientCode'>$clientName ($clientCode)</option>";
}
echo "</select>";
echo "<br><br>";
echo "<label for='contact_email'>Contact Email:</label>";
echo "<select name='contact_email' id='contact_email' required>";
$contacts = getContacts($conn);
foreach ($contacts as $contactEmail) {
    echo "<option value='$contactEmail'>$contactEmail</option>";
}
echo "</select>";
echo "<br><br>";
echo "<input type='submit' name='link' value='Link Contact'>";
echo "<input type='submit' name='unlink' value='Unlink Contact'>";
echo "</form>";


// check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['link'])) {
        $clientCode = $_POST['code'];
        $contactEmail = $_POST['contact_email'];

        // check if the link already exists 
        $checkQuery = "SELECT * FROM client_contacts WHERE code = ? AND contact_email = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ss", $clientCode, $contactEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<h2>Error: Contact already linked to client with code: $clientCode<h2><br>";
        } else {
            // inserts data into table
            $insertQuery = "INSERT INTO client_contacts (code, contact_email) VALUES (?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ss", $clientCode, $contactEmail);
            if ($stmt->execute()) {
                echo "<h2>Contact linked successfully to client with code: $clientCode<br>";
            } else {
                echo "<h2>Error linking contact to client: " . $stmt->error . "<br>";
            }
        }
    } elseif (isset($_POST['unlink'])) {
        $clientCode = $_POST['code'];
        $contactEmail = $_POST['contact_email'];

        // deletes data from table
        $deleteQuery = "DELETE FROM client_contacts WHERE code = ? AND contact_email = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("ss", $clientCode, $contactEmail);
        if ($stmt->execute()) {
            echo "<h2>Contact unlinked successfully from client with code: $clientCode<br>";
        } else {
            echo "<h2>Error unlinking contact from client: " . $stmt->error . "<br>";
        }
    }
}

// closes database
$conn->close();
?>

<a class="btn btn-primary" href="/php/1.php" role="button">back to home page</a>
<a class="btn btn-primary" href="/php/2.php" role="button">back to create a contact</a>

