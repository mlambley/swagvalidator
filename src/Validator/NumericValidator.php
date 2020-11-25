<?php
namespace Mlambley\Swagvalidator\Validator;

use Mlambley\Swagvalidator\Exception;
use Mlambley\Swagvalidator\ValidatorInterface;

class NumericValidator extends NumberBase implements ValidatorInterface
{
    public function validate($schema, $data, $context = '')
    {
        if (isset($schema->format)) {
            $functionMapping = [
                'float' => 'validateFloat',
                'double' => 'validateDouble'
            ];
            if (isset($functionMapping[$schema->type])) {
                $this->{$functionMapping[$schema->type]}($schema, $data, $context);
                $this->validateNumericFields($schema, $data, $context);

                return;
            }
            //The format property is an open string-valued property, and can have any value to support documentation needs.
            //Therefore, allow unknown formats and fall back to regular checking.
        }

        //Check that the data is a number.
        if (!$this->isNumeric($data)) {
            throw new Exception\ValidationException(sprintf('%1$s is not a number.', $context));
        }

        $this->validateNumericFields($schema, $data, $context);
    }

    protected function validateFloat($schema, $data, $context)
    {
        //Check that the data is a float.
        if (!is_float($data)) {
            throw new Exception\ValidationException(sprintf('%1$s is not a float.', $context));
        }
    }

    protected function validateDouble($schema, $data, $context)
    {
        //Do doubles exist in PHP? Check for a float instead.
        $this->validateFloat($schema, $data, $context);
    }
}
