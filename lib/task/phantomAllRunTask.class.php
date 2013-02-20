<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Launches all tests.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfTestAllTask.class.php 29415 2010-05-12 06:24:54Z fabien $
 */
class phantomAllRunTask extends sfTestAllTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->namespace = 'phantom';
    $this->name = 'all';

    $this->briefDescription = 'Launches all tests';

    $this->detailedDescription = <<<EOF
The [phantom:all|INFO] task launches all unit and functional tests:

  [./symfony phantom:all|INFO]

The task launches all tests found in [test/|COMMENT].

If some tests fail, you can use the [--trace|COMMENT] option to have more
information about the failures:

  [./symfony phantom:all -t|INFO]

Or you can also try to fix the problem by launching them by hand or with the
[test:unit|COMMENT], [test:functional|COMMENT] and [phantom:funcunit|COMMENT] task.

Use the [--only-failed|COMMENT] option to force the task to only execute tests
that failed during the previous run:

  [./symfony phantom:all --only-failed|INFO]

Here is how it works: the first time, all tests are run as usual. But for
subsequent test runs, only tests that failed last time are executed. As you
fix your code, some tests will pass, and will be removed from subsequent runs.
When all tests pass again, the full test suite is run... you can then rinse
and repeat.

The task can output a JUnit compatible XML log file with the [--xml|COMMENT]
options:

  [./symfony phantom:all --xml=log.xml|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    parent::execute($arguments, $options);
    
    $this->runTask('phantom:funcunit', array('frontend'), $options);
  }
}
