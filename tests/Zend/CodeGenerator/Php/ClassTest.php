<?php

use PHPUnit\Framework\TestCase;

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/**
 * @see Zend_CodeGenerator_Php_Class
 */
require_once 'Zend/CodeGenerator/Php/Class.php';

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_CodeGenerator
 * @group Zend_CodeGenerator_Php
 */
class Zend_CodeGenerator_Php_ClassTest extends TestCase
{
    public function testConstruction()
    {
        $class = new Zend_CodeGenerator_Php_Class();
        $this->assertInstanceOf('Zend_CodeGenerator_Php_Class', $class);
    }

    public function testNameAccessors()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setName('TestClass');
        $this->assertEquals($codeGenClass->getName(), 'TestClass');
    }

    public function testAbstractAccessors()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $this->assertFalse($codeGenClass->isAbstract());
        $codeGenClass->setAbstract(true);
        $this->assertTrue($codeGenClass->isAbstract());
    }

    public function testExtendedClassAccessors()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setExtendedClass('ExtendedClass');
        $this->assertEquals($codeGenClass->getExtendedClass(), 'ExtendedClass');
    }

    public function testImplementedInterfacesAccessors()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setImplementedInterfaces(['Class1', 'Class2']);
        $this->assertEquals($codeGenClass->getImplementedInterfaces(), ['Class1', 'Class2']);
    }

    public function testPropertyAccessors()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setProperties([
            ['name' => 'propOne'],
            new Zend_CodeGenerator_Php_Property(['name' => 'propTwo'])
            ]);

        $properties = $codeGenClass->getProperties();
        $this->assertEquals(count($properties), 2);
        $this->isInstanceOf(current($properties), 'Zend_CodeGenerator_Php_Property');

        $property = $codeGenClass->getProperty('propTwo');
        $this->isInstanceOf($property, 'Zend_CodeGenerator_Php_Property');
        $this->assertEquals($property->getName(), 'propTwo');

        // add a new property
        $codeGenClass->setProperty(['name' => 'prop3']);
        $this->assertEquals(count($codeGenClass->getProperties()), 3);
    }

    public function testSetProperty_AlreadyExists_ThrowsException()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setProperty(['name' => 'prop3']);

        $this->expectException("Zend_CodeGenerator_Php_Exception");

        $codeGenClass->setProperty(['name' => 'prop3']);
    }

    public function testSetProperty_NoArrayOrProperty_ThrowsException()
    {
        $this->expectException("Zend_CodeGenerator_Php_Exception");

        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setProperty("propertyName");
    }

    public function testMethodAccessors()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setMethods([
            ['name' => 'methodOne'],
            new Zend_CodeGenerator_Php_Method(['name' => 'methodTwo'])
            ]);

        $methods = $codeGenClass->getMethods();
        $this->assertEquals(count($methods), 2);
        $this->isInstanceOf(current($methods), 'Zend_CodeGenerator_Php_Method');

        $method = $codeGenClass->getMethod('methodOne');
        $this->isInstanceOf($method, 'Zend_CodeGenerator_Php_Method');
        $this->assertEquals($method->getName(), 'methodOne');

        // add a new property
        $codeGenClass->setMethod(['name' => 'methodThree']);
        $this->assertEquals(count($codeGenClass->getMethods()), 3);
    }

    public function testSetMethod_NoMethodOrArray_ThrowsException()
    {
        $this->expectException(
            "Zend_CodeGenerator_Php_Exception"
        );
        $this->expectExceptionMessage('setMethod() expects either an array of method options or an instance of Zend_CodeGenerator_Php_Method');

        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setMethod("aMethodName");
    }

    public function testSetMethod_NameAlreadyExists_ThrowsException()
    {
        $methodA = new Zend_CodeGenerator_Php_Method();
        $methodA->setName("foo");
        $methodB = new Zend_CodeGenerator_Php_Method();
        $methodB->setName("foo");

        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setMethod($methodA);

        $this->expectException("Zend_CodeGenerator_Php_Exception");
        $this->expectExceptionMessage('A method by name foo already exists in this class.');

        $codeGenClass->setMethod($methodB);
    }

    /**
     * @group ZF-7361
     */
    public function testHasMethod()
    {
        $method = new Zend_CodeGenerator_Php_Method();
        $method->setName('methodOne');

        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setMethod($method);

        $this->assertTrue($codeGenClass->hasMethod('methodOne'));
    }

    /**
     * @group ZF-7361
     */
    public function testHasProperty()
    {
        $property = new Zend_CodeGenerator_Php_Property();
        $property->setName('propertyOne');

        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setProperty($property);

        $this->assertTrue($codeGenClass->hasProperty('propertyOne'));
    }

    public function testToString()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class([
            'abstract' => true,
            'name' => 'SampleClass',
            'extendedClass' => 'ExtendedClassName',
            'implementedInterfaces' => ['Iterator', 'Traversable'],
            'properties' => [
                ['name' => 'foo'],
                ['name' => 'bar']
                ],
            'methods' => [
                ['name' => 'baz']
                ],
            ]);

        $expectedOutput = <<<EOS
abstract class SampleClass extends ExtendedClassName implements Iterator, Traversable
{

    public \$foo = null;

    public \$bar = null;

    public function baz()
    {
    }


}

EOS;

        $output = $codeGenClass->generate();
        $this->assertEquals($expectedOutput, $output, $output);
    }

    /**
     * @group ZF-7909 */
    public function testClassFromReflectionThatImplementsInterfaces()
    {
        if (!class_exists('Zend_CodeGenerator_Php_ClassWithInterface')) {
            require_once dirname(__FILE__) . "/_files/ClassAndInterfaces.php";
        }

        require_once "Zend/Reflection/Class.php";
        $reflClass = new Zend_Reflection_Class('Zend_CodeGenerator_Php_ClassWithInterface');

        $codeGen = Zend_CodeGenerator_Php_Class::fromReflection($reflClass);
        $codeGen->setSourceDirty(true);

        $code = $codeGen->generate();
        $expectedClassDef = 'class Zend_CodeGenerator_Php_ClassWithInterface implements Zend_Code_Generator_Php_OneInterface, Zend_Code_Generator_Php_TwoInterface';
        $this->assertStringContainsString($expectedClassDef, $code);
    }

    /**
     * @group ZF-7909
     */
    public function testClassFromReflectionDiscardParentImplementedInterfaces()
    {
        if (!class_exists('Zend_CodeGenerator_Php_ClassWithInterface')) {
            require_once dirname(__FILE__) . "/_files/ClassAndInterfaces.php";
        }

        require_once "Zend/Reflection/Class.php";
        $reflClass = new Zend_Reflection_Class('Zend_CodeGenerator_Php_NewClassWithInterface');

        $codeGen = Zend_CodeGenerator_Php_Class::fromReflection($reflClass);
        $codeGen->setSourceDirty(true);

        $code = $codeGen->generate();

        $expectedClassDef = 'class Zend_CodeGenerator_Php_NewClassWithInterface extends Zend_CodeGenerator_Php_ClassWithInterface implements Zend_Code_Generator_Php_ThreeInterface';
        $this->assertStringContainsString($expectedClassDef, $code);
    }

    /**
     * @group ZF-9602
     */
    public function testSetextendedclassShouldIgnoreEmptyClassnameOnGenerate()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setName('MyClass')
                     ->setExtendedClass('');

        $expected = <<<CODE
class MyClass
{


}

CODE;
        $this->assertEquals($expected, $codeGenClass->generate());
    }

    /**
     * @group ZF-9602
     */
    public function testSetextendedclassShouldNotIgnoreNonEmptyClassnameOnGenerate()
    {
        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setName('MyClass')
                     ->setExtendedClass('ParentClass');

        $expected = <<<CODE
class MyClass extends ParentClass
{


}

CODE;
        $this->assertEquals($expected, $codeGenClass->generate());
    }

    /**
     * @group ZF-11513
     */
    public function testAllowsClassConstantToHaveSameNameAsClassProperty()
    {
        $const = new Zend_CodeGenerator_Php_Property();
        $const->setName('name')->setDefaultValue('constant')->setConst(true);

        $property = new Zend_CodeGenerator_Php_Property();
        $property->setName('name')->setDefaultValue('property');

        $codeGenClass = new Zend_CodeGenerator_Php_Class();
        $codeGenClass->setName('My_Class')->setProperties([$const, $property]);

        $expected = <<<CODE
class My_Class
{

    const name = 'constant';

    public \$name = 'property';


}

CODE;
        $this->assertEquals($expected, $codeGenClass->generate());
    }
}
