<?php
/* SVN FILE: $Id$ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) Tests <https://trac.cakephp.org/wiki/Developement/TestSuite>
 * Copyright 2006-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2006-2008, Cake Software Foundation, Inc.
 * @link				https://trac.cakephp.org/wiki/Developement/TestSuite CakePHP(tm) Tests
 * @package			cake.tests
 * @subpackage		cake.tests.cases.libs.view.helpers
 * @since			CakePHP(tm) v 1.2.0.4206
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
uses('view'.DS.'helpers'.DS.'app_helper', 'view'.DS.'helper', 'view'.DS.'helpers'.DS.'javascript','view'.DS.'view',
	'view'.DS.'helpers'.DS.'html', 'view'.DS.'helpers'.DS.'form', 'class_registry', 'controller'.DS.'controller');

class TheJsTestController extends Controller {
	var $name = 'TheTest';
	var $uses = null;
}
class TheView extends View {
	function scripts() {
		return $this->__scripts;
	}
}
class TestJavascriptObject {
	var $property1 = 'value1';
	var $property2 = 2;
}
/**
 * Short description for class.
 *
 * @package    test_suite
 * @subpackage test_suite.cases.libs
 * @since      CakePHP Test Suite v 1.0.0.0
 */
class JavascriptTest extends UnitTestCase {
	function setUp() {
		$this->Javascript =& new JavascriptHelper();
		$this->Javascript->Html =& new HtmlHelper();
		$this->Javascript->Form =& new FormHelper();
		$this->View =& new TheView(new TheJsTestController());
		ClassRegistry::addObject('view', $this->View);
	}

	function tearDown() {
		unset($this->Javascript->Html);
		unset($this->Javascript->Form);
		unset($this->Javascript);
		ClassRegistry::removeObject('view');
		unset($this->View);
	}

	function testLink() {
		$result = $this->Javascript->link('script.js');
		$expected = '<script type="text/javascript" src="js/script.js"></script>';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->link('script');
		$expected = '<script type="text/javascript" src="js/script.js"></script>';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->link('scriptaculous.js?load=effects');
		$expected = '<script type="text/javascript" src="js/scriptaculous.js?load=effects"></script>';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->link('jquery-1.1.2');
		$expected = '<script type="text/javascript" src="js/jquery-1.1.2.js"></script>';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->link('jquery-1.1.2');
		$expected = '<script type="text/javascript" src="js/jquery-1.1.2.js"></script>';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->link('/plugin/js/jquery-1.1.2');
		$expected = '<script type="text/javascript" src="/plugin/js/jquery-1.1.2.js"></script>';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->link('/some_other_path/myfile.1.2.2.min.js');
		$expected = '<script type="text/javascript" src="/some_other_path/myfile.1.2.2.min.js"></script>';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->link('some_other_path/myfile.1.2.2.min.js');
		$expected = '<script type="text/javascript" src="js/some_other_path/myfile.1.2.2.min.js"></script>';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->link('some_other_path/myfile.1.2.2.min');
		$expected = '<script type="text/javascript" src="js/some_other_path/myfile.1.2.2.min.js"></script>';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->link('http://example.com/jquery.js');
		$expected = '<script type="text/javascript" src="http://example.com/jquery.js"></script>';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->link(array('prototype.js', 'scriptaculous.js'));
		$this->assertPattern('/^\s*<script\s+type="text\/javascript"\s+src=".*js\/prototype\.js"[^<>]*><\/script>/', $result);
		$this->assertPattern('/<\/script>\s*<script[^<>]+>/', $result);
		$this->assertPattern('/<script\s+type="text\/javascript"\s+src=".*js\/scriptaculous\.js"[^<>]*><\/script>\s*$/', $result);

		$result = $this->Javascript->link('jquery-1.1.2', false);
		$resultScripts = $this->View->scripts();
		reset($resultScripts);
		$expected = '<script type="text/javascript" src="js/jquery-1.1.2.js"></script>';
		$this->assertNull($result);
		$this->assertEqual(count($resultScripts), 1);
		$this->assertEqual(current($resultScripts), $expected);
	}

	function testFilteringAndTimestamping() {
		if (!is_writable(JS)) {
			echo "<br />JavaScript directory not writable, skipping JS asset timestamp tests<br />";
			return;
		}

		cache(str_replace(WWW_ROOT, '', JS) . '__cake_js_test.js', 'alert("test")', '+999 days', 'public');
		$timestamp = substr(strtotime('now'), 0, 8);

		Configure::write('Asset.timestamp', true);
		$result = $this->Javascript->link('__cake_js_test');
		$this->assertPattern('/^<script[^<>]+src=".*js\/__cake_js_test\.js\?' . $timestamp . '[0-9]{2}"[^<>]*>/', $result);

		$debug = Configure::read('debug');
		Configure::write('debug', 0);
		$result = $this->Javascript->link('__cake_js_test');
		$expected = '<script type="text/javascript" src="js/__cake_js_test.js"></script>';
		$this->assertEqual($result, $expected);

		Configure::write('Asset.timestamp', 'force');
		$result = $this->Javascript->link('__cake_js_test');
		$this->assertPattern('/^<script[^<>]+src=".*js\/__cake_js_test.js\?' . $timestamp . '[0-9]{2}"[^<>]*>/', $result);

		Configure::write('debug', $debug);
		Configure::write('Asset.timestamp', false);

		$old = Configure::read('Asset.filter.js');

		Configure::write('Asset.filter.js', 'js.php');
		$result = $this->Javascript->link('__cake_js_test');
		$this->assertPattern('/^<script[^<>]+src=".*cjs\/__cake_js_test\.js"[^<>]*>/', $result);

		Configure::write('Asset.filter.js', true);
		$result = $this->Javascript->link('jquery-1.1.2');
		$expected = '<script type="text/javascript" src="cjs/jquery-1.1.2.js"></script>';
		$this->assertEqual($result, $expected);

		if ($old === null) {
			Configure::delete('Asset.filter.js');
		}

		unlink(JS . '__cake_js_test.js');
	}

	function testValue() {
		$result = $this->Javascript->value(array('title' => 'New thing', 'indexes' => array(5, 6, 7, 8)));
		$expected = '{"title":"New thing","indexes":[5,6,7,8]}';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->value(null);
		$this->assertEqual($result, 'null');

		$result = $this->Javascript->value(true);
		$this->assertEqual($result, 'true');

		$result = $this->Javascript->value(false);
		$this->assertEqual($result, 'false');

		$result = $this->Javascript->value(5);
		$this->assertEqual($result, '5');

		$result = $this->Javascript->value(floatval(5.3));
		$this->assertPattern('/^5.3[0]+$/', $result);

		$result = $this->Javascript->value('');
		$expected = '""';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->value('CakePHP' . "\n" . 'Rapid Development Framework');
		$expected = '"CakePHP\\nRapid Development Framework"';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->value('CakePHP' . "\r\n" . 'Rapid Development Framework' . "\r" . 'For PHP');
		$expected = '"CakePHP\\nRapid Development Framework\\nFor PHP"';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->value('CakePHP: "Rapid Development Framework"');
		$expected = '"CakePHP: \\"Rapid Development Framework\\""';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->value('CakePHP: \'Rapid Development Framework\'');
		$expected = '"CakePHP: \\\'Rapid Development Framework\\\'"';
		$this->assertEqual($result, $expected);
	}

	function testObjectGeneration() {
		$object = array('title' => 'New thing', 'indexes' => array(5, 6, 7, 8));
		$result = $this->Javascript->object($object);
		$expected = '{"title":"New thing","indexes":[5,6,7,8]}';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->object(array('default' => 0));
		$expected = '{"default":0}';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->object(array(
			'2007' => array(
				'Spring' => array('1' => array('id' => 1, 'name' => 'Josh'), '2' => array('id' => 2, 'name' => 'Becky')),
				'Fall' => array('1' => array('id' => 1, 'name' => 'Josh'), '2' => array('id' => 2, 'name' => 'Becky'))
			), '2006' => array(
				'Spring' => array('1' => array('id' => 1, 'name' => 'Josh'), '2' => array('id' => 2, 'name' => 'Becky')),
				'Fall' => array('1' => array('id' => 1, 'name' => 'Josh'), '2' => array('id' => 2, 'name' => 'Becky')
			))
		));
		$expected = '{"2007":{"Spring":{"1":{"id":1,"name":"Josh"},"2":{"id":2,"name":"Becky"}},"Fall":{"1":{"id":1,"name":"Josh"},"2":{"id":2,"name":"Becky"}}},"2006":{"Spring":{"1":{"id":1,"name":"Josh"},"2":{"id":2,"name":"Becky"}},"Fall":{"1":{"id":1,"name":"Josh"},"2":{"id":2,"name":"Becky"}}}}';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->object(array('Object' => array(true, false, 1, '02101', 0, -1, 3.141592653589, "1")));
		$expected = '{"Object":[true,false,1,"02101",0,-1,3.14159265359,"1"]}';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->object(array('Object' => array(true => true, false, -3.141592653589, -10)));
		$expected = '{"Object":{"1":true,"2":false,"3":-3.14159265359,"4":-10}}';
		$this->assertEqual($result, $expected);

		if (function_exists('json_encode')) {
			$old = $this->Javascript->useNative;
			$this->Javascript->useNative = true;
			$object = array('title' => 'New thing', 'indexes' => array(5, 6, 7, 8));
			$result = $this->Javascript->object($object);
			$expected = '{"title":"New thing","indexes":[5,6,7,8]}';
			$this->assertEqual($result, $expected);
			$this->Javascript->useNative = $old;
		}

		$result = $this->Javascript->object(new TestJavascriptObject());
		$expected = '{"property1":"value1","property2":2}';
		$this->assertEqual($result, $expected);

		$object = array('title' => 'New thing', 'indexes' => array(5, 6, 7, 8));
		$result = $this->Javascript->object($object, array('block' => true));
		$expected = '{"title":"New thing","indexes":[5,6,7,8]}';
		$this->assertPattern('/^<script[^<>]+>\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*' . str_replace('/', '\\/', preg_quote($expected)) . '\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">.+<\/script>$/s', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);
	}

	function testScriptBlock() {
		$result = $this->Javascript->codeBlock('something', true, false);
		$this->assertPattern('/^<script[^<>]+>something<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">something<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->codeBlock('something', false, false);
		$this->assertPattern('/^<script[^<>]+>something<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">something<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->codeBlock('something', true, true);
		$this->assertPattern('/^<script[^<>]+>\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*something\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*something\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->codeBlock('something', false, true);
		$this->assertPattern('/^<script[^<>]+>\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*something\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*something\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->codeBlock('something', array('safe' => false));
		$this->assertPattern('/^<script[^<>]+>something<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">something<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->codeBlock('something', array('safe' => true));
		$this->assertPattern('/^<script[^<>]+>\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*something\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*something\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->codeBlock(null, array('safe' => true, 'allowCache' => false));
		$this->assertPattern('/^<script[^<>]+>\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*$/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->blockEnd();
		$this->assertPattern('/^\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);

		$result = $this->Javascript->codeBlock('something');
		$this->assertPattern('/^<script[^<>]+>\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*something\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">.+<\/script>$/s', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->codeBlock();
		$this->assertPattern('/^<script[^<>]+>\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*$/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->blockEnd();
		$this->assertPattern('/^\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);

		$this->Javascript->cacheEvents(false, true);
		$this->assertFalse($this->Javascript->inBlock);
		$result = $this->Javascript->codeBlock();
		$this->assertIdentical($result, null);
		$this->assertTrue($this->Javascript->inBlock);
		echo 'alert("this is a buffered script");';

		$result = $this->Javascript->blockEnd();
		$this->assertIdentical($result, null);
		$this->assertFalse($this->Javascript->inBlock);

		$result = $this->Javascript->getCache();
		$this->assertEqual('alert("this is a buffered script");', $result);
	}

	function testOutOfLineScriptWriting() {
		echo $this->Javascript->codeBlock('$(document).ready(function() { /* ... */ });', array('inline' => false));

		$this->Javascript->codeBlock(null, array('inline' => false));
		echo '$(function(){ /* ... */ });';
		$this->Javascript->blockEnd();
	}

	function testEvent() {
		$result = $this->Javascript->event('myId', 'click', 'something();');
		$this->assertPattern('/^<script[^<>]+>\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*.+\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">.+' . str_replace('/', '\\/', preg_quote('Event.observe($(\'myId\'), \'click\', function(event) { something(); }, false);')) . '.+<\/script>$/s', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->event('myId', 'click', 'something();', array('safe' => false));
		$this->assertPattern('/^<script[^<>]+>[^<>]+<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">' . str_replace('/', '\\/', preg_quote('Event.observe($(\'myId\'), \'click\', function(event) { something(); }, false);')) . '<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->event('myId', 'click');
		$this->assertPattern('/^<script[^<>]+>\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*.+\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">.+' . str_replace('/', '\\/', preg_quote('Event.observe($(\'myId\'), \'click\', function(event) {  }, false);')) . '.+<\/script>$/s', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->event('myId', 'click', 'something();', false);
		$this->assertPattern('/^<script[^<>]+>\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*.+\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">.+' . str_replace('/', '\\/', preg_quote('Event.observe($(\'myId\'), \'click\', function(event) { something(); }, false);')) . '.+<\/script>$/s', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->event('myId', 'click', 'something();', array('useCapture' => true));
		$this->assertPattern('/^<script[^<>]+>\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*.+\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">.+' . str_replace('/', '\\/', preg_quote('Event.observe($(\'myId\'), \'click\', function(event) { something(); }, true);')) . '.+<\/script>$/s', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->event('document', 'load');
		$this->assertPattern('/^<script[^<>]+>\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*.+\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">.+' . str_replace('/', '\\/', preg_quote('Event.observe(document, \'load\', function(event) {  }, false);')) . '.+<\/script>$/s', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->event('$(\'myId\')', 'click', 'something();', array('safe' => false));
		$this->assertPattern('/^<script[^<>]+>[^<>]+<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">' . str_replace('/', '\\/', preg_quote('Event.observe($(\'myId\'), \'click\', function(event) { something(); }, false);')) . '<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->event('\'document\'', 'load', 'something();', array('safe' => false));
		$this->assertPattern('/^<script[^<>]+>[^<>]+<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">' . str_replace('/', '\\/', preg_quote('Event.observe(\'document\', \'load\', function(event) { something(); }, false);')) . '<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$this->Javascript->cacheEvents();
		$result = $this->Javascript->event('myId', 'click', 'something();');
		$this->assertNull($result);

		$result = $this->Javascript->getCache();
		$this->assertPattern('/^' . str_replace('/', '\\/', preg_quote('Event.observe($(\'myId\'), \'click\', function(event) { something(); }, false);')) . '$/s', $result);

		$result = $this->Javascript->event('#myId', 'alert(event);');
		$this->assertNull($result);

		$result = $this->Javascript->getCache();
		$this->assertPattern('/^\s*var Rules = {\s*\'#myId\': function\(element, event\)\s*{\s*alert\(event\);\s*}\s*}\s*EventSelectors\.start\(Rules\);\s*$/s', $result);
	}

	function testWriteEvents() {
		$this->Javascript->cacheEvents();
		$result = $this->Javascript->event('myId', 'click', 'something();');
		$this->assertNull($result);

		$result = $this->Javascript->writeEvents();
		$this->assertPattern('/^<script[^<>]+>\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*.+\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">.+' . str_replace('/', '\\/', preg_quote('Event.observe($(\'myId\'), \'click\', function(event) { something(); }, false);')) . '.+<\/script>$/s', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->getCache();
		$this->assertTrue(empty($result));

		$this->Javascript->cacheEvents();
		$result = $this->Javascript->event('myId', 'click', 'something();');
		$this->assertNull($result);

		$result = $this->Javascript->writeEvents(false);
		$resultScripts = $this->View->scripts();
		reset($resultScripts);
		$this->assertNull($result);
		$this->assertEqual(count($resultScripts), 1);
		$result = current($resultScripts);
		$this->assertPattern('/^<script[^<>]+>\s*' . str_replace('/', '\\/', preg_quote('//<![CDATA[')) . '\s*.+\s*' . str_replace('/', '\\/', preg_quote('//]]>')) . '\s*<\/script>$/', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript">.+' . str_replace('/', '\\/', preg_quote('Event.observe($(\'myId\'), \'click\', function(event) { something(); }, false);')) . '.+<\/script>$/s', $result);
		$this->assertPattern('/^<script[^<>]+type="text\/javascript"[^<>]*>/', $result);
		$this->assertNoPattern('/^<script[^type]=[^<>]*>/', $result);

		$result = $this->Javascript->getCache();
		$this->assertTrue(empty($result));
	}

	function testEscapeScript() {
		$result = $this->Javascript->escapeScript('');
		$expected = '';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->escapeScript('CakePHP' . "\n" . 'Rapid Development Framework');
		$expected = 'CakePHP\\nRapid Development Framework';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->escapeScript('CakePHP' . "\r\n" . 'Rapid Development Framework' . "\r" . 'For PHP');
		$expected = 'CakePHP\\nRapid Development Framework\\nFor PHP';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->escapeScript('CakePHP: "Rapid Development Framework"');
		$expected = 'CakePHP: \\"Rapid Development Framework\\"';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->escapeScript('CakePHP: \'Rapid Development Framework\'');
		$expected = 'CakePHP: \\\'Rapid Development Framework\\\'';
		$this->assertEqual($result, $expected);
	}

	function testEscapeString() {
		$result = $this->Javascript->escapeString('');
		$expected = '';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->escapeString('CakePHP' . "\n" . 'Rapid Development Framework');
		$expected = 'CakePHP\\nRapid Development Framework';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->escapeString('CakePHP' . "\r\n" . 'Rapid Development Framework' . "\r" . 'For PHP');
		$expected = 'CakePHP\\nRapid Development Framework\\nFor PHP';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->escapeString('CakePHP: "Rapid Development Framework"');
		$expected = 'CakePHP: \\"Rapid Development Framework\\"';
		$this->assertEqual($result, $expected);

		$result = $this->Javascript->escapeString('CakePHP: \'Rapid Development Framework\'');
		$expected = 'CakePHP: \\\'Rapid Development Framework\\\'';
		$this->assertEqual($result, $expected);
	}
}

?>