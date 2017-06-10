<?php
namespace TeamNeusta\PushMe\Services;


use Symfony\Component\Filesystem\Exception\IOException;
use TeamNeusta\PushMe\Services\Provider\File;

class ConfigService
{
    /**
     * configuration file name.
     */
    const CONFIGURATION_FILE_NAME = '.pushme';

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $fs;

    /**
     * ConfigService constructor.
     *
     * @param \Symfony\Component\Filesystem\Filesystem|null $fs
     */
    public function __construct(
        \Symfony\Component\Filesystem\Filesystem $fs = null,
        File $file = null
    )
    {
        $this->fs = $fs ?? new \Symfony\Component\Filesystem\Filesystem();
        $this->file = $file ?? new File();
    }

    /**
     *
     */
    public function createEmptyConfiguration()
    {
        $this->fs->touch('./' . self::CONFIGURATION_FILE_NAME);
    }

    /**
     * @param $projectName
     * @param $endpoint
     */
    public function addData($projectName, $endpoint, $branch, $version)
    {
        $data = [
            'projectname' => $projectName,
            'endpoint' => $endpoint,
            'branch' => $branch,
            'version_endpoint' => $version
        ];
        $this->fs->dumpFile('./' . self::CONFIGURATION_FILE_NAME, json_encode($data));

    }

    /**
     * @return bool
     */
    public function configExist()
    {
        return file_exists('./' . self::CONFIGURATION_FILE_NAME);
    }

    /**
     * Retrieve project configuration.
     *
     * @return bool|array
     */
    public function getConfiguration() : array
    {
        $filename = './' . self::CONFIGURATION_FILE_NAME;
        $config = $this->getConfigurationFileContent($filename);
        return $config;
    }

    /**
     * @param $fileName
     * @param bool $createIfNotExist
     * @return array|bool|mixed|null
     * @throws IOException
     */
    public function getConfigurationFileContent($fileName, $createIfNotExist = true)
    {
        if (!$this->fs->exists($fileName) && $createIfNotExist) {
            try {
                // generate a empty array for configuration
                $defaults = [
                    'projectname' => '',
                    'endpoint' => false
                ];
                $this->fs->dumpFile($fileName, json_encode($defaults));
            } catch (\Exception $e) {
                throw new IOException($e->getMessage());
            }
        }
        $config = json_decode($this->file->getContents($fileName), true);
        if (is_null($config)) {
            $config = false;
            return $config;
        }
        return $config;
    }

    /**
     * @return mixed
     */
    public function getEndpoint()
    {
        $configuration = $this->getConfiguration();

        return $configuration['endpoint'];
    }

    public function getProject()
    {
        $configuration = $this->getConfiguration();

        return $configuration['projectname'];
    }

    public function getBranch ()
    {
        $configuration = $this->getConfiguration();

        return $configuration['branch'];
    }

    public function getVersion ()
    {
        $configuration = $this->getConfiguration();

        return $configuration['version_endpoint'];
    }
}