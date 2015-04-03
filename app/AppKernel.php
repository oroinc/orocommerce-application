<?php

use Oro\Bundle\DistributionBundle\OroKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Yaml\Yaml;

class AppKernel extends OroKernel
{
    const APPLICATION_ADMIN    = 'admin';
    const APPLICATION_FRONTEND = 'frontend';
    const APPLICATION_INSTALL  = 'install';
    const APPLICATION_TRACKING = 'tracking';

    /**
     * @var string
     */
    protected $application = self::APPLICATION_ADMIN;

    public function registerBundles()
    {
        $bundles = array(
        // bundles
        );

        if (in_array($this->getEnvironment(), array('dev'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        if (in_array($this->getEnvironment(), array('test'))) {
            $bundles[] = new Oro\Bundle\TestFrameworkBundle\OroTestFrameworkBundle();
        }

        return array_merge(parent::registerBundles(), $bundles);
    }

    /**
     * @return string
     */
    public function getApplication()
    {
        return $this->application;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/'.$this->getApplication().'/config/config_'.$this->getEnvironment().'.yml');
    }

    /**
     * @param string $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    protected function initializeContainer()
    {
        static $first = true;

        if ('test' !== $this->getEnvironment()) {
            parent::initializeContainer();
            return;
        }

        $debug = $this->debug;

        if (!$first) {
            // disable debug mode on all but the first initialization
            $this->debug = false;
        }

        // will not work with --process-isolation
        $first = false;

        try {
            parent::initializeContainer();
        } catch (\Exception $e) {
            $this->debug = $debug;
            throw $e;
        }

        $this->debug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    protected function collectBundles()
    {
        $files = $this->findBundles(
            array(
                $this->getRootDir() . '/../src',
                $this->getRootDir() . '/../vendor'
            )
        );
        $bundles = array();
        $exclusions = array();

        list($bundlesNode, $exclusionsNode) = $this->getNodes();

        foreach ($files as $file) {
            $import = Yaml::parse($file);
            if (!empty($import)) {
                if (!empty($import[$bundlesNode])) {
                    $bundles = array_merge(
                        $bundles,
                        $this->getBundlesMapping($import[$bundlesNode])
                    );
                }
                if (!empty($import[$exclusionsNode])) {
                    $exclusions = array_merge(
                        $exclusions,
                        $this->getBundlesMapping($import[$exclusionsNode])
                    );
                }
            }
        }
        $bundles = array_diff_key($bundles, $exclusions);
        uasort($bundles, array($this, 'compareBundles'));
        return $bundles;
    }

    /**
     * @return array
     */
    protected function getNodes()
    {
        $suffix = '';
        $apps = [self::APPLICATION_ADMIN, self::APPLICATION_TRACKING];

        if (!in_array($this->getApplication(), $apps)) {
            $suffix = '_' . $this->getApplication();
        }

        return [
            'bundles' . $suffix,
            'exclusions' . $suffix
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getKernelParameters()
    {
        return array_merge(parent::getKernelParameters(), ['kernel.application' => $this->getApplication()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return $this->getRootDir().'/../var/cache/'.$this->getApplication().'_'.$this->getEnvironment();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return $this->getRootDir().'/../var/logs';
    }
}
