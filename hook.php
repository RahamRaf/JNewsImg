<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $uploadedfile = $_POST['oldfile'];

    if (isset($uploadedfile) && strlen($uploadedfile) > 10) {

      include_once './img.inc.php';
      $jimg = new JImgProcess;
      $result = $jimg->make_standard($uploadedfile);

      if($result['result'] == true) {
        $response = [
          'result' => $result['result'],
          'msg' => 'تو موفق شدی! باریکلا!',
          'src' => $result['src'],
          'ext' => $result['ext']
        ];
        echo json_encode($response);
      } else {
        //echo $result['msg'];
        echo json_encode(['result' => $result['result'], 'msg' => $result['msg']]);
      }
    }

} else { ?>
  <!DOCTYPE html>
  <html lang="en">
  ﻿<head>
    <meta charset="utf-8" />
  <title>عکس‌ساز خودکار ژوپی‌آ</title>
  </head>

  <body>

<form method="post" action="">
<p><label>آدرس کامل عکس رو اینجا کپی کن: <input type="text" placeholder="Image URL" name="oldfile" /></label></p>
<p><input type="submit" /></p>
</form>

</body>
<?php
  } ?>
