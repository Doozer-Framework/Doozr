<?php

require_once DOOZR_DOCUMENT_ROOT.'Core/Service/doozR_Formhelper/classes/input.aclass.php';


class Swfupload extends AInput
{
    public function setValue($value, $overrideIfExists = false)
    {
        if (!$overrideIfExists) {
            if ($this->_allreadySubmitted) {
                $value = $this->checkValue();
            }
        }

        $this->_attributes['value'] = $value;
    }


    public function getValue()
    {
        // if form was allready submitted retrieve value right now
        if ($this->_allreadySubmitted) {
            $this->_attributes['value'] = $this->checkValue();
        }

        return ($this->_attributes['value']) ? $this->_attributes['value'] : null;
    }


    public function setSize($size)
    {
        $this->_attributes['size'] = $size;
    }


    public function getSize()
    {
        return ($this->_attributes['size']) ? $this->_attributes['size'] : null;
    }


    public function setMaxlength($maxlength)
    {
        $this->_attributes['maxlength'] = $maxlength;
    }


    public function getMaxlength()
    {
        return ($this->_attributes['maxlength']) ? $this->_attributes['maxlength'] : null;
    }


    public function render($tabcount = 0)
    {
    	$includes = '
			<script type="text/javascript" src="view/static/js/swfupload/swfupload.js"></script>
			<script type="text/javascript" src="view/static/js/swfupload/swfupload.swfobject.js"></script>
			<script type="text/javascript" src="view/static/js/swfupload/fileprogress.js"></script>
			<script type="text/javascript" src="view/static/js/swfupload/handlers.js"></script>' . $this->nl();

        $html = $includes . $this->nl() . '
		<div>
            <div>
                <input type="text" id="txtFileName" disabled="true" />
                <span id="spanButtonPlaceholder"></span>
            </div>
            <div class="flash" id="fsUploadProgress">
                <!-- This is where the file progress gets shown.  SWFUpload doesnt update the UI directly.
                The Handlers (in handlers.js) process the upload events and make the UI updates -->
            </div>
            <input type="hidden" name="hidFileID" id="hidFileID" value="" />
            <!-- This is where the file ID is stored after SWFUpload uploads the file and gets the ID back from upload.php -->
        </div>';

		$html .= file_get_contents(DOOZR_DOCUMENT_ROOT.'Core/Service/doozR_Formhelper/lib/includes/swfupload/inline.js');
		$html .= '<script type="text/javascript" src="view/static/js/swfupload/doozRswfupload.js"></script>';

    	return $html;

    	/*
        // add elements html-code
        $html = $this->t($tabcount) . '<input type="' . $this->_type . '"';

        foreach ($this->_attributes as $attribute => $value) {
            $html .= ' ' . $attribute . '="' . $value . '"';
        }

        // end of element
        $html .= ' />' . $this->nl();

        // return generated html code
        return $html;
        */
    }
}

?>