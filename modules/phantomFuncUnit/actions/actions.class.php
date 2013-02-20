<?php

/**
 * widgetActiveDay actions.
 *
 * @package    geonaute
 * @subpackage widgetActiveDay
 * @author     StadLine
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class phantomFuncUnitActions extends sfActions
{
    public function preExecute()
    {
        $this->forward404Unless(sfConfig::get('phantom_enabled', false), "PhantomTest is not enabled");
    }
    
    /**
     * Executes test action
     *
     * @param sfRequest $request A request object
     */
    public function executeTest(sfWebRequest $request)
    {
        $this->setLayout(false);
        
        $application = sfConfig::get('sf_app');
        $testDirectory = sfConfig::get('sf_root_dir').'/'.sfConfig::get('phantom_funcunit_test_directory');
        $url = $request->getParameter('url');
        $path = $testDirectory.'/'.$application.'/'.$url;
        
        if (is_readable($path)) {
            $this->content = file_get_contents($testDirectory.'/'.$application.'/'.$url);
        }
        else {
            $this->forward404("$path is not readable");
        }
    }

}
