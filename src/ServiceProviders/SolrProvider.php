<?php

namespace App\ServiceProviders;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use App\Solr\SolrCollectionHandler;
use App\Solr\SolrIndexerService;
use App\Solr\SolrService;

class SolrProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['solr_collection'] = function ($container) {
            return new SolrCollectionHandler(
                $container['settings']['solr.endpoint'],
                $container['settings']['solr.collection']
            );
        };

        $container['solr_indexer_service'] = function ($container) {
            return new SolrIndexerService(
                $container['index_store'],
                $container['settings']['app.path.repositories']
            );
        };

        $container['solr_service'] = function ($container) {
            return new SolrService(
                $container['solr_collection'],
                $container['solr_indexer_service'],
                $container['logger']
            );
        };
    }
}
