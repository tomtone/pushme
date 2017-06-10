<?php
namespace TeamNeusta\PushMe\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use TeamNeusta\PushMe\Services\ConfigService;

class InitCommand extends Command
{
    /**
     * @var ConfigService
     */
    private $configService;

    /**
     * Constructor.
     *
     * @param ConfigService|null $configService
     * @param string|null $name The name of the command; passing null means it must be set in configure()
     */
    public function __construct(ConfigService $configService = null, $name = null)
    {
        parent::__construct($name);
        $this->configService = $configService ?? new ConfigService();
    }

    /**
     * Announce name and description so command could be called.
     *
     * @codeCoverageIgnore
     */
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('interactively init pushme');
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
        if(!$this->configService->configExist()) {
            $helper = new QuestionHelper();
            $question = new Question("Create empty config file? [yes] ", 'yes');
            $question->setAutocompleterValues(['yes', 'no', 'y', 'n']);
            $question->setValidator(function ($answer) {
                if (!in_array($answer, ['yes', 'no', 'y', 'n'])) {
                    throw new \RuntimeException(
                        'Valid Answers are: yes, no, y, n'
                    );
                }
                if (in_array($answer, ['no', 'n'])) {
                    return false;
                } else {
                    return true;
                }
            });
            $createConfigFile = $helper->ask($input, $output, $question);

            if ($createConfigFile) {
                $this->configService->createEmptyConfiguration();
            }
            $output->writeln("Config .push me created.");
        }else{
            $output->writeln("Config .pushme already exist, skipping...");
        }

        $helper = new QuestionHelper();
        $question = new Question("Projectname? ");
        $projectName = $helper->ask($input, $output, $question);

        $helper = new QuestionHelper();
        $question = new Question("Listing Service Endpoint? [http://some.tld/pushme] ", 'http://some.tld/pushme');
        $endpoint = $helper->ask($input, $output, $question);

        $helper = new QuestionHelper();
        $question = new Question("Branch to diff to? [develop] ", 'develop');
        $branch = $helper->ask($input, $output, $question);

        $helper = new QuestionHelper();
        $question = new Question("Where to get latest released Version? [http://some.tld/pushme/tag] ", 'http://some.tld/pushme/tag');
        $version = $helper->ask($input, $output, $question);


        $this->configService->addData($projectName, $endpoint, $branch, $version);

        $output->writeln("You're all set. Get to work now!");

        return 0;
    }
}