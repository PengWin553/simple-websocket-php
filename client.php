<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Websocket in PHP</title>
</head>
<body>
    <form action="" method="POST">
        <table>
            <tr>
                <td>
                    <label for="">Enter message:</label>
                    <input type="text" name="txtMessage">
                    <input type="submit" name="btnSend" value="Send">
                </td>
            </tr>
            <?php
                $host = "127.0.0.1";
                $port = 5500;  // Changed to match the port in the server script

                // Database connection
                $db_host = '127.0.0.1';
                $db_user = 'root';
                $db_pass = '';
                $db_name = 'simple_websocket';

                $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
                if ($mysqli->connect_error) {
                    die("Connection failed: " . $mysqli->connect_error);
                }

                if (isset($_POST['btnSend'])) {
                    $msg = $_REQUEST['txtMessage'];
                    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
                    socket_connect($sock, $host, $port);

                    socket_write($sock, $msg, strlen($msg));

                    $reply = socket_read($sock, 1924);
                    $reply = trim($reply);
                    $reply = "Server says:\t" . $reply;

                    socket_close($sock);

                    // Insert the message into the database
                    $query = "INSERT INTO messages (content) VALUES ('$msg')";
                    if ($mysqli->query($query) !== TRUE) {
                        echo "Error: " . $mysqli->error;
                    }
                }

                // Fetch all messages from the database
                $messages_query = "SELECT content FROM messages";
                $result = $mysqli->query($messages_query);

                $all_messages = "";
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $all_messages .= htmlspecialchars($row['content']) . "\n";
                    }
                }

                $mysqli->close();
            ?>
            <tr>
                <td>
                    <textarea name="" id="" cols="30" rows="10"><?php echo $all_messages; ?></textarea>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
