<?php
$_SERVER;

if($_SERVER["REQUEST_METHOD"] == "POST") {

  //check API Key
  if(!isset($_POST['key']) && $_POST['key'] != "7598903cjflkfcl224i242ox42klwd")
    die ( json_encode( array("result" => false, "msg" => 'Wrong API Key!') ) );

    $uploadedcontent = $_POST['contenthtml'];

    if (isset($uploadedcontent) && strlen($uploadedcontent) > 50) {

		    $errors = 0;

        $description_dom = new DOMDocument();
        $description_dom->loadHTML( (string)$uploadedcontent );

        // Switch back to SimpleXML for readability
        $description_sxml = simplexml_import_dom( $description_dom );

        //flush output
        $final = array('w' => 0, 'h' => 0); //Final largest image
        $jpg = array(); //keep other post images that ar larger than 300x300px

        // Find all images, and extract their 'src' param
        $imgs = $description_sxml->xpath('//img');
        $d = array(0, 0);

        foreach($imgs as $image) {

              //get file extension
              $tmp = stripslashes($image['src']);
              $tmp = end(explode('.', $tmp));
              $tmpext = strtolower($tmp);

              //get image src from content and download it to the server
              include_once './img.inc.php';
              $jimg = new JImgProcess;
              $getimg = $jimg->get_remote_image($image['src'], $tmpext);

              if($getimg['result'] === false) {
                die ( json_encode( $getimg ) );
              }
              $newimg = $getimg['msg'];

             //get dimentions of the images to get biggest images.
             $d = getimagesize($newimg);

             //compare with image before
             if( $final['w'] < $d[0] && $d[1] >= 300) {
               //check for width: assign new image if it's bigger than previous one
               //we don't accept images that their height is smaller than 300px

                 $final['ext'] = $tmpext;

                 $final['src'] = $newimg;
                 $final['w'] = $d[0];
                 $final['h'] = $d[1];

             } else if($d[0]>=300 && $d[1]>=300) {
               //keep images of content that are bigger than 300x300px

               $jpg[] = array('src' => $newimg , 'w' => $d[0], 'h' => $d[1], 'ext' => $tmpext);

             } else {
               //remove small image from Servers

               unlink ($newimg);
             }

        }

        //add them to output if content had extra useful pictures
        if( count($jpg) > 0) {
          $final['extra'] = $jpg;
        }

        //export final largest image
        die ( json_encode($final) );
    }
    die ( json_encode( array('result' => false , 'msg' => 'Supplied html code was too short!')));

} else { ?>

  <!DOCTYPE html>
  <html lang="en">
  ﻿<head>
    <meta charset="utf-8" />

    <title>عکس‌یاب خودکار ژوپی‌آ</title>
  </head>

  <body style="direction: rtl;">

    <h2>همین‌جوری کون‌لخت پاشدی اومدی اینجا فکر کردی چیزی گیرت میاد؟! نه جانم از این خبرا نیست.</h2>
    <form method="post" action="">
      <textarea name="contenthtml" id="contenthtml"></textarea>
      <input type="hidden" name="key" id="key" value="7598903cjflkfcl224i242ox42klwd" />
      <input type="submit" />
    </form>

  </body>
  </html>

<?php
  }
?>
