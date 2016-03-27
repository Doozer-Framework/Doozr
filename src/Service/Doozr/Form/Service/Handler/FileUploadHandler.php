<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service - Handler - FileUploadHandler.
 *
 * FileUploadHandler.php - Handler for file uploads. Handles uploaded files
 * (e.g. store for further validation). The handler also ensures that all
 * valid uploaded files are provided as emulated file uploads on page/step
 * reloads and when jumping into a step.
 *
 * Important note: This getUploadedFiles is used only in context of the form
 * service and so its not only responsible for uploaded files but also for
 * already uploaded and validated files.
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
 * Doozr - Form - Service - Handler - FileUploadHandler.
 *
 * Handler for file uploads. Handles uploaded files
 * (e.g. store for further validation). The handler also ensures that all
 * valid uploaded files are provided as emulated file uploads on page/step
 * reloads and when jumping into a step.
 * Important: This getUploadedFiles is used only in context of the form service and so
 * its not only responsible for uploaded files but also for already uploaded
 * and validated files.
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
class Doozr_Form_Service_Handler_FileUploadHandler extends Doozr_Base_Class
    implements Doozr_Form_Service_Handler_Interface
{
    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns uploaded files.
     *
     * It does transfer the file to systems temporary location and store information about it for validation
     * (if required).
     *
     * @param int   $step  Step to process
     * @param array $pool  Pool to use/parse as fallback (e.g. used when jump is active ...)
     * @param array $files Files array of PHP ($_FILES) to check for uploaded files
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Array containing file(s) information, for either uploaded or reloaded for step from store!
     */
    public function getUploadedFiles($step, array $pool = null, array $files = null)
    {
        // Result is empty if no files at all are uploaded ...
        $result = [];

        // 1. Get files for this step from pool
        if (null !== $pool && count($pool) > 0) {
            $result = (isset($pool[Doozr_Form_Service_Constant::IDENTIFIER_FILES][$step])) ?
                $pool[Doozr_Form_Service_Constant::IDENTIFIER_FILES][$step] :
                [];
        }

        // 2. Get uploaded files for this step - if any ...
        if (null !== $files && count($files) > 0) {
            $normalizedFiles = $this->extract($files);

            foreach ($normalizedFiles as $key => $value) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Extracts the file information as flat array.
     *
     * @param array $files File array to parse
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Resulting array
     */
    protected function extract(array $files)
    {
        foreach ($files as $key => $file) {
            if ($file instanceof Doozr_Request_File) {

                // Simple security layer
                $filename = $this->cleanFilename($file->getClientFilename());

                // @todo Move to a basic validation applied always for files if enabled in config.
                #$mediaType = $this->getMimeTypeByExtension(pathinfo($filename)['extension']);

                /* @var Doozr_Request_File $file */
                // Put everything in a simple array structure ...
                $files[$key] = [
                    'name'     => $filename,
                    'type'     => $file->getClientMediaType(),
                    'tmp_name' => $file->getTemporaryName(),
                    'error'    => $file->getError(),
                    'size'     => $file->getSize(),
                    'emulated' => false,
                ];
            } else {
                $files[$key] = $this->extract($file);
            }
        }

        return $files;
    }

    /**
     * Simple cleanup filter for filenames.
     * Use this method to clean an uploaded files from critical (insecure) characters.
     *
     * @param string $filename Filename to clean/filter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Cleaned filename
     */
    protected function cleanFilename($filename)
    {
        return mb_ereg_replace('([^\w\s\d\-_~,;\[\]\(\).])', '', $filename);
    }

    /**
     * Returns a files mime-type by its extension.
     * (e.g. would return mime-type 'text/plain' for extension 'txt').
     *
     * @param string $extension Extension used for lookup.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null Mime-type as string, NULL on no match
     */
    protected function getMimeTypeByExtension($extension)
    {
        $matrix = [
            'ez'      => 'application/andrew-inset',
            'hqx'     => 'application/mac-binhex40',
            'cpt'     => 'application/mac-compactpro',
            'doc'     => 'application/msword',
            'bin'     => 'application/octet-stream',
            'dms'     => 'application/octet-stream',
            'lha'     => 'application/octet-stream',
            'lzh'     => 'application/octet-stream',
            'exe'     => 'application/octet-stream',
            'class'   => 'application/octet-stream',
            'so'      => 'application/octet-stream',
            'dll'     => 'application/octet-stream',
            'oda'     => 'application/oda',
            'pdf'     => 'application/pdf',
            'ai'      => 'application/postscript',
            'eps'     => 'application/postscript',
            'ps'      => 'application/postscript',
            'smi'     => 'application/smil',
            'smil'    => 'application/smil',
            'wbxml'   => 'application/vnd.wap.wbxml',
            'wmlc'    => 'application/vnd.wap.wmlc',
            'wmlsc'   => 'application/vnd.wap.wmlscriptc',
            'bcpio'   => 'application/x-bcpio',
            'vcd'     => 'application/x-cdlink',
            'pgn'     => 'application/x-chess-pgn',
            'cpio'    => 'application/x-cpio',
            'csh'     => 'application/x-csh',
            'dcr'     => 'application/x-director',
            'dir'     => 'application/x-director',
            'dxr'     => 'application/x-director',
            'dvi'     => 'application/x-dvi',
            'spl'     => 'application/x-futuresplash',
            'gtar'    => 'application/x-gtar',
            'hdf'     => 'application/x-hdf',
            'js'      => 'application/x-javascript',
            'skp'     => 'application/x-koan',
            'skd'     => 'application/x-koan',
            'skt'     => 'application/x-koan',
            'skm'     => 'application/x-koan',
            'latex'   => 'application/x-latex',
            'nc'      => 'application/x-netcdf',
            'cdf'     => 'application/x-netcdf',
            'sh'      => 'application/x-sh',
            'shar'    => 'application/x-shar',
            'swf'     => 'application/x-shockwave-flash',
            'sit'     => 'application/x-stuffit',
            'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc'  => 'application/x-sv4crc',
            'tar'     => 'application/x-tar',
            'tcl'     => 'application/x-tcl',
            'tex'     => 'application/x-tex',
            'texinfo' => 'application/x-texinfo',
            'texi'    => 'application/x-texinfo',
            't'       => 'application/x-troff',
            'tr'      => 'application/x-troff',
            'roff'    => 'application/x-troff',
            'man'     => 'application/x-troff-man',
            'me'      => 'application/x-troff-me',
            'ms'      => 'application/x-troff-ms',
            'ustar'   => 'application/x-ustar',
            'src'     => 'application/x-wais-source',
            'xhtml'   => 'application/xhtml+xml',
            'xht'     => 'application/xhtml+xml',
            'zip'     => 'application/zip',
            'au'      => 'audio/basic',
            'snd'     => 'audio/basic',
            'mid'     => 'audio/midi',
            'midi'    => 'audio/midi',
            'kar'     => 'audio/midi',
            'mpga'    => 'audio/mpeg',
            'mp2'     => 'audio/mpeg',
            'mp3'     => 'audio/mpeg',
            'aif'     => 'audio/x-aiff',
            'aiff'    => 'audio/x-aiff',
            'aifc'    => 'audio/x-aiff',
            'm3u'     => 'audio/x-mpegurl',
            'ram'     => 'audio/x-pn-realaudio',
            'rm'      => 'audio/x-pn-realaudio',
            'rpm'     => 'audio/x-pn-realaudio-plugin',
            'ra'      => 'audio/x-realaudio',
            'wav'     => 'audio/x-wav',
            'pdb'     => 'chemical/x-pdb',
            'xyz'     => 'chemical/x-xyz',
            'bmp'     => 'image/bmp',
            'gif'     => 'image/gif',
            'ief'     => 'image/ief',
            'jpeg'    => 'image/jpeg',
            'jpg'     => 'image/jpeg',
            'jpe'     => 'image/jpeg',
            'png'     => 'image/png',
            'tiff'    => 'image/tiff',
            'tif'     => 'image/tif',
            'djvu'    => 'image/vnd.djvu',
            'djv'     => 'image/vnd.djvu',
            'wbmp'    => 'image/vnd.wap.wbmp',
            'ras'     => 'image/x-cmu-raster',
            'pnm'     => 'image/x-portable-anymap',
            'pbm'     => 'image/x-portable-bitmap',
            'pgm'     => 'image/x-portable-graymap',
            'ppm'     => 'image/x-portable-pixmap',
            'rgb'     => 'image/x-rgb',
            'xbm'     => 'image/x-xbitmap',
            'xpm'     => 'image/x-xpixmap',
            'xwd'     => 'image/x-windowdump',
            'igs'     => 'model/iges',
            'iges'    => 'model/iges',
            'msh'     => 'model/mesh',
            'mesh'    => 'model/mesh',
            'silo'    => 'model/mesh',
            'wrl'     => 'model/vrml',
            'vrml'    => 'model/vrml',
            'css'     => 'text/css',
            'html'    => 'text/html',
            'htm'     => 'text/html',
            'asc'     => 'text/plain',
            'txt'     => 'text/plain',
            'rtx'     => 'text/richtext',
            'rtf'     => 'text/rtf',
            'sgml'    => 'text/sgml',
            'sgm'     => 'text/sgml',
            'tsv'     => 'text/tab-seperated-values',
            'wml'     => 'text/vnd.wap.wml',
            'wmls'    => 'text/vnd.wap.wmlscript',
            'etx'     => 'text/x-setext',
            'xml'     => 'text/xml',
            'xsl'     => 'text/xml',
            'mpeg'    => 'video/mpeg',
            'mpg'     => 'video/mpeg',
            'mpe'     => 'video/mpeg',
            'qt'      => 'video/quicktime',
            'mov'     => 'video/quicktime',
            'mxu'     => 'video/vnd.mpegurl',
            'avi'     => 'video/x-msvideo',
            'movie'   => 'video/x-sgi-movie',
            'ice'     => 'x-conference-xcooltalk',
        ];

        return (isset($matrix[$extension])) ? $matrix[$extension] : null;
    }
}
