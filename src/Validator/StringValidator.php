<?php
namespace Mlambley\Swagvalidator\Validator;

use Mlambley\Swagvalidator\Exception;
use Mlambley\Swagvalidator\ValidatorInterface;

class StringValidator implements ValidatorInterface
{
    public function validate($schema, $data, $context = '')
    {
        //Check that the data is a string.
        if (!is_string($data)) {
            throw new Exception\ValidationException(sprintf('%1$s is not a string.', $context));
        }

        if (isset($schema->format)) {
            $this->validateFormat($schema, $data, $context);
        }

        if (isset($schema->maxLength)) {
            $this->validateMaxLength($schema, $data, $context);
        }

        if (isset($schema->minLength)) {
            $this->validateMinLength($schema, $data, $context);
        }

        if (isset($schema->pattern)) {
            $this->validatePattern($schema, $data, $context);
        }
    }

    protected function validateFormat($schema, $data, $context)
    {
        //byte base64 encoded characters
        //binary any sequence of octets
        //date As defined by full-date - RFC3339
        //date-time As defined by date-time - RFC3339
        $functionMapping = [
            'byte' => 'validateByte',
            'binary' => 'validateBinary',
            'date' => 'validateDate',
            'date-time' => 'validateDateTime',
            'password' => 'validatePassword'
        ];
        if (isset($functionMapping[$schema->type])) {
            $this->{$functionMapping[$schema->type]}($schema, $data, $context);
        }
        //The format property is an open string-valued property, and can have any value to support documentation needs.
        //Therefore, allow unknown formats and fall back to regular checking.
    }

    protected function validateByte($schema, $data, $context)
    {
        if (preg_match('/^(?:[A-Za-z0-9+\\/]{4})*(?:[A-Za-z0-9+\\/]{2}==|[A-Za-z0-9+\\/]{3}=)?$/', $data) !== 1) {
            throw new Exception\ValidationException(sprintf('%1$s "%2$s" is not valid byte (Base64) data.', $context, $data));
        }
    }

    protected function validateBinary($schema, $data, $context)
    {
        //Is it even possible to have binary data in a json string? Check for byte instead
        try {
            $this->validateByte($schema, $data, $context);
        } catch (Exception\ValidationException $e) {
            throw new Exception\ValidationException(sprintf('%1$s is not binary data.', $context));
        }
    }

    protected function validateDate($schema, $data, $context)
    {
        //Eg. 2018-05-31
        $format = 'Y-m-d';
        $date = \DateTime::createFromFormat($format, $data);
        if (!$date || ($date->format($format) !== $data)) {
            throw new Exception\ValidationException(sprintf('%1$s "%2$s" is not a valid date (YYYY-MM-DD).', $context, $data));
        }
    }

    protected function validateDatetime($schema, $data, $context)
    {
        //2002-10-02T10:00:00-05:00
        //2002-10-02T15:00:00Z
        //2002-10-02T15:00:00.05Z
        $rfc3339DateTimeRegex = '/^(\d{4})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])T([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9]|60)(\.[0-9]+)?(Z|(\+|-)([01][0-9]|2[0-3]):([0-5][0-9]))$/';

        if (preg_match($rfc3339DateTimeRegex, $data) !== 1) {
            throw new Exception\ValidationException(sprintf('%1$s "%2$s" is not a valid date-time (See RFC3339).', $context, $data));
        }
    }

    protected function validatePassword($schema, $data, $context)
    {
        //There is no special format here. Just used to hint UIs the input needs to be obscured.
    }

    protected function validateMaxLength($schema, $data, $context)
    {
        //A string instance is valid against this keyword if its length is less than, or equal to, the value of this keyword.
        if (mb_strlen($data) > $schema->maxLength) {
            throw new Exception\ValidationException(sprintf('%1$s "%2$s" has too many characters (max length is %3$s).', $context, $data, $schema->maxLength));
        }
    }

    protected function validateMinLength($schema, $data, $context)
    {
        //A string instance is valid against this keyword if its length is greater than, or equal to, the value of this keyword.
        if (mb_strlen($data) < $schema->minLength) {
            throw new Exception\ValidationException(sprintf('%1$s "%2$s" has too few characters (min length is %3$s).', $context, $data, $schema->minLength));
        }
    }

    protected function validatePattern($schema, $data, $context)
    {
        //A string instance is considered valid if the regular expression matches the instance successfully.
        //Regular expressions are not implicitly anchored.

        $regex = $schema->pattern;

        //Add in the forward slashes.
        $regex = '/' . $regex . '/';

        if (preg_match($regex, $data) !== 1) {
            throw new Exception\ValidationException(sprintf('%1$s "%2$s" does not match the pattern "%3$s"', $context, $data, $schema->pattern));
        }
    }
}
