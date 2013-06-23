<?php 
//including joomla core for DB class, you can use your own classes 
  define( '_JEXEC', 0 );
  define( 'DS', DIRECTORY_SEPARATOR );
  define( 'JPATH_BASE', $_SERVER[ 'DOCUMENT_ROOT' ] );
  require_once( JPATH_BASE . DS . 'includes' . DS . 'defines.php' );
  require_once( JPATH_BASE . DS . 'includes' . DS . 'framework.php' );
  require_once( JPATH_BASE . DS . 'libraries' . DS . 'joomla' . DS . 'factory.php' );
  $mainframe =& JFactory::getApplication('site');
  $db = JFactory::getDBO(); //include joomla DB class
  $img_tag = $_POST["img_tag"];
?>
<html>
	<head>
		<title>Image Uploader</title>
		<link rel="stylesheet" type="text/css" href="up.css"/>
	</head>
	
	<body>
		<div class="header"><h1>Image Uploader</h1></div>
	    <div class="wrapper">
			<div class="img_con">
				<h4>STEP1 => Enter Image Tag to see the already uploaded images!</h4>
				<form action="" method="POST">
					<input type="text" name="img_tag" placeholder="Enter image tag here"/>
					<input type="submit" value="Submit"/>
				</form>
				
				<div class="al_img">
					<?php
					//this is something designed for my DB to find the image id for a particular image tag or category
						$sel_id = "SELECT img_id FROM #__jos_image WHERE image_tag = '$img_tag'";
						$db->setQuery($sel_id);
						$idlist = $db->loadObjectList();
						foreach ($idlist as $r)
						{
							$img_id = $r->img_id;
							//echo $img_id;
						}
						//get the file_name and display the images which has been already uploaded for particular image tag
						if($img_id != ""){
							$sel_img = "SELECT file_name FROM #__jos_image_files WHERE file_image_id = '$img_id'";
							$db->setQuery($sel_img);
							$imglist = $db->loadObjectList();
							foreach ($imglist as $s)
							{
								$img_src = $s->file_name;
								//echo $img_src;
								if($img_src == ""){
									echo "This is a new image category/Tag, please upload the images!";
									echo "<script>
											document.getElementById('no_image_mesg').style.display= 'none';
										  </script>
										 ";		
								}
								else{
									echo "<div class='al_img' id='alg_img_$img_src'>
											<img class='img_$img_src' src='$img_src'/><br/>
										</div>";
								}
							}
						}
						
						else{
							echo "<div id='no_img_mesg'>The image category does not exist, please go to backend and create the image tag/category!!</div>";
						}
					?>
					
				</div>
				<span style="clear:both;"></span>
			</div>
			
			<div class="up_con">
				<h4>STEP 2 => Please upload images!!!</h4>
				<form action="" method="POST" enctype="multipart/form-data">
					<input type="file" name="files[]" multiple/>
					<input type="hidden" name="img_id" value= "<?php echo $img_id; ?>"/>
					<input type="submit" value="Upload"/>
				</form>
				<!--script--->
				<?php
					if(isset($_FILES['files'])){
					    $errors= array();
						foreach($_FILES['files']['tmp_name'] as $key => $tmp_name ){
							$file_name = $_FILES['files']['name'][$key];
							$file_size =$_FILES['files']['size'][$key];
							$file_tmp =$_FILES['files']['tmp_name'][$key];
							$file_type=$_FILES['files']['type'][$key];	
							
							
							//function for creating thumbnail
							if (!function_exists('makeThumbnails')) {
							 function makeThumbnails($updir, $img)
								{
								    $thumbnail_width = 160;
								    $thumbnail_height = 240;
								    $thumb_beforeword = "thumb";
								    $arr_image_details = getimagesize("$updir$img");
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
								    	$thumb_dir = $updir."/resized/"; //make sure the directory has 777 permissions
								        $old_image = $imgcreatefrom("$updir"."$img");
								        //$new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
								        $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
										$white = imagecolorallocate($new_image, 255, 255, 255);
										imagefill($new_image, 0, 0, $white);
								        imagecopyresampled($new_image, $old_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
								        $img_det[]=explode(".", $img);
										$img_name = $img_det[0][0];
										$img_ext = $img_ext[0][1];
								        $imgt($new_image, "$thumb_dir" ."$img_name" ."_"."$thumbnail_height"."x"."$thumbnail_width".".jpg");
								    }
								  }
								}
							//function for creating thumbnail
							
					        if($file_size > 52428800){
								$errors[]='File size must be less than 50 MB';
					        }		
							
							
					       $desired_dir = "./uploads/"; //if doesnt work try uploads/
					        if(empty($errors)==true){
					            if(is_file("$desired_dir/".$file_name)==false){
					                move_uploaded_file($file_tmp,"$desired_dir".$file_name);
									$filesrc = $desired_dir.$file_name;
									//$thumbsrc = $desired_dir."resized/".$file_name;
									//echo $filesrc;
									
									$img_id = $_POST["img_id"];
									
									//insert into DB, this has been coded to my project needs, you can change your insert query here
									$insert = "INSERT INTO #__jos_image_files(file_image_id,file_name,file_title,file_extension,file_mimetype,file_published,file_is_image,file_image_thumb_height,file_image_thumb_width) VALUES 
													('$img_id','$file_src','$file_name','jpg','image/jpeg','1','1','240','160')";
									$db->setQuery($insert);
									$db->query();
									//insert into DB
									
									$updir = $desired_dir.'resized/';
									$desired_width = "240px";
									echo "<div class='img_con'>";
									echo "<img class='main_img' src='$filesrc'/>";
									echo "</div>";
									echo "<span style='clear:both;'></span>";
									echo "<script>
											document.getElementById('no_img_mesg').style.display= 'none';
										  </script>
										 ";
									makeThumbnails($desired_dir,$file_name);
									//resize($filesrc, $destsrc, 240, 240);
					            }else{									
					                echo "The file is already uploaded My Friend. Please check with the image tag above!!";	
									echo "<script>
											document.getElementById('no_image_mesg').style.display= 'none';
										  </script>
										 ";			
					            }			
					        }else{
					                print_r($errors);
					        }
					    }
						if(empty($error)){
							//echo "Success";
							//echo $desired_dir.$file_name;
							//didn't use insert here because we wont be able to get info for every image, only will be able to get the final image in the loop.
						}
					}
					?>
				<!--script--->
			</div>	
		</div>
	</body>
</html>

