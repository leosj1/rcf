<?php

/*THIS CODE EXTRACTS INFORMATION FROM LOG FILES (.SCRATCH) TO A .CSV FILE
SPREADSHEETS*/ 

//LOG FILE LINK -> "https://github.com/leosj1/rcf/blob/master/D04558-D04598.SCRATCH"

//EXTRACTED OUTPUT LINK -> "https://github.com/leosj1/rcf/blob/master/tapes.csv"

/*TO RUN THIS FILE, PLACE FILE IN XAMPP HTDOCS FOLDER, REPLICATE PATH "C:\\xampp\\htdocs\\seun\\NEW\\" 
and run http://localhost/seun/read_scratch_3480_upload_git.php on browser*/

//increase memory limit to handle larger files
ini_set('memory_limit', '2646679286');
ini_set('max_execution_time', 400);


$path = "NEW";
// $path2 = "C:\\wamp64\\www\\seun\\NEW\\"; //path to the file on localhost server
$path2 = "C:\\xampp\\htdocs\\seun\\NEW\\"; //path to the file on localhost server


// function to extract string after particular substring 
function strafter($str, $substr)
{
    $pos = strpos($str, $substr);
    if ($pos === false) {
        return $str;
    } else {
        return (substr($str, $pos + strlen($substr)));
    }
}

// function to extract string before particular substring 
function strbefore($str, $substr)
{
    $pos = strpos($str, $substr);
    if ($pos === false) {
        return $str;
    } else {
        return (substr($str, 0, $pos));
    }
}

//custom function to check if an array exists in an array then removes that array of values
function checkexist(array $arr, array $z)
{
    $count = 0;
    $va = [];
    foreach ($arr as $key => $value) {
        foreach ($z as $key2 => $valu) {
            if (strpos($value, $valu)) {
                $va[] = $value;
                break;
            }
        }
    }
    $final = array_diff($arr, $va);
    return $final;
}
//array that contains list of unwanted data
$z = array(
    "ensemble 9999,",
    "ensemble 932,",
    "ensemble 933,",
    "ensemble 934,",
    "ensemble 935,",
    "ensemble 936,",
    "ensemble 937,",
    "ensemble 938,",
    "ensemble 939",
    "ensemble 940,",
    "ensemble 941,",
    "ensemble 942,",
    "ensemble 943,",
    "ensemble 944,",
// "ensemble 1,",
// "ensemble 2,",
// "ensemble 3,",
// "ensemble 4,",
// "ensemble 5,",
// "ensemble 6,",
// "ensemble 7,",
// "ensemble 8,",
// "ensemble 9,",
// "ensemble 10,",
// "ensemble 11,",
// "ensemble 12,",
// "ensemble 13,",
// "ensemble 14,",
    "DIPLOMAT COP",
// "ensemble 15,",
    "Zero-length block",
    "End-of-file",
    "Length =",
    "WARNING",
    "Beginning of tape (BOT) detected",
    "Repositioned output(s)"
);

$count = 0;

$arr = [];
$arr2 = [];
$arr3 = [];

//loops through the folder and works on files that have .scratch as an encapsulation
foreach (scandir($path2) as $fil) {
    if (strtolower(strafter($fil, ".")) == "scratch") {
        if ('.' === $fil) continue;
        if ('..' === $fil) continue;
        // echo $fil;
        $lines = file($path2 . $fil); //put entire file into an array
    

// var_dump($fil);
// die();

//extracts line in file that has 'BATCHCOPY - Copying tape', 'ensemble' and 'Transfer total'
        foreach ($lines as $key => $value) { //loop through file array to extract data
            if (stristr($value, 'BATCHCOPY - Copying tape') == true) { //checks if line has batchcopy header
                $arr[] = $lines[$key];
            }

            if (stristr($value, 'ensemble') == true) { //checks lines after end of file
                $arr[] = $lines[$key];
            }

            if (stristr($value, 'Transfer total') == true) { //checks if line has'transfer'
                $arr[] = $lines[$key]; // returns the transfer line for data size
            }

        }
    } 
    // die();

}
// die();
//  echo '<pre>',print_r($arr),'</pre>'; 
//  die();

//remove unwannted array from array
$arr = checkexist($arr, $z);
//  echo '<pre>',print_r($arr),'</pre>'; 
//  die();
$arrsyy = [];
//restructure the array keys
foreach ($arr as $key => $v) {
    $arrsyy[] = $v;
}

//rearrange the array
foreach ($arrsyy as $key2 => $value2) {
    if (stristr($value2, 'BATCHCOPY - Copying tape') == true) { //checks if line has batchcopy header
        $arr2[] = $arrsyy[$key2];
        $arr2[] = $arrsyy[$key2 + 1];
    }

    if (stristr($value2, 'Transfer total') == true) { //checks if line has'transfer'
        $arr2[] = $arrsyy[$key2 - 1];
        $arr2[] = $arrsyy[$key2]; // returns the transfer line for data size
    }
}
// echo '<pre>', print_r($arr2), '</pre>';
// die();
foreach ($arr2 as $key2 => $value2) {
    if (stristr($value2, 'BATCHCOPY - Copying tape') == true) { //checks if line has batchcopy header
        if (stristr($arr2[$key2 + 1], 'ensemble') == true) {
            if (stristr($arr2[$key2 + 2], 'ensemble') == true) {
                if (stristr($arr2[$key2 + 3], 'Transfer total')) {
                    $arr3[] = $arr2[$key2];
                    $arr3[] = $arr2[$key2 + 1];
                    $arr3[] = $arr2[$key2 + 2];
                    $arr3[] = $arr2[$key2 + 3];
                }
            }
        }
    }
}
// echo '<pre>', print_r($arr3), '</pre>';
// die();
$a_1 = 0;
$arr_size = count($arr3); //counts the amount of data in the appended $arr2 array

for ($a = 0, $b = 1, $c = 2, $d = 3; $a < $arr_size, $b < $arr_size, $c < $arr_size, $d < $arr_size; $a = $a + 4, $b = $b + 4, $c = $c + 4, $d = $d + 4) {//loops through the $arr array after every four lines to extract tape id, file_num_start, file_num_end and data size respectively
    $extract[$a_1] = array(
        'TAPE_ID' => trim(strbefore(strafter($arr3[$a], 'tape'), '{')), // extracts tapeid from key value 0
        'LINE NUM' => strbefore(strafter($arr3[$a], '{'), '}'), // extracts line  num from key value 0
        'SEQUENCE' => strbefore(strafter($arr3[$a], '('), ')'), // extracts squence from key value 0
        'num_of_files' => (int)(strbefore(strafter($arr3[$c], 'ensemble'), ',')) - (int)(strbefore(strafter($arr3[$b], 'ensemble'), ',')), // extracts num of files by subtracting from key value 1 and 2 
        'FILE_NUM_START' => strbefore(strafter($arr3[$b], 'ensemble'), ','), // extracts file num start from key value 1
        'FILE_NUM_END' => strbefore(strafter($arr3[$c], 'ensemble'), ','), // extracts file num end from key value 2
        'DATA_SIZE' => strbefore(strafter($arr3[$d], 'Mb,'), 'Gb') // extracts datasize from key value 3
    );
    $a_1++;
}
// echo '<pre>', print_r($extract), '</pre>';
// die();

//extract array to csv and DOWNLOAD 'tapes.csv' file (you will have to wait a while for large scratch files)
$filename = 'tapes.csv';
header("Content-type: text/csv");

header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
$output = fopen("php://output", "w");

$header = array_keys($extract[0]);

fputcsv($output, $header);

foreach ($extract as $row) {
    fputcsv($output, $row);
}
fclose($output);

?>