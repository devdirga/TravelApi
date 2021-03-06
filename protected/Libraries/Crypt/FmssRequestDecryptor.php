<?php

namespace Travel\Libraries\Crypt;

use Travel\Libraries\Crypt\CastleCrypt;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



/**
 * Description of FmssRequestDecryptor
 *
 * @author Wahyu Hidayat
 */
class FmssRequestDecryptor
{

    private static $rsaPrivateKey = '-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEAw3kgCeFI5Vp6z5tzGhbBpW5faguhrOrP9Wlth2gDU1b/nFXg
ImpJVbnhyrzQU1oT8NQ6NOqfPE7aD9dd/tVIXHAL54kjCBc5QGik9Vskpl0kp9Ri
5IOx9/XDabtKyL2T20p11vilEcfGKy0jsUHYO+tZ7DRguxLyhe7tzND3fhAZoLli
v2K8ntAq+cnUU4Xa3SNuGRSTmvwYWMKZ8ZxgSbPSw50qejrQt1Qm83td03tdDDML
QQERUa3jM61eZHl9BWkyALPC/SEUmKd8g4OVmkJ8kLDBwTC36Lk6h5gJAbSGDqnM
jsqxf9oTcAo6eG/ArH1pBASaeM1MYShE+/7qPQIDAQABAoIBAEOKtdRUILEHO4pC
x1nLZSv5YESvMjUiiardu/feq89/LyFg1uptWVuit+8qoL08UcmUO2yPaWgYQubY
XuUv4yn1mVdunkksZR7pSZ5x2M8XPhQzodwd2T+5jsUY7zU/tDhAmUknbzoekk3W
8g9Mlh/WKaMhUg8x89TtgwOTbQEEio7zdD6SyJetcdXiYVz3gtaLlp3TISSH9RBp
OsFXRwTgRuhTkWM9mkWMneDMCtrccnBcKq1Oy+MELCtVaAUlH7Mq+3dAhfFNGoZn
n7XwniNayT0av3iT4jtO5GI9a6NeRQTE5PMKBDaqxQjEU1sb21sU4PMLfQrhSr8Q
UZrbY00CgYEA3+f2/KFdog7HAzm1ndAbOHGba94w1a7Ngpdf0H+di5BuQVRR3H/e
84UmQiLIh+ATg2NZSyFCGtl6HQjakJxq0LyDn/+9MJ310wB8vv7nj1m7U4GayZdb
vww+L4ImsradAyEhXFeW53J9IUqccoGgJPIa2VkbwK9CNXt5W8YNmdsCgYEA333W
hPq5xBA+ln1X8wH6AXW8HHyYwbastRDeE5Z8w4QRQz45tMDRH82LSk5gVhrBM3IJ
l65Re9MU9GvaqwYepgqH9VlJ5e2tpjDwpJtc0VWCwY1ic7YTqPp+alfHWKiP3t7Q
n5tL5locxW3d4RzWnLNz0s70vhYHVbQNzAT4Q8cCgYEAtPUeMhKgP+c9hCfR1ldo
iHeYqtcKFMiPoyl/8FwTEcaKtRXWiyR1Jc7Ims7NzrKUUq2qbwPUDysQfAK50gH4
efbBUkA3wOEI2Z0dNKeCseJNeTHXzXEcXw0f/PltGSZpQyw1cBICDdADvTI1un10
1ics99gCi/mYwuylqjwopd0CgYEAve6jsL+jwAxOJHbBl7PGVBdKlqsM5xPoErkT
AnKR9Vb3lL39LK/xCaYVCkExffue1anEnTN37FOnK1G9tDqvMU0h3lDTjKRBP0u4
NywR5ZVWWkddtBi4/JJlfNq8f4xBOJcDlaDVEB7k9KQ6PGXVvbaEaFOZizINabhr
QUe39WECgYBs1yP3v7A68NUL48/7YzA5C82Vatly6hYwVNwjWYzzciTqAU8wddoq
vCFrn4YQrWKN+dz3z+HgPxrqaeEnzxFhOXm5YquD7QisgIaXW5ifLKb+DboGM3bz
DysghwRj6A2F9FR/BKtJRZ43JdxR9Z/ta0Q6JGoa/fMz6MpVD8g+Qg==
-----END RSA PRIVATE KEY-----';

    public static function decrypt($encryptedText)
    {
        $text2 = base64_decode($encryptedText);

        //$text3 = self::decryptRsa($text2);
        //$text4 = strrev($text3);
        $text4 = strrev($text2);
        $text5 = self::restoreNumber($text4);
        list($text6, $key, $iv) = self::unmergeText($text5);
        //        echo "Unmerged <pre>";
        //        print_r(self::unmergeText($text5));
        //        echo "</pre>";
        //        echo "text 6 ----- " .$text6." ----------  KEY ".$key." --------- IV ". $iv;
        $plainTextx = "";
        if (in_array('mcrypt', get_loaded_extensions())) {
            $plainTextx = self::decryptBlowfish($text6, $key, $iv);
        } else {
            require "class.pcrypt.php";
            $crypt = new pcrypt(MODE_CBC, 'BLOWFISH', $key, $iv);
            $plainTextx = self::removeRightPadding($crypt->decrypt(self::hex2bin($text6)));
        }

        list($timestamp, $plainText) = explode("|", $plainTextx);
        /*
	$strTimestamp = date("Y-m-d H:i:s", $timestamp);
        $expiredTime = date("Y-m-d H:i:s", strtotime($strTimestamp . " + 5 MINUTE"));
        $now = date("Y-m-d H:i:s");
        if ($now > $expiredTime) {
            $plainText = "ERROR:Expired Now ". $now ." Expired Time ". $expiredTime;
        }
*/
        return $plainText;
    }

    private static function decryptRsa($encryptedText)
    {
        $cc = new CastleCrypt();
        $cc->setPrivateKey(self::$rsaPrivateKey);
        $text = $cc->decrypt($encryptedText);

        return $text;
    }

    private static function decryptBlowfish($cipherText, $key, $iv)
    {
        $cipherText = self::hex2bin($cipherText);
        $decipherText = "";

        $cipher = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');
        if (mcrypt_generic_init($cipher, $key, $iv) != -1) {
            $decipherText = mdecrypt_generic($cipher, $cipherText);
        }
        mcrypt_generic_deinit($cipher);

        $decipherText = self::removeRightPadding($decipherText);

        return $decipherText;
    }

    private static function removeRightPadding($text)
    {
        $strlen = strlen($text);
        $i = $strlen;
        while ($i > 0) {
            $ord = ord($text[$i - 1]);
            if ($ord >= 32 && $ord <= 126) {
                break;
            }
            $i--;
        }

        $text = substr($text, 0, $i);

        return $text;
    }

    private static function hex2bin($h)
    {
        //this function is the opposite of php's bin2hex
        if (!is_string($h)) {
            return null;
        }
        $r = '';
        for ($a = 0; $a < strlen($h); $a += 2) {
            $r .= chr(hexdec($h{
            $a} . $h{
            ($a + 1)}));
        }
        return $r;
    }

    private static function restoreNumber($text)
    {
        $arrSearch = array('Z', 'Y', 'X', 'W', 'V', 'U', 'T', 'S', 'R', 'Q');
        $arrReplace = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $text2 = str_replace($arrSearch, $arrReplace, $text);

        return $text2;
    }

    private static function unmergeText($text)
    {
        $cipherText = "";
        $keyAll = "";

        for ($i = 0; $i < strlen($text); $i++) {
            if ($text[$i] >= "G" && $text[$i] <= "P") {
                $keyAll .= $text[$i];
            } else {
                $cipherText .= $text[$i];
            }
        }

        $key = substr($keyAll, 0, 16);
        $iv = substr($keyAll, 16);

        return array($cipherText, $key, $iv);
    }
}