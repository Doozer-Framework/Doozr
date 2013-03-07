<?php

require_once '../Controller/Core/Core.php';

$DoozR = DoozR_Core::getInstance();

$_dREQUEST = $DoozR->getRequest();

if (isset($_dREQUEST['style'])) {
    $stylesheet = $_dREQUEST['style'];	
} else {
	$stylesheet = 'default';
}
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>doozR_Formhelper Form-Master</title>
	
	<link rel="stylesheet" href="<?=$stylesheet;?>.css" type="text/css" media="all" /> 
</head>
<body>
	
	<div id="content">
		<!-- 
		 block element [defines a block in a form - so a group of items can be seperated from other group of fields] 
		-->
		<div id="block1" class="DoozR-Formhelper-block">
			<!--
			 row element [defines a row in a block]
			-->
			<div id="block1-row1" class="DoozR-Formhelper-row">
				<!--
                    col element [defines a col (from a layout) in a row]	
				-->
                <div id="block1-row1-col1" class="DoozR-Formhelper-col">
                	<label for="textinput">textinput</label>
					<input type="text" name="textinput" maxlength="255" />
                </div>
                <!--
                    col element [defines a col (from a layout) in a row]    
                -->
                <div id="block1-row1-col2" class="DoozR-Formhelper-col">
                    <label for="textinput2">textinput2</label>
                    <input type="text" name="textinput2" maxlength="255" />
                </div>
                <!--
                    col element [defines a col (from a layout) in a row]    
                -->
                <div id="block1-row1-col3" class="DoozR-Formhelper-col">
                    <label for="textinput3">textinput3</label><br />
                    <input type="text" name="textinput3" maxlength="255" />
                </div>  							
			</div>
		</div>
	</div>
	
</body>
</html>