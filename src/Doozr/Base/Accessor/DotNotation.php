<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - Accessor - DotNotation.
 *
 * DotNotation.php - DotNotation accessor for array structures.
 *
 * Dot notation for access multidimensional arrays.
 *
 * $dn = new DotNotation(['bar'=>['baz'=>['foo'=>true]]]);
 *
 * $value = $dn->get('bar.baz.foo'); // $value == true
 *
 * $dn->set('bar.baz.foo', false); // ['foo'=>false]
 *
 * $dn->add('bar.baz', ['boo'=>true]); // ['foo'=>false,'boo'=>true]
 *
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
 * @author     Anton Medvedev <anton (at) elfet (dot) ru>
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
 * Doozr - Base - Accessor - DotNotation.
 *
 * DotNotation accessor for array structures.
 *
 * @category   Doozr
 *
 * @author     Anton Medvedev <anton (at) elfet (dot) ru>
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Base_Accessor_DotNotation extends Doozr_Base_Class
{
    /**
     * Values.
     *
     * @var array
     */
    protected $values = [];

    /**
     * Separator pattern.
     *
     * @var string
     */
    const SEPARATOR = '/[:\.]/';

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param array $values Array of values to provide access to.
     *
     * @author Anton Medvedev <anton (at) elfet (dot) ru>
     */
    public function __construct(array $values = [])
    {
        $this
            ->values($values);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns value by path.
     *
     * @param string $path    Path to return value for.
     * @param string $default Default value to return if path could no be resolved.
     *
     * @author Anton Medvedev <anton (at) elfet (dot) ru>
     *
     * @return mixed Value by path, or default value
     */
    public function get($path, $default = null)
    {
        $array = $this->getValues();

        if (!empty($path)) {
            $keys = $this->explode($path);

            foreach ($keys as $key) {
                if (isset($array[$key])) {
                    $array = $array[$key];
                } else {
                    return $default;
                }
            }
        }

        return $array;
    }

    /**
     * Sets a value for path.
     *
     * @param string $path  Path to set value for.
     * @param mixed  $value Value to set 
     *
     * @author Anton Medvedev <anton (at) elfet (dot) ru>
     */
    public function set($path, $value)
    {
        if (!empty($path)) {
            $at   = &$this->getValues();
            $keys = $this->explode($path);

            while (count($keys) > 0) {
                if (count($keys) === 1) {
                    if (is_array($at)) {
                        $at[array_shift($keys)] = $value;
                    } else {
                        throw new \RuntimeException("Can not set value at this path ($path) because is not array.");
                    }
                } else {
                    $key = array_shift($keys);
                    if (!isset($at[$key])) {
                        $at[$key] = [];
                    }
                    $at = &$at[$key];
                }
            }
        } else {
            $this->values = $value;
        }
    }

    /**
     * Add a value to a path.
     *
     * @param string $path   Path to add value to.
     * @param array  $values Values to add.
     *
     * @author Anton Medvedev <anton (at) elfet (dot) ru>
     */
    public function add($path, array $values)
    {
        $get = (array) $this->get($path);
        $this->set($path, $this->arrayMergeRecursiveDistinct($get, $values));
    }

    /**
     * Returns whether a path exists.
     *
     * @param string $path Path to check existence.
     *
     * @author Anton Medvedev <anton (at) elfet (dot) ru>
     *
     * @return bool TRUE if path exists, otherwise FALSE
     */
    public function have($path)
    {
        $keys  = $this->explode($path);
        $array = $this->values;
        foreach ($keys as $key) {
            if (isset($array[$key])) {
                $array = $array[$key];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Setter for values.
     *
     * @param array $values Values to set.
     *
     * @author Anton Medvedev <anton (at) elfet (dot) ru>
     */
    public function setValues(array $values)
    {
        $this->values = $values;
    }

    /**
     * Setter for values.
     *
     * @param array $values Values to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function values(array $values)
    {
        $this->setValues($values);

        return $this;
    }

    /**
     * Getter for values.
     *
     * @author Anton Medvedev <anton (at) elfet (dot) ru>
     *
     * @return array Values
     */
    public function getValues()
    {
        return $this->values;
    }
    
    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Splits path by separator.
     *
     * @param string $path Path to split.
     *
     * @author Anton Medvedev <anton (at) elfet (dot) ru>
     *
     * @return array Splitted path
     */
    protected function explode($path)
    {
        return preg_split(self::SEPARATOR, $path);
    }

    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):.
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * arrayMergeRecursiveDistinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * arrayMergeRecursiveDistinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * If key is integer, it will be merged like array_merge do:
     * arrayMergeRecursiveDistinct(array(0 => 'org value'), array(0 => 'new value'));
     *     => array(0 => 'org value', 1 => 'new value');
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     *
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     * @author Anton Medvedev <anton (at) elfet (dot) ru>
     */
    protected function arrayMergeRecursiveDistinct(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                if (is_int($key)) {
                    $merged[] = $this->arrayMergeRecursiveDistinct($merged[$key], $value);
                } else {
                    $merged[$key] = $this->arrayMergeRecursiveDistinct($merged[$key], $value);
                }
            } else {
                if (is_int($key)) {
                    $merged[] = $value;
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }
}
