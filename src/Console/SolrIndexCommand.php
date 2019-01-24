<?php

namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Solr\SolrService;

class SolrIndexCommand extends Command
{
    /** @var SolrService */
    private $solr;

    /**
     * Initializes the command, injecting required dependencies.
     *
     * @return  void
     */
    public function __construct(SolrService $solr)
    {
        $this->solr = $solr;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('solr-index')
            ->setDescription('Pushes locally collected data to Solr')
            ->setHelp('This command creates (or overrides) a configured collection on Solr.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->solr->run();
    }
}
