<?php
namespace Mlambley\Swagvalidator;

interface ValidatorInterface
{
    /**
     * @param \stdClass $schema
     * @param mixed $data Could be \stdClass, array, string, int, etc.
     * @param string $context
     * @return mixed
     */
    public function validate($schema, $data, $context = '');
}
