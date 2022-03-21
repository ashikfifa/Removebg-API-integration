<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">



    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link  type="text/javascript" href="progress.js">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Cutoutwiz</title>

   

</head>

<body>



<div class="container">
<div class='row' style="margin-top:4%">
<p style="font-weight:700;font-size:77px;color:#125a31"> THE KOW COMPANY</p>
<br> <br>
      <div class='col-md-6'>
        <img src="upload/back.jpg">
      </div>
      <div class='col-md-6'>
        <p style="font-size:50px;font-weight:700">Remove Image Background</p>
        <p style="font-size:20px">100% Automatically and <b><u>Free</u></b>
        </p>

      </div>
    
      </div>
</div>

<div class="container">
    <br>
    <p style="float:right"> <span  id="estimated_timeinhr">Estimation time: </span>
       <span  id="estimated_timeinmin">:: </span>
      <span  id="estimated_timeinsec"> </span> </p>

<form method="post" name="image_upload_form" id="image_upload_form" enctype="multipart/form-data">
    <input type="file" name="files[]" id="image_upload" multiple>    
    <input type="submit"id="uploadFile" name="submit" value="Upload">
</form>

<div class='progress' id='progressBarDiv'>
            <div class='progress-bar' id='progressBar'></div>
            <div class='percent' id='percent'></div>
     </div>


<div id="status"></div>

<?php

require_once 'vendor/autoload.php';
$upload_dir = 'upload/';
$api_key = 't1LghFrFpi9ZW4ahNWkxzRrd';



if(isset($_POST['submit'])) {
 
   
    // Configure upload directory and allowed file types
    $upload_dir = 'upload'.DIRECTORY_SEPARATOR;
    $allowed_types = array('jpg', 'png', 'jpeg','tif','tiff');

     
    // Define maxsize for files i.e 2MB
    $maxsize = 2 * 1024 * 1024;

    $totalsize = 0;
    $i=0;
    
 
    // Checks if user sent an empty form
    if(!empty(array_filter($_FILES['files']['name']))) {

      
        $index=1;
        // Loop through each file in files[] array
        foreach ($_FILES['files']['tmp_name'] as $key => $value) {
            
            $file_tmpname = $_FILES['files']['tmp_name'][$key];
            $file_name = $_FILES['files']['name'][$key];
            $file_size = $_FILES['files']['size'][$key];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            // $est_time = $total/ 2097152;

            $totalsize = $totalsize + $file_size;
            $i++;

            $filepath = $upload_dir.$file_name;
 
            // Check file type is allowed or not
            if(in_array(strtolower($file_ext), $allowed_types)) {
 
         
                // If file with name already exist then append time in
                // front of name of the file to avoid overwriting of file
                if(file_exists($filepath)) {
                    $filepath = $upload_dir.time().$file_name;
                     
                    move_uploaded_file($file_tmpname, $filepath);
                   
                }
                else {
                 
                     move_uploaded_file($file_tmpname, $filepath);
                        
                  
                }

                   // Start IBR
              
            $path_info = pathinfo($filepath);
            // $path_size = pathinfo($file_size);
            $client = new GuzzleHttp\Client();
    
            $res = $client->post('https://api.remove.bg/v1.0/removebg', [
                'multipart' => [
                    [
                        'name'     => 'image_file',
                        'contents' => fopen( $filepath, 'r'),
                    ],
                    [
                        'name'     => 'size',
                        'contents' => 'auto',
                        // 'contents' => fopen( $file_size,'r'),
                    ],
                    [
                        'name'     => 'bg_color',
                        'contents' => 'fff',
                    ],
                ],
                'headers' => [
                    'X-Api-Key' => $api_key,
                ],
            ]);

            echo "<script type=\"text/javascript\">

                var e = document.getElementById('percent'); 
                e.innerHTML ='" . $index . "';

                </script>";
            
               $index=$index+10;
            $fp = fopen("{$upload_dir}{$path_info['filename']}-no-bg.png", "wb");

            fwrite($fp, $res->getBody());
            fclose($fp);
            
            echo "<div class='row'><div class='col-md-6'> <img src='{$upload_dir}{$path_info['filename']}-no-bg.png' style='padding-top:5%'> <br>{$path_info['filename']} 
            </div></div>";
            

            // End IBR

            }
            else {
                 
                // If file extension not valid
                echo "Error uploading {$file_name} ";
                echo "({$file_ext} file type is not allowed)<br / >";
            }
        }
     
        $size = $totalsize/ 1000000;
        $est_time = (5 * $i) + ($size/ 2.3);

        $est_time_min= "00";$est_time_hr="00";


        if($est_time > 60)
          $est_time_min= round($est_time/60,2);

        $est_time_sec= round($est_time - (60 * $est_time_min));


        if($est_time_min > 60)
            $est_time_hr =round ($est_time_min / 60,2);



        echo"Estimated Time : {$est_time_hr} : {$est_time_min} :{$est_time_sec}";
    }
    else {
         
        // If no files selected
        echo "No files selected.";
    }
}


?>
 
</div>




<script>


$('#image_upload').on('change', function() {
var fileCount = document.getElementById('image_upload').files.length;
var files = $('#image_upload')[0].files;
var totalSize = 0;

for (var i = 0; i < files.length; i++) {
  // calculate total size of all files        
  totalSize += files[i].size;
}
//   alert(totalSize)

//----------------

var sizeInMB = totalSize/ 1000000;
    var est_time = (5 * fileCount) + (sizeInMB/ 2.3);

      var est_time_min = "00",est_time_hr="00";


      if(est_time > 60)
        est_time_min= ($est_time/60);


      var est_time_sec= (est_time - (60 * est_time_min));
      est_time_sec_cut= est_time_sec.toFixed(2);


      if(est_time_min > 60)
          est_time_hr = (est_time_min / 60);


//-------------
$("#estimated_timeinsec").text(est_time_sec_cut);
 $("#estimated_timeinmin").text(est_time_min);
 $("#estimated_timeinhr").text(est_time_hr);

});

/////




 
</script>


</body>
</html>
