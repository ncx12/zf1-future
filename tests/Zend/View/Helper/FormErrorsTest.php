<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\TextUI\TestRunner;

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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_FormErrorsTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_FormErrorsTest::main");
}

require_once 'Zend/View/Helper/FormErrors.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_View_Helper_FormErrors
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_FormErrorsTest extends TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite = new TestSuite("Zend_View_Helper_FormErrorsTest");
        $result = (new TestRunner())->run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->view = new Zend_View();
        $this->helper = new Zend_View_Helper_FormErrors();
        $this->helper->setView($this->view);
        ob_start();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        ob_end_clean();
    }

    public function testGetElementEndReturnsDefaultValue()
    {
        $this->assertEquals('</li></ul>', $this->helper->getElementEnd());
    }

    public function testGetElementSeparatorReturnsDefaultValue()
    {
        $this->assertEquals('</li><li>', $this->helper->getElementSeparator());
    }

    public function testGetElementStartReturnsDefaultValue()
    {
        $this->assertEquals('<ul%s><li>', $this->helper->getElementStart());
    }

    public function testCanSetElementEndString()
    {
        $this->testGetElementEndReturnsDefaultValue();
        $this->helper->setElementEnd('</pre></div>');
        $this->assertEquals('</pre></div>', $this->helper->getElementEnd());
    }

    public function testCanSetElementSeparatorString()
    {
        $this->testGetElementSeparatorReturnsDefaultValue();
        $this->helper->setElementSeparator('<br />');
        $this->assertEquals('<br />', $this->helper->getElementSeparator());
    }

    public function testCanSetElementStartString()
    {
        $this->testGetElementStartReturnsDefaultValue();
        $this->helper->setElementStart('<div><pre>');
        $this->assertEquals('<div><pre>', $this->helper->getElementStart());
    }

    public function testFormErrorsRendersUnorderedListByDefault()
    {
        $errors = ['foo', 'bar', 'baz'];
        $html = $this->helper->formErrors($errors);
        $this->assertStringContainsString('<ul', $html);
        foreach ($errors as $error) {
            $this->assertStringContainsString('<li>' . $error . '</li>', $html);
        }
        $this->assertStringContainsString('</ul>', $html);
    }

    public function testFormErrorsRendersWithSpecifiedStrings()
    {
        $this->helper->setElementStart('<dl><dt>')
                     ->setElementSeparator('</dt><dt>')
                     ->setElementEnd('</dt></dl>');
        $errors = ['foo', 'bar', 'baz'];
        $html = $this->helper->formErrors($errors);
        $this->assertStringContainsString('<dl>', $html);
        foreach ($errors as $error) {
            $this->assertStringContainsString('<dt>' . $error . '</dt>', $html);
        }
        $this->assertStringContainsString('</dl>', $html);
    }

    public function testFormErrorsPreventsXssAttacks()
    {
        $errors = [
            'bad' => '\"><script>alert("xss");</script>',
        ];
        $html = $this->helper->formErrors($errors);
        $this->assertStringNotContainsString($errors['bad'], $html);
        $this->assertStringContainsString('&', $html);
    }

    public function testCanDisableEscapingErrorMessages()
    {
        $errors = [
            'foo' => '<b>Field is required</b>',
            'bar' => '<a href="/help">Please click here for more information</a>'
        ];
        $html = $this->helper->formErrors($errors, ['escape' => false]);
        $this->assertStringContainsString($errors['foo'], $html);
        $this->assertStringContainsString($errors['bar'], $html);
    }

    /**
     * @group ZF-3477
     * @link http://framework.zend.com/issues/browse/ZF-3477
     */
    public function testCanSetClassAttribute()
    {
        $options = ['class' => 'custom-class'];
        $actualHtml = $this->helper->formErrors([], $options);
        $this->assertEquals(
            '<ul class="custom-class"><li></li></ul>',
            $actualHtml
        );
    }

    /**
     * @group ZF-5962
     */
    public function testCanSetElementStringsPerOptions()
    {
        $actual = $this->helper->formErrors(
            ['foo', 'bar', 'baz'],
            [
                 'elementStart' => '<p>',
                 'elementEnd' => '</p>',
                 'elementSeparator' => '<br>',
            ]
        );

        $this->assertEquals('<p>foo<br>bar<br>baz</p>', $actual);
    }
}

// Call Zend_View_Helper_FormErrorsTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_FormErrorsTest::main") {
    Zend_View_Helper_FormErrorsTest::main();
}
