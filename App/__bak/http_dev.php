<?php

/**
 * include DoozR bootstrapper
 */
require_once '../Framework/Core/DoozR.bootstrap.php';

/**
 * instanciate DoozR
 */
$DoozR = DoozR_Core::getInstance();

/**
 * get module http
 */
$http = DoozR_Core::module('http');


// the url to use for request
$url = 'http://ec.europa.eu/taxation_customs/vies/viesquer.do';

// the parameter set 1 = Google Ireland
/*
$parameter = array(
    'ms'           => 'IE',        // config for vat to check
    'iso'          => 'IE',
    'vat'          => '6388047V',
    'requesterMs'  => 'DE',        // config for requesting company (us)
    'requesterIso' => 'DE',
    'requesterVat' => '812489284'
);
*/

// the parameter set 2 = Red Bull GmbH
$parameter = array(
    'ms'           => 'AT',        // config for vat to check
    'iso'          => 'AT',
    'vat'          => 'U33864707',
    'requesterMs'  => 'DE',        // config for requesting company (us)
    'requesterIso' => 'DE',
    'requesterVat' => '812489284'
);

// the method
$method = 'POST';

// becomes => http://ec.europa.eu/taxation_customs/vies/viesquer.do?ms=IE&iso=IE&vat=6388047V&requesterMs=DE&requesterIso=DE&requesterVat=126229693

/**
 * TESTING A POST-REQUEST
 */
$http->setUrl($url);

$http->setMethod($method);

$http->setParameter($parameter);

$result = $http->send();

/*
$result = '
Yes, valid VAT number
        </td>
    </tr>

</tbody></table>


<br>

<table style="padding-left: 5px;" align="center" bgcolor="#ffff00" border="0" width="85%">
    <tbody><tr>
        <td width="30%">
            <font face="Verdana" size="2">
            VAT number
            </font>
        </td>

        <td width="50%">
            <font face="Verdana" size="2">
            IE 6388047V
            </font>
        </td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>

            <font face="Verdana" size="2">
            Member State
            </font>
        </td>
        <td>
            <font face="Verdana" size="2">
            IE
            </font>
        </td>
        <td>&nbsp;</td>

    </tr>
    <!-- NAME -->
    <tr>
        <td colspan="3"><hr width="100%"></td>
    </tr>

    <tr>
        <td valign="top">
            <font face="Verdana" size="2">
            Name
            </font>

        </td>
        <td>
            <font face="Verdana" size="2">
            GOOGLE IRELAND LIMITED
            </font>
        </td>
        <td>&nbsp;</td>
    </tr>

    <!-- ADDRESS -->

    <tr>
        <td colspan="3"><hr width="100%"></td>
    </tr>

    <tr>
        <td valign="top">
            <font face="Verdana" size="2">
            Address
            </font>
        </td>

        <td>
            <font face="Verdana" size="2">
            1ST &amp; 2ND FLOOR ,GORDON HOUSE ,BARROW STREET ,DUBLIN 4
            </font>
        </td>
        <td>&nbsp;</td>
    </tr>

    <!-- REQUESTER ID AND RECEIVED DATE -->

    <tr>
        <td colspan="3"><hr width="100%"></td>
    </tr>
    <tr>
        <td>
            <font face="Verdana" size="2">
            Consultation Number
            </font>
        </td>

        <td>
            <font face="Verdana" size="2">
            WAPIAAAASwRTSRvB
            </font>
        </td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>

            <font face="Verdana" size="2">
            Date when request received
            </font>
        </td>
        <td>
            <font face="Verdana" size="2">
            03/11/2010 (dd/mm/yyyy)
            </font>
        </td>
        <td>&nbsp;</td>

    </tr>
</tbody></table>



                                                                    </td>

                                                                </tr>
                                                                <tr>
                                                                    <td height="20"></td>
                                                                </tr>

                                                                <tr>
                                                                    <td colspan="5">
                                                                    </td>
                                                                </tr>
                                                            </tbody></table>
                                                        </td>
                                                    </tr>
                                                </tbody></table>
                                            </td>

                                        </tr>
                                    </tbody></table>
                                </td>
                            </tr>
                        </tbody></table>
                    </td>
                    <td width="10">
                    </td>
                    <!-- MENU RIGHT -->

                    <td class="noprint" valign="top">
                        &nbsp;
                    </td>
                    <td width="10">
                    </td>
                </tr>
                <tr id="idClearPixel">
                    <td class="noprint" height="1" width="10"><img src="images/icons/clearpixel.gif" class="noprint" height="1" width="10"></td>
                    <td class="noprint" height="1" valign="top" width="190"><img src="images/icons/clearpixel.gif" class="noprint" height="1" width="190"></td>

                    <td class="noprint" height="1" width="10"><img src="images/icons/clearpixel.gif" class="noprint" height="1" width="10"></td>
                    <td height="1" valign="top"></td>
                    <td class="noprint" height="1" width="10"><img src="images/icons/clearpixel.gif" class="noprint" height="1" width="10"></td>
                    <td class="noprint" height="1" valign="top" width="170"><img src="images/icons/clearpixel.gif" class="noprint" height="10" width="170"></td>
                    <td class="noprint" height="1" width="10"><img src="images/icons/clearpixel.gif" class="noprint" height="1" width="10"></td>
                </tr>
                </tbody>
            </table>

        </td>
    </tr>
    <tr>
        <td height="25">
            <!-- Footer table -->
            <table id="footer" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tbody><tr class="ftr">
                    <td class="ftr" height="24" width="10"><img src="images/icons/clearpixel.gif" height="24" width="10"></td>
                    <td class="ftr" align="center" width="190"> </td>

                    <td class="ftr" height="24" width="10"><img src="images/icons/clearpixel.gif" height="24" width="10"></td>
                    <td class="ftr" align="center" height="24"><font class="laResistance">385</font>';
*/




pred(getVatStatus($result));





function getVatStatus($html)
{
    // assume wrong/invalid VAT
    $status = false;

    // additional data
    $memberstate    = null;
    $name           = null;
    $address        = null;
    $consultationnr = null;
    $requestdate    = null;

    // check for validity
    if (stristr($html, 'Yes, valid VAT number')) {
        $status = true;
    }

    // on success parse additional data
    if ($status) {
        // parse addtitional data
        $result = null;

        // remove new lines
        $html = str_replace("\n", ' ', $html);


        // GET MEMBERSTATE
        $pattern = '/Member State(.*?)<!-- NAME -->/i';
        preg_match($pattern, $html, $result);

        if (isset($result[0])) {
            $memberstate = clean($result[0], 'Member State');
        }


        // GET NAME
        $pattern = '/<!-- NAME -->(.*?)<!-- ADDRESS -->/i';
        preg_match($pattern, $html, $result);

        if (isset($result[0])) {
            $name = clean($result[0], 'name');
        }


        // GET ADDRESS
        $pattern = '/<!-- ADDRESS -->(.*?)<!-- REQUESTER ID AND RECEIVED DATE -->/i';
        preg_match($pattern, $html, $result);

        if (isset($result[0])) {
            $address = clean($result[0], 'address');
        }


        // GET CONSULTATIONUMBER
        $pattern = '/<!-- REQUESTER ID AND RECEIVED DATE -->(.*?)Date when request received/i';
        preg_match($pattern, $html, $result);

        if (isset($result[0])) {
            $consultationnr = clean($result[0], array('Consultation Number', 'Date when request received'));
        }


        // GET REQUESTDATE
        $pattern = '/Date when request received(.*?)\(dd\/mm\/yyyy\)/i';
        preg_match($pattern, $html, $result);

        if (isset($result[0])) {
            $requestdate = clean($result[0], array('Date when request received', '(dd/mm/yyyy)'));
            $requestdateParts = explode('/', $requestdate);
            $requestdate = new DateTime();
            $requestdate->setDate($requestdateParts[2], $requestdateParts[1], $requestdateParts[0]);
        }
    }

    // return combined data
    return array(
        'valid'          => $status,
        'memberstate'    => $memberstate,
        'name'           => $name,
        'address'        => $address,
        'consultationnr' => $consultationnr,
        'requestdate'    => $requestdate          // retrieve this by e.g. pred($requestdate->format('Y-m-d'));
    );
}


function clean($dirty, $remove = 'name')
{
    $cleaned = strip_tags($dirty);
    $cleaned = str_ireplace('&nbsp;', '', $cleaned);
    if (is_array($remove)) {
        foreach ($remove as $removal) {
            $cleaned = str_ireplace($removal, '', $cleaned);
        }
    } else {
        $cleaned = str_ireplace($remove, '', $cleaned);
    }
    $cleaned = trim($cleaned);
    return $cleaned;
}

?>