<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Request - File - UploadedFileFactory.
 *
 * UploadedFileFactory.php - Transforms a passed PHP $_FILES upload array to PSR-7 structured upload array.
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2016, Benjamin Carl - All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - All advertising materials mentioning features or use of this software
 *   must display the following acknowledgment: This product includes software
 *   developed by Benjamin Carl and other contributors.
 * - Neither the name Benjamin Carl nor the names of other contributors
 *   may be used to endorse or promote products derived from this
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Please feel free to contact us via e-mail: opensource@clickalicious.de
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class.php';

/**
 * Doozr - Request - File - UploadedFileFactory.
 *
 * Transforms a passed PHP $_FILES upload array to PSR-7 structured upload array.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Request_File_UploadedFileFactory extends Doozr_Base_Class
{
    /**
     * Uploaded files input ($_FILES).
     *
     * @var array
     */
    protected static $uploadedFiles;

    /**
     * Parsed result (output) PSR-7 ready/compatible.
     *
     * @var array|null
     */
    protected static $parsedUploadedFiles;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param array $uploadedFiles Uploaded files array (e.g. $_FILES)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(array $uploadedFiles = [])
    {
        $this
            ->uploadedFiles($uploadedFiles);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Builds a PSR-7 compatible uploaded file structure and returns it.
     *
     * @param bool $force TRUE to force build, otherwise FALSE to accept runtime cached result
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Uploaded files in new structure (PSR-7)
     */
    public function build($force = false)
    {
        // Expensive: Try to get from last run ...
        if ((null === $parsedUploadedFiles = $this->getParsedUploadedFiles()) || (true === $force)) {
            $parsedUploadedFiles = [];

            // ... otherwise parse fresh
            foreach ($this->getUploadedFiles() as $argumentName => $file) {
                $parsedUploadedFiles[$argumentName] = $this->factoryFilesTree(
                    $file['name'],
                    $file['type'],
                    $file['tmp_name'],
                    $file['error'],
                    $file['size']
                );
            }

            $this->setParsedUploadedFiles($parsedUploadedFiles);
        }

        return $parsedUploadedFiles;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Recursive parses a passed uploaded file and builds the new structure.
     *
     * @param array|string $fileName          Name(s) of the uploaded file(s)
     * @param array|string $fileType          Type(s) of the uploaded file(s)
     * @param array|string $fileTemporaryName Temporary name(s) of the uploaded file(s)
     * @param array|int    $fileError         Error(s) of the uploaded file(s)
     * @param array|int    $fileSize          Filesize(s) of the uploaded file(s)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Uploaded file(s) in new structure (PSR).
     */
    protected function factoryFilesTree($fileName, $fileType, $fileTemporaryName, $fileError, $fileSize)
    {
        if (false === is_array($fileError)) {
            return new Doozr_Request_File(
                [
                    'name'     => $fileName,
                    'type'     => $fileType,
                    'tmp_name' => $fileTemporaryName,
                    'error'    => $fileError,
                    'size'     => $fileSize,
                ]
            );
        }

        $filesTree = [];

        foreach ($fileError as $nodeName => $errorData) {
            $filesTree[$nodeName] = $this->factoryFilesTree(
                $fileName[$nodeName],
                $fileType[$nodeName],
                $fileTemporaryName[$nodeName],
                $errorData,
                $fileSize[$nodeName]
            );
        }

        return $filesTree;
    }

    /**
     * Checks if the uploaded file was uploaded using array notation.
     * (e.g. TRUE if fieldname was something like foo[] or foo[bar] or foo[bar][]).
     *
     * @param string $argumentName Name of the argument to check (fieldname of upload/file element)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if array, otherwise FALSE
     */
    protected function isArrayUpload($argumentName)
    {
        return
            false === is_int($this->getUploadedFiles()[$argumentName]['error']) &&
            true === is_array($this->getUploadedFiles()[$argumentName]['error'])
        ;
    }

    /**
     * Checks if the uploaded file.
     *
     * @param string $argumentName Check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if upload is single file, otherwise FALSE
     */
    protected function isSingleUpload($argumentName)
    {
        return !$this->isArrayUpload($argumentName);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | SETTER & GETTER & ISSER & HASSER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for uploadedFiles.
     *
     * @param array $uploadedFiles Value for uploadedFiles to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setUploadedFiles(array $uploadedFiles)
    {
        self::$uploadedFiles = $uploadedFiles;
    }

    /**
     * Fluent: Setter for uploadedFiles.
     *
     * @param array $uploadedFiles Value for uploadedFiles to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function uploadedFiles(array $uploadedFiles)
    {
        $this->setUploadedFiles($uploadedFiles);

        return $this;
    }

    /**
     * Getter for uploadedFiles.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Value of uploadedFiles.
     */
    protected function getUploadedFiles()
    {
        return self::$uploadedFiles;
    }

    /**
     * Setter for parsedUploadedFiles.
     *
     * @param array $parsedUploadedFiles Value for parsedUploadedFiles to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setParsedUploadedFiles(array $parsedUploadedFiles)
    {
        self::$parsedUploadedFiles = $parsedUploadedFiles;
    }

    /**
     * Fluent: Setter for parsedUploadedFiles.
     *
     * @param array $parsedUploadedFiles Value for parsedUploadedFiles to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function parsedUploadedFiles(array $parsedUploadedFiles)
    {
        $this->setParsedUploadedFiles($parsedUploadedFiles);

        return $this;
    }

    /**
     * Getter for parsedUploadedFiles.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Value of parsedUploadedFiles.
     */
    protected function getParsedUploadedFiles()
    {
        return self::$parsedUploadedFiles;
    }
}
