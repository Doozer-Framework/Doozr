<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Unit-Tests
 *
 * FolderExistTest.php - Tests if all default Framework folder are exist
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2013, Benjamin Carl - All rights reserved.
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
 *   must display the following acknowledgement: This product includes software
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
 * @category   DoozR
 * @package    DoozR_Test
 * @subpackage DoozR_Test_FileAndFolder_FolderExist
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

// get PHPUnit-Test-framework
require_once 'PHPUnit/Autoload.php';

/**
 * DoozR - Unit-Tests
 *
 * Tests if all default Framework folder are exist
 *
 * @category   DoozR
 * @package    DoozR_Test
 * @subpackage DoozR_Test_FileAndFolder_FolderExist
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_FolderExist extends PHPUnit_Framework_TestCase
{
    /**
     * the base path to operate on
     *
     * @var string
     * @access private
     */
    private $_basePath = '../Framework/Data/Private/';

    /**
     * tests if all framework default folder are Exist
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function testFolderExist()
    {
        // 1st test if folder exist
        $this->assertEquals(true, file_exists($this->_basePath));

        /**
         * now test if the child folder are writable
         */
        $childFolder = array(
            'Auth',
			'Cache' => array(
                'Clientdetect',
				'Default',
				'Ids',
				'Localization',
				'Smarty'
			),
			'Config' => array(
                'Ids',
				'Localization',
				'Smarty'
			),
			'Font' => array(
                'Ttf'
			),
			'Log',
            'Temp'
        );

        // iterate / recursive check
        $this->_exists($childFolder);
    }


    /**
     * recursive check existence of folder/files
     *
     * @param mixed  $fileOrFolder String-Folder/File OR Array-Folder/File to check
     * @param string $parent       Parentfolder to operate in
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _exists($fileOrFolder, $parent = '')
    {
        if (is_array($fileOrFolder)) {
            // check child folder for existence
            foreach ($fileOrFolder as $parentFolder => $childFolder) {
                if (!is_integer($parentFolder)) {
                    $this->_exists($parent.$parentFolder.'/');
                    $this->_exists($childFolder, $parent.$parentFolder.'/');
                } else {
                    $this->_exists($childFolder, $parent);
                }
            }
        } else {
            // check for existence
            $this->assertEquals(true, file_exists($this->_basePath.$parent.$fileOrFolder));
        }
    }
}

?>