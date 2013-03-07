<?php

// include DoozR core
require_once 'Controller/Core/Core.php';

// instanciate DoozR core
$DoozR = DoozR_Core::getInstance();

// dummy unlink	
if (file_exists(DOOZR_DOCUMENT_ROOT . 'Data/upload/' . $_FILES['resume_file']['name'])) {
	unlink(DOOZR_DOCUMENT_ROOT . 'Data/upload/' . $_FILES['resume_file']['name']);
}

// dummy move
move_uploaded_file($_FILES['resume_file']['tmp_name'], DOOZR_DOCUMENT_ROOT . 'Data/upload/' . $_FILES['resume_file']['name']); 

?>
