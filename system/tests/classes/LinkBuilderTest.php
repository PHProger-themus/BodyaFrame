<?php

namespace system\tests\classes;

use PHPUnit\Framework\TestCase;
use system\classes\LinkBuilder;
use Cfg;
use system\core\PHP;

/**
 * @covers LinkBuilder
 */
class LinkBuilderTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        Cfg::$get->routes['testing/page'] = [
            'controller' => 'testing',
            'action' => 'index'
        ];
        Cfg::$get->routes['testing/page/{user}'] = [
            'controller' => 'testing',
            'action' => 'user'
        ];
        Cfg::$get->routes['testing/page/{user}/{setting}'] = [
            'controller' => 'testing',
            'action' => 'userSetting'
        ];
        Cfg::$get->lang = 'ru';
    }

    public function testUrlBuilding()
    {
        Cfg::$get->multilang = false;
        $this->assertEquals('/testing/page', LinkBuilder::url('testing', 'index'));
        Cfg::$get->multilang = true;
        $this->assertEquals('/ru/testing/page', LinkBuilder::url('testing', 'index'));
        $this->assertEquals('/en/testing/page', LinkBuilder::url('testing', 'index', ['lang' => 'en']));
        $this->assertEquals('/ru/testing/page?id=1&name=BodyaFrame', LinkBuilder::url('testing', 'index', ['get' => ['id' => 1, 'name' => 'BodyaFrame']]));
    }

    public function testUrlBuildingWithSpecifiedPrefix()
    {
        Cfg::$get->website['prefix'] = '/prefix';
        $this->assertEquals('/prefix/ru/testing/page', LinkBuilder::url('testing', 'index'));
        Cfg::$get->multilang = false;
        $this->assertEquals('/prefix/testing/page', LinkBuilder::url('testing', 'index'));
        Cfg::$get->multilang = true;
        $this->assertEquals('/prefix/en/testing/page', LinkBuilder::url('testing', 'index', ['lang' => 'en']));
        $this->assertEquals('/prefix/en/testing/page?id=1&name=BodyaFrame', LinkBuilder::url('testing', 'index', ['lang' => 'en', 'get' => ['id' => 1, 'name' => 'BodyaFrame']]));
    }

    public function testUrlBuildingWithAnchor()
    {
        Cfg::$get->website['prefix'] = '';
        $this->assertEquals('/ru/testing/page#products', LinkBuilder::url('testing', 'index', ['anchor' => 'products']));
        $this->assertEquals('/ru/testing/page?id=1#products', LinkBuilder::url('testing', 'index', ['anchor' => 'products', 'get' => ['id' => 1]]));
    }

    public function testUrlBuildingWithUrlParameter()
    {
        $this->assertEquals('/ru/testing/page/bodyaframe', LinkBuilder::url('testing', 'user', ['url' => ['user' => 'bodyaframe']]));
        $this->assertEquals('/ru/testing/page/bodyaframe/general', LinkBuilder::url('testing', 'userSetting', ['url' => ['user' => 'bodyaframe', 'setting' => 'general']]));
        $this->assertEquals('/ru/testing/page/bodyaframe/general?id=1#products', LinkBuilder::url('testing', 'userSetting', ['url' => ['user' => 'bodyaframe', 'setting' => 'general'], 'anchor' => 'products', 'get' => ['id' => 1]]));
    }

    public function testUrlBuildingWithUrlParametersExpectException()
    {
        $this->expectExceptionMessage("Не задан подмассив url или неверное количество параметров");
        $this->assertEquals('/ru/testing/page/bodyaframe/general', LinkBuilder::url('testing', 'userSetting', ['url' => ['user' => 'bodyaframe']]));
    }

    public function testRawUrlBuildingWithoutAll()
    {
        $this->assertEquals('/ru/testing/page', LinkBuilder::raw('/testing/page'));
        $this->assertEquals('/en/testing/page', LinkBuilder::raw('/testing/page', 'en'));
        $this->assertEquals('/en', LinkBuilder::raw('', 'en'));
    }

    public function testRawUrlBuildingWithPrefix()
    {
        Cfg::$get->website['prefix'] = '/prefix';
        $this->assertEquals('/prefix/ru/testing/page', LinkBuilder::raw('/testing/page'));
        $this->assertEquals('/prefix/en/testing/page', LinkBuilder::raw('/testing/page', 'en'));
        $this->assertEquals('/prefix/en', LinkBuilder::raw('', 'en'));
        $this->assertEquals('/prefix/ru', LinkBuilder::raw(''));
    }

    public function testAddingGetParameters()
    {
        $this->assertEquals('/ru/testing/page?id=1&name=BodyaFrame&token=jGilb5^hj4&testing=test+string', LinkBuilder::addGet(['testing' => 'test string']));
        $this->assertEquals('/ru/testing/page?testing=test+string&id=1&name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::addGet(['testing' => 'test string'], LinkBuilder::QUERY_BEGIN));
    }

    public function testReplacingGetParameters()
    {
        $this->assertEquals('/ru/testing/page?id=2&name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::addGet(['id' => '2'], LinkBuilder::QUERY_BEGIN));
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4&id=2', LinkBuilder::addGet(['id' => '2'], LinkBuilder::QUERY_END));
    }

    public function testAppendingGetParameters()
    {
        // Parameter [id] in the beginning
        $this->assertEquals('/ru/testing/page?id=1+3&name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::addGet(['id' => '3'], LinkBuilder::QUERY_BEGIN, LinkBuilder::QUERY_APPEND));
        $this->assertEquals('/ru/testing/page?id=1&name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::addGet(['id' => '1'], LinkBuilder::QUERY_END, LinkBuilder::QUERY_APPEND));

        // Parameter [id] in the middle
        PHP::setServer('REQUEST_URI', '/ru/testing/page?name=BodyaFrame&id=1&token=jGilb5^hj4');
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&id=1+3&token=jGilb5^hj4', LinkBuilder::addGet(['id' => '3'], LinkBuilder::QUERY_BEGIN, LinkBuilder::QUERY_APPEND));
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&id=1&token=jGilb5^hj4', LinkBuilder::addGet(['id' => '1'], LinkBuilder::QUERY_END, LinkBuilder::QUERY_APPEND));

        // Parameter [id] in the end
        PHP::setServer('REQUEST_URI', '/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4&id=1');
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4&id=1+3', LinkBuilder::addGet(['id' => '3'], LinkBuilder::QUERY_BEGIN, LinkBuilder::QUERY_APPEND));
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4&id=1', LinkBuilder::addGet(['id' => '1'], LinkBuilder::QUERY_END, LinkBuilder::QUERY_APPEND));

        // Multiple parameters [id] in the beginning
        PHP::setServer('REQUEST_URI', '/ru/testing/page?id=1+2+4&name=BodyaFrame&token=jGilb5^hj4');
        $this->assertEquals('/ru/testing/page?id=1+2+4+3&name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::addGet(['id' => '3'], LinkBuilder::QUERY_BEGIN, LinkBuilder::QUERY_APPEND));
        $this->assertEquals('/ru/testing/page?id=1+2+4&name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::addGet(['id' => '1'], LinkBuilder::QUERY_END, LinkBuilder::QUERY_APPEND));
        $this->assertEquals('/ru/testing/page?id=1+2+4&name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::addGet(['id' => '2'], LinkBuilder::QUERY_END, LinkBuilder::QUERY_APPEND));
        $this->assertEquals('/ru/testing/page?id=1+2+4&name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::addGet(['id' => '4'], LinkBuilder::QUERY_END, LinkBuilder::QUERY_APPEND));

        // Multiple parameters [id] in the middle
        PHP::setServer('REQUEST_URI', '/ru/testing/page?name=BodyaFrame&id=1+2+4&token=jGilb5^hj4');
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&id=1+2+4+3&token=jGilb5^hj4', LinkBuilder::addGet(['id' => '3'], LinkBuilder::QUERY_BEGIN, LinkBuilder::QUERY_APPEND));
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&id=1+2+4&token=jGilb5^hj4', LinkBuilder::addGet(['id' => '1'], LinkBuilder::QUERY_END, LinkBuilder::QUERY_APPEND));
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&id=1+2+4&token=jGilb5^hj4', LinkBuilder::addGet(['id' => '2'], LinkBuilder::QUERY_END, LinkBuilder::QUERY_APPEND));
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&id=1+2+4&token=jGilb5^hj4', LinkBuilder::addGet(['id' => '4'], LinkBuilder::QUERY_END, LinkBuilder::QUERY_APPEND));

        // Multiple parameters [id] in the end
        PHP::setServer('REQUEST_URI', '/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4&id=1+2+4');
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4&id=1+2+4+3', LinkBuilder::addGet(['id' => '3'], LinkBuilder::QUERY_BEGIN, LinkBuilder::QUERY_APPEND));
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4&id=1+2+4', LinkBuilder::addGet(['id' => '1'], LinkBuilder::QUERY_END, LinkBuilder::QUERY_APPEND));
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4&id=1+2+4', LinkBuilder::addGet(['id' => '2'], LinkBuilder::QUERY_END, LinkBuilder::QUERY_APPEND));
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4&id=1+2+4', LinkBuilder::addGet(['id' => '4'], LinkBuilder::QUERY_END, LinkBuilder::QUERY_APPEND));
    }

    public function testRemovingGetParameters()
    {
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::removeGet(['id']));

        PHP::setServer('REQUEST_URI', '/ru/testing/page?name=BodyaFrame&id=1+2+4&token=jGilb5^hj4');
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::removeGet(['id']));

        PHP::setServer('REQUEST_URI', '/ru/testing/page?id=1+2+4&name=BodyaFrame&token=jGilb5^hj4');
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::removeGet(['id']));

        PHP::setServer('REQUEST_URI', '/ru/testing/page?id=1+2+4');
        $this->assertEquals('/ru/testing/page', LinkBuilder::removeGet(['id']));
    }

    public function testCuttingGetParameters()
    {
        PHP::setServer('REQUEST_URI', '/ru/testing/page?id=1+2+4&name=BodyaFrame&token=jGilb5^hj4');
        $this->assertEquals('/ru/testing/page?id=1+2&name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::removeGet(['id' => 4], LinkBuilder::QUERY_CUT));
        $this->assertEquals('/ru/testing/page?id=1+4&name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::removeGet(['id' => 2], LinkBuilder::QUERY_CUT));
        $this->assertEquals('/ru/testing/page?id=2+4&name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::removeGet(['id' => 1], LinkBuilder::QUERY_CUT));

        PHP::setServer('REQUEST_URI', '/ru/testing/page?name=BodyaFrame&id=1+2+4&token=jGilb5^hj4');
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&id=1+2&token=jGilb5^hj4', LinkBuilder::removeGet(['id' => 4], LinkBuilder::QUERY_CUT));
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&id=1+4&token=jGilb5^hj4', LinkBuilder::removeGet(['id' => 2], LinkBuilder::QUERY_CUT));
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&id=2+4&token=jGilb5^hj4', LinkBuilder::removeGet(['id' => 1], LinkBuilder::QUERY_CUT));

        PHP::setServer('REQUEST_URI', '/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4&id=1+2+4');
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4&id=1+2', LinkBuilder::removeGet(['id' => 4], LinkBuilder::QUERY_CUT));
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4&id=1+4', LinkBuilder::removeGet(['id' => 2], LinkBuilder::QUERY_CUT));
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4&id=2+4', LinkBuilder::removeGet(['id' => 1], LinkBuilder::QUERY_CUT));
    }

    public function testCuttingGetParameter()
    {
        PHP::setServer('REQUEST_URI', '/ru/testing/page?id=1&name=BodyaFrame&token=jGilb5^hj4');
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::removeGet(['id' => 1], LinkBuilder::QUERY_CUT));

        PHP::setServer('REQUEST_URI', '/ru/testing/page?name=BodyaFrame&id=1&token=jGilb5^hj4');
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::removeGet(['id' => 1], LinkBuilder::QUERY_CUT));

        PHP::setServer('REQUEST_URI', '/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4&id=1');
        $this->assertEquals('/ru/testing/page?name=BodyaFrame&token=jGilb5^hj4', LinkBuilder::removeGet(['id' => 1], LinkBuilder::QUERY_CUT));

        PHP::setServer('REQUEST_URI', '/ru/testing/page?id=1');
        $this->assertEquals('/ru/testing/page', LinkBuilder::removeGet(['id' => 1], LinkBuilder::QUERY_CUT));
    }

}