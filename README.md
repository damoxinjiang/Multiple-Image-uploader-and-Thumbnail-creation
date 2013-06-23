<<<<<<< HEAD
<h1>Multiple Image uploader with Thumbnail Creation</h1>

Download and try the code; let me know if you have any queries at <a href="mailto:jeremyrajan@gnossem.com">jeremyrajan@gmail.com</a>

Full tutorial can be accessed here: <a href="http://tuts.jeremyrajan.com/blog/multi-image-uploader-with-thumbnail-creation-in-php/">http://tuts.jeremyrajan.com/blog/multi-image-uploader-with-thumbnail-creation-in-php/</a>
=======

STEP1: 

The following code will allow you to upload multiple files to the server and I use the enctype="multipart/form-data" in my form. So lets start off with the form HTML code:
<code>
<h4>Upload your Files (*.jpg,*.png)</h4>
 <form action="" method="POST" enctype="multipart/form-data">
 <input type="file" name="files[]" multiple/>
 <input type="submit" value="Upload"/>
</form>
</code>

The above form, takes in “multipart/form-data” so that we can upload multiple files at one go.

Tip: Make sure to increase your upload_max_size and max_execution_time in your PHP settings, so that you don’t get error messages while uploading multiple files.

Now lets dig into some scripting, shall we! Lets check how to handle the files that we are uploading. First we will set the max upload size and if the size is within the limits you would not get the error messages otherwise proceed.

STEP2:
<code>
<?php
 if($file_size > 1310720){ //10MB = 1310720 Bytes
  $errors[]='File size must be less than 10 MB';
 }
?>
</code>

STEP3:

Next we set the $desired_dir, which will decide where the system will upload the files and if we do not find any errors in $errors[] then upload the files.

<code>
 <?php
  $desired_dir = "/path/to/the/upload/folder";

  if(empty($errors)==true){

     if(is_file("$desired_dir/".$file_name)==false) //checking whether the file is already uploaded or not

      {

         move_uploaded_file($file_tmp,"$desired_dir".$file_name); //moving the files

         $filesrc = $desired_dir.$file_name; //file_src for inserting file_src in DB

         //mysql query goes here for insertion into DB, you already have $file_src and $file_name.

         makeThumbnails($desired_dir,$file_name); //calling the function to create the Thumb

      }
    else{ 

         echo "The file is already uploaded My Friend. Please check!";

      }
  ?>
</code>

STEP4:

Now the part where we create the thumbs, the makeThumbnails() function:

<code>
<?php

if (!function_exists('makeThumbnails')) { //use this otherwise, we will get re-intilize error!
    function makeThumbnails($updir, $img)
     {
  $thumbnail_width = 160; //set the thumbnail width
	$thumbnail_height = 240; //set the thumbnail height
        $thumb_beforeword = "thumb"; //the "thumb" prefix, if you need!
        $arr_image_details = getimagesize("$updir$img"); //get the big image for resizing/making thumb
	$original_width = $arr_image_details[0];
        $original_height = $arr_image_details[1];
        if ($original_width > $original_height) {
	   $new_width = $thumbnail_width;
	   $new_height = intval($original_height * $new_width / $original_width);
        } else {
	   $new_height = $thumbnail_height;
	   $new_width = intval($original_width * $new_height / $original_height);
	}
	   $dest_x = intval(($thumbnail_width - $new_width) / 2);
	   $dest_y = intval(($thumbnail_height - $new_height) / 2);
	   if ($arr_image_details[2] == 1) {
		$imgt = "ImageGIF";
		$imgcreatefrom = "ImageCreateFromGIF";
	    }
	   if ($arr_image_details[2] == 2) {
		$imgt = "ImageJPEG";
		$imgcreatefrom = "ImageCreateFromJPEG";
	    }
	   if ($arr_image_details[2] == 3) {
		$imgt = "ImagePNG";
		$imgcreatefrom = "ImageCreateFromPNG";
	    }
	   if ($imgt) {
	        $thumb_dir = $updir."/resized/"; //Setting the thumbnail directory
		$old_image = $imgcreatefrom("$updir"."$img");
	        $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
		$white = imagecolorallocate($new_image, 255, 255, 255);
		imagefill($new_image, 0, 0, $white);
                imagecopyresampled($new_image, $old_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height); //new function php resampled for better image quality
	        $imgt($new_image, "$thumb_dir"."thumb_beforehand"."_"."$img"); //get the new image, set it to resized folder and prefix as defined earlier in the function.
	     }
	}
   }
?>
</code>
Thats it! Just one page of code to upload multiple files, insert the file data into DB and create thumbnail.
>>>>>>> 95f219d7ec18beee6c4d9c0fcfed5a28731d9597
