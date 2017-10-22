<?php

//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ALL);

//Class to load classes it finds the file when the progrm starts to fail for calling a missing class
class Manage {
    public static function autoload($class) {
        include $class . '.php';
    }
}

spl_autoload_register(array('Manage', 'autoload'));

//instantiate the program object
$obj = new main();

class main
{
    public function __construct()
    {
       $pageRequest = 'upload';
        if (isset($_REQUEST['page'])) {
            $pageRequest = $_REQUEST['page'];
        }
        $page = new $pageRequest;
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $page->get();
        } else {
            $page->post();
        }
    }

}

abstract class page
{
    protected $html;

    public function __construct()
    {
        $this->html .= '<h1>Project 1</h1>';
        $this->html .= '<body bgcolor="#E6E6FA">';
    }
    public function __destruct()
    {
        $this->html .= '</body></html>';
        print_r($this->html);
    }

    public function get()
    {
        echo 'default get message';
    }

    public function post()
    {
        print_r($_POST);
    }
}

class getform
{
    public $html;
    public static function upform()
    {
        $form = '<form action="index.php?page=upload" method="post"  enctype="multipart/form-data">';
        $form .= 'Please select the file to upload:<br><br>';
        $form .= '<input type="file" name="fileToUpload" id="filetoupload"">';
        $form .= '<input type="submit" value="Upload" name="submit">';
        $form .= '</form> ';
        return $form;
    }
}

class nextpage
{
    public static function sendto($target_file)
    {
        header('Location: index.php?page=htmltable&doc=' . $target_file);
    }
}

class upload extends page
{
    public $html;

    public function get()
    {
        $this->html .= '<body><b>Upload and Read File</b></body><br><br>';
        $this->html .= getform::upform();
    }

    public function post()
    {
        $target_dir = "/afs/cad/u/c/i/ci38/public_html/Project1/uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $filetype = 'pathinfo($target_file,PATHINFO_EXTENSION';

        if (isset($_POST["submit"]))
        {

            if ($filetype != 'csv') {
                echo 'The file is not CSV file';
            }

                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    nextpage::sendto($target_file);
                }
        }        else  {
                echo "Error in uploading file";
            }
    }
}

class htmlTable extends page
{
    public $html;
    public function get()
    {
        $fileName = $_GET['doc'];
        $handle = fopen($fileName, "r");
        echo '<table>';
        if (true) {
            $csvcontents = fgetcsv($handle);
            echo '<tr>';
            foreach ($csvcontents as $headercolumn) {
                echo "<th>$headercolumn</th>";
            }
            echo '</tr>';
        }
        while ($csvcontents = fgetcsv($handle)) {
            echo '<tr>';
            foreach ($csvcontents as $column) {
                echo "<td>$column</td>";
            }
            echo '</tr>';
        }
        echo '</table>';
        fclose($handle);
    }

    public function post()
    {
        print_r($_FILES);
    }
}

?>