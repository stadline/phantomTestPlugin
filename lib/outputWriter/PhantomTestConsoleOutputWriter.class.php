<?php

class PhantomTestConsoleOutputWriter extends PhantomTestOutputWriter {

    private $summary = array('suites' => 0, 'total' => 0, 'passed' => 0, 'failed' => 0, 'errorEncountered' => false);
    private $lineWidth = 70;
    private $tabWidth = 2;

    const GREENWHITE = "\033[1;97m\033[42m";
    const REDWHITE = "\033[1;97m\033[41m";
    const NORMAL = "\033[0m";
    const RED = "\033[0;91m";
    const GREEN = "\033[0;92m";

    public function initialize() {
        //noop
    }

    public function writeTestSuite($suite) {
        $startColor = "";
        $endColor = "";

        if ($suite['summary']['failed'] > 0){
            $this->summary['errorEncountered'] = true;

            $startColor = self::REDWHITE;
            $endColor = self::NORMAL;
            $status = $suite['summary']['failed'] . " errors";
        }
        else {
            $startColor = self::GREEN;
            $endColor = self::NORMAL;
            $status = "ok";
        }

        printf("%'.-{$this->lineWidth}s$startColor%s$endColor\n", $suite['name'], $status);

        $this->summary['total'] += $suite['summary']['total'];
        $this->summary['passed'] += $suite['summary']['passed'];
        $this->summary['failed'] += $suite['summary']['failed'];
        $this->summary['suites']++;

        if ($this->verboseMode){
            foreach ($suite['cases'] as $case) {
                $this->writeTestCase($case);
            }
        }
    }

    public function writeTestCase($case) {
        $tabWidth = $this->tabWidth;
        $lineWidth = $this->lineWidth - $tabWidth;

        $startColor = "";
        $endColor = "";
        $name = $case['name'];
        if (isset($case['module']) && $case['module'] != 'none') $name = $case['module'] . ".$name";

        if (isset($case['failed']) && count($case['failed']) > 0){
            $status = "problem";
            $startColor = self::RED;
            $endColor = self::NORMAL;
        }
        else {
            $startColor = self::GREEN;
            $endColor = self::NORMAL;
            $status = "ok";
        }

        printf("%-{$tabWidth}s%'.-{$lineWidth}s$startColor%s$endColor\n", "", $name, $status);

        if (isset($case['failed']))
            $this->writeTestErrors($case['failed']);
    }

    protected function writeTestErrors($errors) {
        $tabWidth = $this->tabWidth * 2;
        $lineWidth = $this->lineWidth - $tabWidth;

        $startColor = self::RED;
        $endColor = self::NORMAL;

        foreach ($errors as $error) {
            $source = "";
            if (isset($failure['source']) && strlen($failure['source']) > 0) {
                $source = "Encountered at " . $failure['source'];
            }

            printf("$startColor%-{$tabWidth}s%-{$lineWidth}s$endColor\n", "", "Error: $error");
        }
    }

    public function finalize() {
        $startColor = self::GREENWHITE;
        $endColor = self::NORMAL;

        $message = " All tests successful!";
        if ($this->summary['errorEncountered']){
            $startColor = self::REDWHITE;
            $endColor = self::NORMAL;

            $message = " There were errors or failures encountered when running tests. Search and destroy those bugs!";
        }
        echo "\n" . $startColor . $message . "\n";
        echo " " . $this->summary['suites'] . " test suites. " . $this->summary['passed'] . " tests of " . $this->summary['total'] . " passed, " . $this->summary['failed'] . " failed.$endColor\n\n";
    }

    
}

?>
