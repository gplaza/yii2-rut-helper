<?php

namespace gplaza\rututil;

use yii\base\Behavior;
use yii\validators\Validator;
use gplaza\rututil\RutValidator;

class RutBehavior extends Behavior
{
    public $attributeName;
    public $validatorParams = [];

    public function attach($owner)
    {
        parent::attach($owner);
        $owner->validators[] = Validator::createValidator(RutValidator::class, $owner, $this->attributeName, $this->validatorParams);
    }

    public static function validateRUT($rut)
    {

        if (!preg_match("/^[0-9.]+[-]?+[0-9kK]{1}/", $rut)) {
            return false;
        } else {

            $rutClean = preg_replace('/[\.\-]/i', '', $rut);
            $dv = substr($rutClean, -1);
            $numero = substr($rutClean, 0, strlen($rutClean) - 1);
            $i = 2;
            $suma = 0;

            foreach (array_reverse(str_split($numero)) as $v) {
                if ($i == 8) {
                    $i = 2;
                }

                $suma += $v * $i;
                ++$i;
            }

            $dvr = 11 - ($suma % 11);

            if ($dvr == 11) {
                $dvr = 0;
            }

            if ($dvr == 10) {
                $dvr = 'K';
            }

            return ($dvr == strtoupper($dv));
        }
    }

    public function getFormatRut()
    {
        $rut = $this->owner->attributes[$this->attributeName];
        return RutBehavior::format($rut);
    }

    public static function format($string)
    {
        $string = empty($string) ? "" : $string;
        $string = str_replace(['.', ' ', '-'], '', $string);

        // Primero vamos si es menos o mayor a 9 millones
        if (($largo = strlen($string)) == 8) {
            $string = substr($string, 0, 1) . '.' . substr($string, 1, 3) . '.' . substr($string, 4, 3) . '-' . substr($string, 7, 1);
        } else {
            $string = substr($string, 0, 2) . '.' . substr($string, 2, 3) . '.' . substr($string, 5, 3) . '-' . substr($string, 8, 1);
        }

        return strtoupper($string);
    }

    public static function formatSii($string)
    {
        $string = RutBehavior::format($string);
        return str_replace('.', '', $string);
    }
}
