<?php
namespace TeamNeusta\PushMe\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use TeamNeusta\PushMe\DTO\Commit;
use TeamNeusta\PushMe\Services\CommitService;
use TeamNeusta\PushMe\Services\ConfigService;
use TeamNeusta\PushMe\Services\GitService;
use TeamNeusta\PushMe\Services\PushService;

class PushCommand extends Command
{
    /**
     * @var null|string|CommitService
     */
    private $name;
    /**
     * @var CommitService
     */
    private $commitService;
    /**
     * @var PushService
     */
    private $pushService;
    /**
     * @var ConfigService
     */
    private $configService;
    /**
     * @var GitService
     */
    private $gitService;

    /**
     * Constructor.
     *
     * @param CommitService $commitService
     * @param PushService $pushService
     * @param ConfigService $configService
     * @param GitService $gitService
     * @param string|null $name The name of the command; passing null means it must be set in configure()
     */
    public function __construct(
        CommitService $commitService,
        PushService $pushService,
        ConfigService $configService,
        GitService $gitService,
        $name = null
    )
    {
        parent::__construct($name);
        $this->commitService = $commitService;
        $this->pushService = $pushService;
        $this->configService = $configService;
        $this->gitService = $gitService;
    }

    /**
     * Announce name and description so command could be called.
     *
     * @codeCoverageIgnore
     */
    protected function configure()
    {
        $this
            ->setName('push')
            ->setDescription('push changes to remote');
    }
    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $branch = $this->configService->getBranch();
        $version = $this->getLatestVersion();
        exec("git log --pretty=format:'[hash=%h][branch=%d][message=%s][committed=%ct][author_name=%an][author_mail=%ae]' --abbrev-commit --date=relative $version..$branch", $returnValue);

        /** @var Commit[] $commitMessages */
        $commits = $this->commitService->prepareMessages($returnValue);

        $this->pushService->pushCommits($version, $commits);

        $output->writeln('durch ?!');
        return 0;
    }

    /**
     * @return string|void
     */
    private function getLatestVersion ()
    {
        $url = $this->configService->getVersion();
        $project = $this->configService->getProject();
        $client = new Client();
        $request = new Request('GET', $url . $project);
        try {
            $response = $client->send ( $request );
            $version = $response->getBody ()->getContents ();
        }catch (\GuzzleHttp\Exception\ClientException $e){
            $version = $this->gitService->getVersionFromGit();
        }

        return $version;
    }
}