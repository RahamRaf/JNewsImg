<!DOCTYPE html>
<html lang="en">
﻿<head>
  <meta charset="utf-8" />
  <title>عکس‌ساز ژوپی‌آ</title>
  <style>
    body {
      direction: rtl;
    }
    body,p,div,span,td,tr,li {
      font-family: tahoma, arial, sans;
      font-size: 1em;
    }
  </style>
</head>

<body>
<p>
<form method="post" action="" enctype="multipart/form-data" >
<h1>عکست کو؟</h1>
<p style="background:#e3e3e3; padding: 10px;"><input type="file" name="image_upload" /></p>
<p><input type="submit" value="بفرست بینیم" style="font-size:24px; padding:10px;" /></p>

</form>

<?php
//print_r($_SERVER);
$_SERVER;
if($_SERVER["REQUEST_METHOD"] == "POST") {
	//print_r($_FILES);
    $image = $_FILES["image_upload"];

    if ($image) {
        include_once './img.inc.php';
        $jimg = new JImgProcess;
        $result = $jimg->make_standard($image);

        if($result['result'] == true) {
          $imgsize = getimagesize($result["src"]);
          echo "<p>&nbsp;</p><div style=\"color:green; background:#e3e3e3; padding: 10px; margin: 10px 0;\">اندازه جدید عکست شده: <span style='font-weight: bold; direction: ltr;'>".$imgsize[0]."x".$imgsize[1]."px</span><div style='font-size: smaller;'>$result[msg]</div></div>";
          echo "<p>&nbsp;</p><img src='$result[src]' style='width: 80%; height: auto;' /><p>&nbsp;</p>";
        } else {
          echo "<p style=\"color:red; background:#e3e3e3; padding: 10px;\">$result[msg]</p>";
        }
    }

}
?>
</p>
</body>
</html>
