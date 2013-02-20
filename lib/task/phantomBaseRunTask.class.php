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
abstract class phantomBaseRunTask extends sfBaseTask {

    abstract protected function getTestDirectory();
    abstract protected function getTestUrl($path);

    protected function configure()
    {
        $this->detailedDescription = '';

        $this->addArguments(array(
            new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
            new sfCommandArgument('testFile', sfCommandArgument::OPTIONAL, 'Test file to run', null),
        ));

        $this->addOptions(array(
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('xml', null, sfCommandOption::PARAMETER_OPTIONAL, 'File to write junit format xml to', null)
        ));
    }

    protected function execute($arguments = array(), $options = array())
    {
        $application = $arguments['application'];
        
        $startTime = microtime(true);
        $testResults = array();
        $testSummary = array();

        $testDirectory = $this->getTestDirectory();
        if ($testDirectory[0] != '/') {
            $testDirectory = sfConfig::get('sf_root_dir') . '/' . $testDirectory;
        }
        $testDirectory .= '/' . $application;

        $verbose = $arguments['testFile'] != null ? true : false;

        // setup the output writer and output the results, either as xml or to the console
        if ($options['xml']) {
            $this->outputWriter = new PhantomTestXMLOutputWriter($options['xml'], $testSummary, $testResults, $verbose);
        } else {
            $this->outputWriter = new PhantomTestConsoleOutputWriter($testSummary, $testResults, $verbose);
        }

        $this->outputWriter->initialize();

        // figure out which mode we're in, one test file or all test file
        if ($arguments['testFile']) {
            $this->runTestsOnFile($application, $testDirectory, $arguments['testFile']);
        } else {
            $files = sfFinder::type('file')->follow_link()->name('*Test.js')->in($testDirectory);

            foreach ($files as $path) {
                $this->runTestsOnFile($application, $testDirectory, substr($path, strlen($testDirectory) + 1));
            }
        }
        
        // write results and summary
        $this->outputWriter->finalize();
        echo 'Tests completed in ' . round(((microtime(true) - $startTime) * 1000)) . " ms.\n";
    }

    protected function runTestsOnFile($application, $testDirectory, $path)
    {
        $fullPath = $testDirectory. '/' . $path;
        if (!file_exists($fullPath)) {
            throw new Exception("$fullPath does not exist!");
        }
        
        // init vars
        $runnerDir   = dirname(__FILE__).'/../runner';
        $testUrl     = $this->getTestUrl($path);
        $testFullUrl = sfConfig::get('phantom_url_base').$testUrl;
        
        // test url
        if (false === @get_headers($testFullUrl)) {
            throw new Exception("$testFullUrl is not reachable! Check your /etc/hosts settings.");
        }
        
        // launch test
        $startTime = microtime(true);
        exec("phantomjs $runnerDir/test-runner.js $testFullUrl", $results, $errors);
        $endTime = (microtime(true) - $startTime) * 1000;
        
        // push test case infos
        $suite = array(
            'name' => "[$application] $testUrl",
            'time' => number_format($endTime, 3),
        );
        
        $this->addResultTestCaseToSuite($suite, $results, $errors);
        $this->outputWriter->writeTestSuite($suite);
    }

    public function addResultTestCaseToSuite(&$suite, $results, $errors)
    {
        $suite['summary']['total']  = isset($results[2]) ? $results[2] : 0;
        $suite['summary']['passed'] = isset($results[1]) ? $results[1] : 0;
        $suite['summary']['failed'] = isset($results[3]) ? $results[3] : $errors;
        $suite['cases'] = array();
    }

}