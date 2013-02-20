<?php

/**
 * Base class for phantomQUnit tasks which generate test files from templates.
 *
 * @package    phantomTestPlugin
 * @subpackage task

 * @author     Pablo Godel <pgodel@gmail.com>
 * @author     Frank Stelzer <dev@frankstelzer.de>
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
abstract class phantomQUnitRunTask extends phantomBaseRunTask {

    protected function configure() {
        $this->namespace = 'phantom';
        $this->name = 'qunit';

        $this->briefDescription = 'Launches QUnit tests using PhantomJS headless WebKit';
        
        parent::configure();
    }

    protected function getTestDirectory() {
        return sfConfig::get('phantom_qunit_test_directory');
    }

}