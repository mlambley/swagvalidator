<?php
namespace Mlambley\Swagvalidator\Validator;

use Mlambley\Swagvalidator\Exception;
use Mlambley\Swagvalidator\ValidatorInterface;

class NullValidator implements ValidatorInterface
{
    public function validate($schema, $data, $context = '')
    {
        //Check that the data is null.
        if ($data !== null) {
            throw new Exception\ValidationException(sprintf('%1$s is not null.', $context));
        }
    }
}
