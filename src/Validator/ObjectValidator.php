<?php
namespace Mlambley\Swagvalidator\Validator;

use Mlambley\Swagvalidator\Exception;
use Mlambley\Swagvalidator\ValidatorInterface;

class ObjectValidator implements ValidatorInterface
{
    public function validate($schema, $data, $context = '')
    {
        //Check that the data is a json object.
        if (!is_object($data)) {
            throw new Exception\ValidationException(sprintf('%1$s is not an object.', $context));
        }

        if (isset($schema->maxProperties)) {
            $this->validateMaxProperties($schema, $data, $context);
        }

        if (isset($schema->minProperties)) {
            $this->validateMinProperties($schema, $data, $context);
        }

        if (isset($schema->required)) {
            $this->validateRequired($schema, $data, $context);
        }

        if (isset($schema->properties)) {
            $this->validateProperties($schema, $data, $context);
        }

        $this->validateAdditionalProperties($schema, $data, $context);
    }

    protected function validateMaxProperties($schema, $data, $context)
    {
        //An object instance is valid against "maxProperties" if its number of properties is less than, or equal to, the value of this keyword.
        if (count(array_keys(get_object_vars($data))) > $schema->maxProperties) {
            throw new Exception\ValidationException(sprintf('%1$s has too many properties.', $context));
        }
    }

    protected function validateMinProperties($schema, $data, $context)
    {
        //An object instance is valid against "minProperties" if its number of properties is greater than, or equal to, the value of this keyword.
        if (count(array_keys(get_object_vars($data))) < $schema->minProperties) {
            throw new Exception\ValidationException(sprintf('%1$s has too few properties.', $context));
        }
    }

    protected function validateRequired($schema, $data, $context)
    {
        //Check keys against required properties.
        $missingFields = array();
        foreach ($schema->required as $required) {
            if (!array_key_exists($required, get_object_vars($data))) {
                $missingFields[] = $required;
            }
        }

        if (!empty($missingFields)) {
            throw new Exception\ValidationException(sprintf('%1$s has missing required fields: "%2$s".', $context, implode('", "', $missingFields)));
        }
    }

    protected function validateProperties($schema, $data, $context)
    {
        //Validate each property in json against the schema specified in properties.
        foreach (get_object_vars($data) as $field => $val) {
            //If it's not set then it's an additional property. See validateAdditionalProperties.
            if (isset($schema->properties->$field)) {
                (new Validator())
                    ->validate($schema->properties->$field, $val, $context . '/' . $field);
            }
        }
    }

    protected function validateAdditionalProperties($schema, $data, $context)
    {
        if (!isset($schema->additionalProperties) || $schema->additionalProperties === true) {
            //If true, we allow all additional properties.
            //This is also the default behaviour for JSON schema and is unchanged by Swagger 2.0.
            //See "By default any additional properties are allowed." in https://json-schema.org/understanding-json-schema/reference/object.html
        } elseif ($schema->additionalProperties === false) {
            //We don't allow additional properties. Check whether there are extra fields we weren't expecting.
            if (isset($schema->properties)) {
                $extraFields = array_diff(array_keys(get_object_vars($data)), array_keys(get_object_vars($schema->properties)));
            } else {
                $extraFields = array_keys(get_object_vars($data));
            }
            if (!empty($extraFields)) {
                throw new Exception\ValidationException(sprintf('%1$s has unexpected extra fields: "%2$s".', $context, implode('", "', $extraFields)));
            }
        } elseif (is_object($schema->additionalProperties)) {
            //If it's an empty object, we also allow all additional properties
            if (!empty(get_object_vars($schema->additionalProperties))) {
                //Fetch additional properties - ones not specified in properties.
                if (isset($schema->properties) && is_object($schema->properties)) {
                    $extraProperties = array_diff_key(get_object_vars($data), get_object_vars($schema->properties));
                } else {
                    $extraProperties = $data;
                }

                //Validate all additional properties against the specified schema.
                foreach ($extraProperties as $field => $val) {
                    (new Validator())
                        ->validate($schema->additionalProperties, $val, $context . '/' . $field);
                }
            }
        }
    }
}
