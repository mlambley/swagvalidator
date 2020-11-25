<?php
namespace Mlambley\Swagvalidator\Validator;

use Mlambley\Swagvalidator\Exception;

abstract class NumberBase
{
    protected function validateNumericFields($schema, $data, $context)
    {
        if (isset($schema->multipleOf)) {
            $this->validateMultipleOf($schema, $data, $context);
        }

        if (isset($schema->maximum)) {
            $this->validateMaximum($schema, $data, $context);
        }

        if (isset($schema->minimum)) {
            $this->validateMinimum($schema, $data, $context);
        }
    }

    protected function validateMultipleOf($schema, $data, $context)
    {
        //The value of "multipleOf" MUST be a JSON number.  This number MUST be strictly greater than 0.
        //A numeric instance is valid against "multipleOf" if the result of the division of the instance by this keyword's value is an integer.
        if (!$this->isNumeric($schema->multipleOf) || !$schema->multipleOf > 0) {
            throw new Exception\ValidationException(sprintf('%1$s has an invalid multipleOf parameter. Must be greater than 0.', $context));
        } elseif (((int)($data / $schema->multipleOf)) != $data / $schema->multipleOf) {
            throw new Exception\ValidationException(sprintf('%1$s is not a multiple of %2$s.', $context, $schema->multipleOf));
        }
    }

    protected function validateMaximum($schema, $data, $context)
    {
        //The value of "maximum" MUST be a JSON number.  The value of "exclusiveMaximum" MUST be a boolean. If "exclusiveMaximum" is present, "maximum" MUST also be present.
        //Successful validation depends on the presence and value of "exclusiveMaximum":
        //if "exclusiveMaximum" is not present, or has boolean value false, then the instance is valid if it is lower than, or equal to, the value of "maximum";
        //if "exclusiveMaximum" has boolean value true, the instance is valid if it is strictly lower than the value of "maximum".

        $excl = false;
        if (isset($schema->exclusiveMaximum) && $schema->exclusiveMaximum === true) {
            $excl = true;
        }

        if ($excl) {
            //If exclusive max is true, then fail if it's equal to or greater.
            if ($data >= $schema->maximum) {
                throw new Exception\ValidationException(sprintf('%1$s must be less than %2$s.', $context, $schema->maximum));
            }
        } else {
            //If exclusive max is false, then fail if it's greater.
            if ($data > $schema->maximum) {
                throw new Exception\ValidationException(sprintf('%1$s must be less than or equal to %2$s.', $context, $schema->maximum));
            }
        }
    }

    protected function validateMinimum($schema, $data, $context)
    {
        //The value of "minimum" MUST be a JSON number.  The value of "exclusiveMinimum" MUST be a boolean. If "exclusiveMinimum" is present, "minimum" MUST also be present.
        //Successful validation depends on the presence and value of "exclusiveMinimum":
        //if "exclusiveMinimum" is not present, or has boolean value false, then the instance is valid if it is greater than, or equal to, the value of "minimum";
        //if "exclusiveMinimum" is present and has boolean value true, the instance is valid if it is strictly greater than the value of "minimum".

        $excl = false;
        if (isset($schema->exclusiveMinimum) && $schema->exclusiveMinimum === true) {
            $excl = true;
        }

        if ($excl) {
            //If exclusive min is true, then fail if it's equal to or less than.
            if ($data <= $schema->minimum) {
                throw new Exception\ValidationException(sprintf('%1$s must be greater than %2$s.', $context, $schema->minimum));
            }
        } else {
            //If exclusive min is false, then fail if it's less than.
            if ($data < $schema->minimum) {
                throw new Exception\ValidationException(sprintf('%1$s must be greater than or equal to %2$s.', $context, $schema->minimum));
            }
        }
    }

    protected function isNumeric($val)
    {
        //Can't just use is_numeric because that matches strings.
        return is_int($val) || is_float($val);
    }
}
