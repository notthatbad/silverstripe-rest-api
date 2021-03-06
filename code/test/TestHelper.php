<?php

namespace Ntb\RestAPI;

/**
 * Test Helper class provides some helpful functions for tests.
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class TestHelper {

    /**
     * @param callable $callback
     * @param string $expectedException
     * @param int $expectedCode
     * @param string $expectedMessage
     * @author VladaHejda
     */
    public static function assertException(callable $callback, $expectedException = 'Exception', $expectedCode = null, $expectedMessage = null) {
        $self = new \SapphireTest;
        if (!\ClassInfo::exists($expectedException)) {
            $self->fail(sprintf('An exception of type "%s" does not exist.', $expectedException));
        }
        try {
            $callback();
        } catch (\Exception $e) {
            $class = \ClassInfo::class_name($e);
            $message = $e->getMessage();
            $code = $e->getCode();
            $errorMessage = 'Failed asserting the class of exception';
            if ($message && $code) {
                $errorMessage .= sprintf(' (message was %s, code was %d)', $message, $code);
            } else if ($code) {
                $errorMessage .= sprintf(' (code was %d)', $code);
            }
            $errorMessage .= '.';
            $self->assertInstanceOf($expectedException, $e, $errorMessage);
            if ($expectedCode !== null) {
                $self->assertEquals($expectedCode, $code, sprintf('Failed asserting code of thrown %s.', $class));
            }
            if ($expectedMessage !== null) {
                $self->assertContains($expectedMessage, $message, sprintf('Failed asserting the message of thrown %s.', $class));
            }
            return;
        }
        $errorMessage = 'Failed asserting that exception';
        if (strtolower($expectedException) !== 'exception') {
            $errorMessage .= sprintf(' of type %s', $expectedException);
        }
        $errorMessage .= ' was thrown.';
        $self->fail($errorMessage);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object|string &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     *
     * @author Juan Treminio
     * @author Christian Blank
     */
    public static function invoke(&$object, $methodName, array $parameters = []) {
        if(is_object($object)) {
            $className = get_class($object);
        } else {
            $className = $object;
        }
        $reflection = new \ReflectionClass($className);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
