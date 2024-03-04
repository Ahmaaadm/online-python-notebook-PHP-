<?php
session_start();
$code_output = "Hello World!";
$code_error = "";
$exit_status = 0;
$file_to_download = "";
$previous_code = "";
$cookie_name = "previous_code";
$expiry_time = time() + (12 * 3600);



function downloadFile() {
    $filePath = "C:\\xampp\\htdocs\\BAPN\\python_files\\".$_SESSION["Fname"];

    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        readfile($filePath);
        exit;
    } else {
        echo "File not found.";
    }
}

if(isset($_POST["run"])){
    $content = $_POST["inputText"];

    $directory = __DIR__; 
    $fileName = uniqid('python_script_', true) . '.py';
    $file_path = $directory .'/python_files'. '/' . $fileName;
    $_SESSION["Fname"] = $fileName;


    file_put_contents($file_path, $content);
    $command = "python ".$file_path;

    exec($command, $exec_output, $exit_status);

    if($exit_status == 0) {
        $code_output = implode("\n", $exec_output);
    } else {
        $code_error = "Error: Command failed with exit status $exit_status";
    }
    setcookie($cookie_name,$content,$expiry_time,"/");
}

if(isset($_POST["down"])){
   downloadFile();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Title</title>
    <link rel="stylesheet" type="text/css" href="styling.css">
</head>
<body>

<nav>
    <div class="left-nav">
        <strong class="titlee">BAP Notebook</strong>
    </div>
    <form method="post">
    <div class="right-nav">
        <button name="down" class="nav-option-extra">download as file</button>
        <a href="about.php" class="nav-option">about</a>
    </div>
    </form>
</nav>

<form method="post">
    <textarea name="inputText" placeholder='Write your Python code here ...' spellcheck="false"><?php echo isset($_POST["inputText"]) ? $_POST["inputText"] : ""; ?></textarea>
    <button type="submit" class="runbtn" name="run">RUN</button>
    <button type="submit" class="runbtn" name="backup">load code</button>
    <div class="result-box" name="result-box">
        <?php
            if($exit_status == 0){
                echo $code_output;
            }
            else{
                echo $code_error;
            }
        ?>
    </div>
</form>
</body>
</html>
