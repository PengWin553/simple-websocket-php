<?php
$host = "127.0.0.1";
$port = 5500;
set_time_limit(0);

// Database connection
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'simple_websocket';

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$sock = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
socket_bind($sock, $host, $port) or die("Could not bind to socket\n");
socket_listen($sock, 3) or die("Could not set up socket listener\n");
echo "Listening for connection\n";

class Chat {
    function readLine() {
        return rtrim(fgets(STDIN));
    }
}

do {
    $accept = socket_accept($sock) or die("Could not accept incoming connection");
    $msg = socket_read($accept, 1024) or die("Could not read input\n");

    $msg = trim($msg);
    echo "Client says:\t" . $msg . "\n\n";

    // Example interaction with the database
    $query = "INSERT INTO messages (content) VALUES ('$msg')";
    if ($mysqli->query($query) === TRUE) {
        $reply = "Message saved to database.";
    } else {
        $reply = "Error: " . $mysqli->error;
    }

    socket_write($accept, $reply, strlen($reply)) or die("Could not write output\n");
    socket_close($accept);
} while (true);

$mysqli->close();
socket_close($sock);
?>
