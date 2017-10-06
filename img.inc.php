<?php

/**
 * Class for JoopeA's need on images
 * @param Int $min_w_st: minimum acceptable width for standard image in posix_get_last_error
 * @param Int $min_h_st: minimum acceptable height for standard image in posix_get_last_error
 * @param Int $max_w: maximum width for our saved images on server
 * @param Array $aspectratio: two integer that specify the standard image aspect ratio
 */
class JImgProcess
{

  public $min_w_st = 600;
  public $min_h_st = 338;
  public $max_w = 1920;
  public $aspectratio = array( 16 , 9 );


  /**
  * This function creates and outputs standard 16:9 image that is validated
  * @param $image: image object or image url
  * @return array(result=>[boolean], msg=>[string], src=>[string/null], ext=>[string/null])
  *   $result is true if make_standard faced no errors, unless return false
  *   $msg is an string rather say reason of an error or success msg
  *   $src is address of standard image
  *   $ext is extension of the image
  */
  function make_standard($image) {

    if(!isset($image)) {
      $return = array('result' => false, 'msg' => 'این فایله که عکس نبود عمو!');
      return ($return);
    }
//print_r($image);
    $uploadedfile = (isset($image['tmp_name']) && strlen($image['tmp_name']) > 10) ? $image['tmp_name'] : $image;
//echo "<br>tmp: ".$uploadedfile;
    $result = true;
    $error_txt = "";

    $filename = (isset($image['name']) && strlen($image['name']) > 4) ? stripslashes($image['name']) : stripslashes($image);

//echo "<br>name: ".$filename;
    $extension = end(explode('.', $filename));
    $extension = strtolower($extension);

    //print_r($image);
    //echo "<br>Na: ".$filename."<br>Ex: ".$extension;

    if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) {
      //Check if it is an image file
        $return = array('result' => false, 'msg' => 'این فایله که عکس نبود عمو!');
        return ($return);
    } else {
      //Check if the image file is not too large (5MB) or too small (10KB)
      //but we have to check if that's a remote file or local
        if(!substr_compare($uploadedfile, "http", 0, 3, true)){

          $imgtmp = $this->get_remote_image($uploadedfile, $extension);
          if($imgtmp['result'] === false) {
            return $imgtmp;
          }

          $uploadedfile = $imgtmp['msg'];
        }

        $size = filesize($uploadedfile);

        if ($size > 5242880) {
            $return = array('result' => false, 'msg' => "عکست خیلی بزرگه بابا!");
            return ($return);
        } else if ($size < 10240) {
            $return = array('result' => false, 'msg' => "واقعن می‌خوای از عکس به این بدکیفیتی استفاده کنی؟ یکی دیگه انتخاب کن!");
            return ($return);
        }
    }

    list($width,$height)=getimagesize($uploadedfile);

    if($height > $width) {
       $return = array('result' => false, 'msg' => "عکست عمودیه به درد خودت می‌خوره!");
       return ($return);
    }
    else if( $width < $this->min_w_st || $height < $this->min_h_st ) {
      $return = array('result' => false, 'msg' => "عکس به این کوچیکی رو که با میکروسکوپ هم نمی‌شه دید! یک بزرگترش رو بفرست.");
      return ($return);
    }

    //if the image is too large, reduce width to max allowed size
    $width = ( $width > $this->max_w ) ? $this->max_w : $width;

    //get 16:9 aspect ratio of the image
    $newwidth  = $width;
    $newheight = $this->aspectratio[1] * $width / $this->aspectratio[0];

    //check if the new height is taller than old img yet, unless re-calculate
    if($newheight > $height) {
      $newheight = $height;
      $newwidth = $this->aspectratio[0] * $height / $this->aspectratio[1];
    }

    $filename = "tmpimg/".time().".".$extension;

    $imagic = new Imagick($uploadedfile);
    $imagic->cropThumbnailImage($newwidth, $newheight);
    $imagic->writeImage($filename);

    $return = array('result' => $result, 'msg' => 'بفرما اینم عکس استاندارد', 'src' => $filename, 'ext' => $extension);
    return($return);
  }

  /**
  * This function copies image from url to tmp folder
  * @param $url: image url
  * @param $ext: extension of the image (jpg/jpeg/png/gif)
  * @return array(result=>[boolean], msg=>[string])
  *   $result is true if make_standard faced no errors, unless return false
  *   $msg is an string rather say reason of an error or filename if successfull
  *   $ext is image extention
  */
  function get_remote_image($url, $ext = "") {
    //create temporary name for images
    $iname = uniqid('mgis');

    // create temporary file to store data from $url
      if (false === ($tmpfname = tempnam('./tmpimg', $iname))) {
              return array('result' => false, 'msg' => "Can't make temp file name: $iname");
      }
      // open input and output
      if (false === ($in = fopen($url, 'rb')) || false === ($out = fopen($tmpfname, 'wb'))) {
              unlink($tmpfname);
              return array('result' => false, 'msg' => 'Can\'t read image from url or write it to file on server.');
      }

      //copy images
      stream_copy_to_stream($in, $out);

      // close input and output file
      fclose($in);
      fclose($out);


      if ($ext != "") {
        //$tmppath = dirname(__FILE__);
        $tmpfname = basename($tmpfname);
        $tmppath = "./tmpimg/";
        rename($tmppath.$tmpfname, $tmppath.$tmpfname.".".$ext);
        $tmpfname = "tmpimg/$tmpfname.$ext";
      }

      return array('result' => true, 'msg' => $tmpfname, 'ext' => $ext);
  }
}



?>
