<?php

namespace MultiTester;

class Config
{
    /**
     * @var MultiTester
     */
    public $tester;

    /**
     * @var string
     */
    public $configFile;

    /**
     * @var array|File
     */
    public $config;

    /**
     * @var array|File
     */
    public $projects;

    /**
     * @var string
     */
    public $projectDirectory;

    /**
     * @var string
     */
    public $composerFile;

    /**
     * @var array|File
     */
    public $data;

    /**
     * @var string
     */
    public $packageName;

    /**
     * @var bool
     */
    public $verbose;

    /**
     * Config constructor.
     *
     * @param MultiTester $multiTester
     * @param array       $arguments
     *
     * @throws MultiTesterException
     */
    public function __construct(MultiTester $multiTester, array $arguments)
    {
        $this->tester = $multiTester;
        $this->verbose = in_array('--verbose', $arguments) || in_array('-v', $arguments);
        $arguments = array_filter($arguments, function ($argument) {
            return $argument !== '--verbose' && $argument !== '-v';
        });
        $this->configFile = isset($arguments[1]) ? $arguments[1] : $multiTester->getMultiTesterFile();

        if (!file_exists($this->configFile)) {
            throw new MultiTesterException("Multi-tester config file '$this->configFile' not found.");
        }

        $config = new File($this->configFile);
        $this->projects = isset($config['projects']) ? $config['projects'] : $config;
        $this->config = isset($config['config']) ? $config['config'] : $config;

        $base = dirname(realpath($this->configFile));
        $this->projectDirectory = isset($this->config['directory'])
            ? rtrim($base, '/\\') . DIRECTORY_SEPARATOR . ltrim($this->config['directory'], '/\\')
            : $base;
        $this->composerFile = $this->projectDirectory . '/composer.json';
        if (!file_exists($this->composerFile)) {
            throw new MultiTesterException("Set the 'directory' entry to a path containing a composer.json file.");
        }
        $this->data = new File($this->composerFile);
        if (!isset($this->data['name'])) {
            throw new MultiTesterException("The composer.json file must contains a 'name' entry.");
        }
        $this->packageName = $this->data['name'];
    }

    /**
     * @return MultiTester
     */
    public function getTester()
    {
        return $this->tester;
    }
}