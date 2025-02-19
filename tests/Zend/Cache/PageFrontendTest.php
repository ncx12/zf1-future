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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Frontend/Page.php';
require_once 'Zend/Cache/Backend/Test.php';

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class Zend_Cache_PageFrontendTest extends TestCase
{
    private $_instance;

    protected function setUp(): void
    {
        if (!$this->_instance) {
            $this->_instance = new Zend_Cache_Frontend_Page([]);
            $this->_backend = new Zend_Cache_Backend_Test();
            $this->_instance->setBackend($this->_backend);
        }
    }

    protected function tearDown(): void
    {
        unset($this->_instance);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Frontend_Page(['lifetime' => 3600, 'caching' => true]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorUnimplementedOption()
    {
        try {
            $test = new Zend_Cache_Frontend_Page(['http_conditional' => true]);
        } catch (Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorWithBadDefaultOptions()
    {
        try {
            $test = new Zend_Cache_Frontend_Page(['default_options' => 'foo']);
        } catch (Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    /**
     * The only bad default options are non-string keys
     * @group ZF-5034
     * @doesNotPerformAssertions
     */
    public function testConstructorWithBadDefaultOptions2()
    {
        try {
            $test = new Zend_Cache_Frontend_Page(['default_options' => ['cache' => true, 1 => 'bar']]);
        } catch (Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorWithBadRegexps()
    {
        try {
            $test = new Zend_Cache_Frontend_Page(['regexps' => 'foo']);
        } catch (Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorWithBadRegexps2()
    {
        try {
            $test = new Zend_Cache_Frontend_Page(['regexps' => ['foo', 'bar']]);
        } catch (Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    /**
     * Only non-string keys should raise exceptions
     * @group ZF-5034
     * @doesNotPerformAssertions
     */
    public function testConstructorWithBadRegexps3()
    {
        $array = [
           '^/$' => ['cache' => true],
           '^/index/' => ['cache' => true],
           '^/article/' => ['cache' => false],
           '^/article/view/' => [
               1 => true,
               'cache_with_post_variables' => true,
               'make_id_with_post_variables' => true,
           ]
        ];
        try {
            $test = new Zend_Cache_Frontend_Page(['regexps' => $array]);
        } catch (Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorWithGoodRegexps()
    {
        $array = [
           '^/$' => ['cache' => true],
           '^/index/' => ['cache' => true],
           '^/article/' => ['cache' => false],
           '^/article/view/' => [
               'cache' => true,
               'cache_with_post_variables' => true,
               'make_id_with_post_variables' => true,
           ]
        ];
        $test = new Zend_Cache_Frontend_Page(['regexps' => $array]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorWithGoodDefaultOptions()
    {
        $test = new Zend_Cache_Frontend_Page(['default_options' => ['cache' => true]]);
    }

    public function testStartEndCorrectCall1()
    {
        ob_start();
        ob_implicit_flush(false);
        if (!($this->_instance->start('serialized2', true))) {
            echo('foobar');
            ob_end_flush();
        }
        $data = ob_get_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foo', $data);
    }

    public function testStartEndCorrectCall2()
    {
        ob_start();
        ob_implicit_flush(false);
        if (!($this->_instance->start('false', true))) {
            echo('foobar');
            ob_end_flush();
        }
        $data = ob_get_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foobar', $data);
    }

    public function testStartEndCorrectCallWithDebug()
    {
        $this->_instance->setOption('debug_header', true);
        ob_start();
        ob_implicit_flush(false);
        if (!($this->_instance->start('serialized2', true))) {
            echo('foobar');
            ob_end_flush();
        }
        $data = ob_get_clean();
        ob_implicit_flush(true);
        $this->assertEquals('DEBUG HEADER : This is a cached page !foo', $data);
    }

    /**
     * @group ZF-10952
     * @doesNotPerformAssertions
     */
    public function testNootice()
    {
        $regex = ['^/article/' => ['cache' => false]];
        $this->_instance->setOption('regexps', $regex);
        $this->_instance->setOption('caching', false);
        $this->_instance->start('zf10952');
        ob_get_clean();
    }
}
