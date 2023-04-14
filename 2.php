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
    $surname = $_POST['surname'];
    $email = $_POST['email'];

    
    if (empty($name) || empty($surname) || empty($email)) {
        die("Please enter valid name, surname, and email.");
    }

    // checks if email already exists
    $sql_check = "SELECT * FROM contacts WHERE email='$email'";
    $result_check = $conn->query($sql_check);
    if ($result_check->num_rows > 0) {
        echo "Error: Email already exists. Please try a different one.";
    }  else {
        // creates a contact object
        $contact = new stdClass();
        $contact->name = $name;
        $contact->surname = $surname;
        $contact->email = $email;

        // inserts contacts object into table
        $sql = "INSERT INTO contacts (name, surname, email) VALUES ('$name', '$surname', '$email')";

        if ($conn->query($sql) === TRUE) {
            echo "New contact created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}


// gets all contacts from  table
$sql_contacts = "SELECT * FROM contacts";
$result_contacts = $conn->query($sql_contacts);

if ($result_contacts->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Name</th><th>Surname</th><th>Email</th></tr>";
    while ($row = $result_contacts->fetch_assoc()) {
        echo "<tr><td>" . $row["name"] . "</td><td>" . $row["surname"] . "</td><td>" . $row["email"] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<h2>No contacts found.<h2>";
}

$conn->close(); ?>

<title>Contacts</title>
<style>
 table, th, td {
 border: 1px solid black;
 border-collapse: collapse;
 padding: 5px;
 }</style>

<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>

<h2>Add a new contact</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<label for="name">Name:</label>
<input type="text" name="name" required>
<br><br>
<label for="surname">Surname:</label>
<input type="text" name="surname" required>
<br><br>
<label for="email">Email:</label>
<input type="email" name="email" required> <input type="submit" value="Submit">
<br><br>

 
<a class="btn btn-primary" href="/php/1.php" role="button">back to home page</a>
<a class="btn btn-primary" href="/php/3.php" role="button">create link</a>