<?php

/**
 * Base class for phantomFuncUnit tasks which generate test files from templates.
 *
 * @package    phantomTestPlugin
 * @subpackage task

 * @author     Pablo Godel <pgodel@gmail.com>
 * @author     Frank Stelzer <dev@frankstelzer.de>
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class phantomFuncUnitRunTask extends phantomBaseRunTask {

    protected function configure() {
        $this->namespace = 'phantom';
        $this->name = 'funcunit';

        $this->briefDescription = 'Launches FuncUnit tests using PhantomJS headless WebKit';
        
        parent::configure();
    }

    protected function getTestDirectory() {
        return sfConfig::get('phantom_funcunit_test_directory');
    }
    
    protected function getTestUrl($path)
    {
        $routing = $this->getRouting();
        $url = $routing->generate('phantom_funcunit_test_url', array('url' => $path));
        return urldecode($url);
    }

}