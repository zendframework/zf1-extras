<?php
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
 * @package    ZendX_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: NavigationTest.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'ZendX_Application_Resource_JQueryTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * Zend_Loader_Autoloader
 */
require_once 'Zend/Loader/Autoloader.php';

/**
 * @category   Zend
 * @package    ZendX_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class Zend_Application_Resource_JQueryTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        Zend_Loader_Autoloader::resetInstance();
        $this->autoloader = Zend_Loader_Autoloader::getInstance();

        $this->application = new Zend_Application('testing');

        $this->bootstrap = new Zend_Application_Bootstrap_Bootstrap($this->application);

        Zend_Registry::_unsetInstance();
        ZendX_JQuery_View_Helper_JQuery::disableNoConflictMode();
    }

    public function tearDown()
    {
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        foreach ($loaders as $loader) {
            spl_autoload_unregister($loader);
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        // Reset autoloader instance so it doesn't affect other tests
        Zend_Loader_Autoloader::resetInstance();
        ZendX_JQuery_View_Helper_JQuery::disableNoConflictMode();
    }

    public function testInitializationInitializesJqueryObject()
    {
//         $resource->setOptions(array('noconflictmode' => true, 'version' => '1.2.3'));
        $this->bootstrap->registerPluginResource('view');
        $resource = new ZendX_Application_Resource_JQuery(array());
        $resource->setBootstrap($this->bootstrap);

        $res = $resource->init();
        $this->assertTrue($res instanceof ZendX_JQuery_View_Helper_JQuery_Container);
        $this->assertSame($res, $resource->getJQuery());
    }
    
    /**
     *  *   resources.Jquery.noconflictmode = false        ; default
 *   resources.Jquery.version = 1.7.1               ; <null>
 *   resources.Jquery.localpath = "/foo/bar"
 *   resources.Jquery.uienable = true;
 *   resources.Jquery.ui_enable = true;
 *   resources.Jquery.uiversion = 0.7.7;
 *   resources.Jquery.ui_version = 0.7.7;
 *   resources.Jquery.uilocalpath = "/bar/foo";
 *   resources.Jquery.ui_localpath = "/bar/foo";
 *   resources.Jquery.cdn_ssl = false
 *   resources.Jquery.render_mode = 255 ; default
 *   resources.Jquery.rendermode = 255 ; default
 *   
 *   resources.Jquery.javascriptfile = "/some/file.js"
 *   resources.Jquery.javascriptfiles.0 = "/some/file.js"
 *   resources.Jquery.stylesheet = "/some/file.css"
 *   resources.Jquery.stylesheets.0 = "/some/file.css"
 *   /**
     *
     */
    
    public function testOptionsArePassedOn() {
        $options = array('noconflictmode' => true,
                         'version' => '1.2.3',
                         'localpath' => '/foo/bar/',
                         'ui_version' => '2.3.4',
                         'uilocalpath' => '/bar/foo/',
                         'cdn_ssl' => true,
                         'rendermode' => 192,
                         'javascriptfile' => '/fooBar.js',
                         'javascriptfiles' => array('johndoe.js','janedoe.js'),
                         'stylesheet' => '/fooBar.css',
                         'stylesheets' => array('johndoe.css','janedoe.css'));                         
        
        $this->bootstrap->registerPluginResource('view');
        $resource = new ZendX_Application_Resource_JQuery(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);

        $res = $resource->init();
        
        $this->assertTrue(ZendX_JQuery_View_Helper_JQuery::getNoConflictMode());
        $this->assertEquals('1.2.3', $res->getVersion());
        $this->assertEquals('/foo/bar/', $res->getLocalPath());
        $this->assertEquals('2.3.4', $res->getUiVersion());
        $this->assertEquals('/bar/foo/', $res->getUiLocalPath());
//        $this->assertTrue($res->getCdnSsl()); // Not implemented yet
        $this->assertEquals(192, $res->getRenderMode());
        $this->assertEquals(array('/fooBar.css', 'johndoe.css', 'janedoe.css'),
                            $res->getStylesheets());
        $this->assertEquals(array('/fooBar.js', 'johndoe.js', 'janedoe.js'),
                            $res->getJavascriptFiles());                            
    }
    
    public function testAliasOptionsArePassedOn() {
        $options = array('uiversion' => '3.4.5',
                         'ui_localpath' => '/f00/b4r/',
                         'render_mode' => 187);
        
        $this->bootstrap->registerPluginResource('view');
        $resource = new ZendX_Application_Resource_JQuery(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);

        $res = $resource->init();
        
        $this->assertEquals('3.4.5', $res->getUiVersion());
        $this->assertEquals('/f00/b4r/', $res->getUiLocalPath());
        $this->assertEquals(187, $res->getRenderMode());
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Application_Resource_LocaleTest::main') {
    Zend_Application_Resource_LocaleTest::main();
}
