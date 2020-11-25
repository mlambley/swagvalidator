<?php
namespace Mlambley\Swagvalidator\Validator;

use Mlambley\Swagvalidator\Exception;
use Mlambley\Swagvalidator\ValidatorInterface;

class BooleanValidator implements ValidatorInterface
{
    public function validate($schema, $data, $context = '')
    {
        //Check that the data is a boolean.
        if (!is_bool($data)) {
            throw new Exception\ValidationException(sprintf('%1$s is not a boolean.', $context));
        }
    }
}
