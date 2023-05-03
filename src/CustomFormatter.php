<?php

namespace gplaza\rututil;

use yii\i18n\Formatter;
use gplaza\rututil\RutBehavior;

class CustomFormatter extends Formatter
{
    public function asRut($value)
    {
        return RutBehavior::format($value);
    }
}
