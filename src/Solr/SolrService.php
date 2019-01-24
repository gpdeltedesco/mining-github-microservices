<?php

namespace App\Solr;

use Psr\Log\LoggerInterface;

class SolrService
{
    /** @var SolrCollectionHandler */
    private $collection;

    /** @var SolrIndexerService */
    private $indexer;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Initializes service.
     *
     * @param   SolrCollectionHandler   $collection
     * @param   SolrIndexerService      $indexer
     * @param   LoggerInterface         $logger
     * @return  void
     */
    public function __construct(
        SolrCollectionHandler $collection,
        SolrIndexerService $indexer,
        LoggerInterface $logger
    ) {
        $this->collection = $collection;
        $this->indexer = $indexer;
        $this->logger = $logger;
    }

    public function run()
    {
        $this->logger->debug('Going to create solr document index.');
        $documents = $this->indexer->buildDocuments();
        $this->logger->debug('{count} documents created.', [ 'count' => count($documents) ]);

        if ($this->collection->exists()) {
            $this->logger->debug('A collection already exists, going to delete it.');
            $this->collection->delete();
            $this->logger->debug('Collection deleted.');
        }

        $this->logger->debug('Going to create new collection.');
        $this->collection->create();
        $this->logger->debug('Collection created.');

        $this->logger->debug('Updating documents to Solr collection.');
        $this->collection->update($documents);
        $this->logger->debug('Documents updated.');
    }
}
