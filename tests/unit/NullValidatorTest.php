<?php
use AspectMock\Test as test;
use Mlambley\Swagvalidator\Exception;
use Mlambley\Swagvalidator\Validator;

class NullValidatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected $schema;
    
    protected function _before()
    {
        $this->schema = new \stdClass();
    }

    protected function _after()
    {
        test::clean();
    }

    public function testValidateNull()
    {
        $object = new Validator\NullValidator();
        $object->validate($this->schema, null, 'context');
    }

    public function testValidateNotNull()
    {
        $this->expectException(Exception\ValidationException::class);
        $this->expectExceptionMessage('context is not null.');
        
        $object = new Validator\NullValidator();
        $object->validate($this->schema, new \stdClass(), 'context');
    }
}
