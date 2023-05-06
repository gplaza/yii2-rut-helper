<?php

namespace gplaza\rututil;

use yii\validators\Validator;
use gplaza\rututil\RutBehavior;

class RutValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = 'El RUT ingresado es invalido.';
    }

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;

        if (RutBehavior::validateRUT($value) == false) {
            $model->addError($attribute, $this->message);
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        $message = json_encode($this->message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        
        return <<<JS

            var Fn = {
                // Valida el rut con su cadena completa 'XXXXXXXX-X'
                validaRut : function (rutCompleto) {
                    if (!/^[0-9]+[-|‐]{1}[0-9kK]{1}$/.test( rutCompleto ))
                        return false;
                    var tmp 	= rutCompleto.split('-');
                    var digv	= tmp[1]; 
                    var rut 	= tmp[0];
                    if ( digv == 'K' ) digv = 'k' ;
                    return (Fn.dv(rut) == digv );
                },
                dv : function(T){
                    var M=0,S=1;
                    for(;T;T=Math.floor(T/10))
                        S=(S+T%10*(9-M++%6))%11;
                    return S?S-1:'k';
                }
            }

        if(Fn.validaRut(value) == false) {
            messages.push($message);
        }
JS;
    }
}