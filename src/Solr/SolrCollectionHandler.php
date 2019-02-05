<?php

namespace App\Solr;

use RuntimeException;
use ErrorException;

class SolrCollectionHandler
{
    /** @var string Collection name */
    private $name;

    /** @var string Solr endpoint, for API requests */
    private $endpoint;

    /**
     * Initializes this service, injecting required dependencies.
     *
     * @param   string      $endpoint   Solr endpoint (http://<host>:<port>)
     * @param   string      $name       Collection
     * @return  void
     */
    public function __construct($endpoint, $name)
    {
        $this->endpoint = $endpoint;
        $this->name = $name;
    }

    /**
     * Blindly creates the collection on solr.
     *
     * @return  void
     */
    public function create()
    {
        $this->call('admin/collections?action=CREATE&numShards=1&name='.$this->name);
    }

    /**
     * Blindly updates a collection of documents to collection.
     *
     * @see https://lucene.apache.org/solr/guide/7_6/uploading-data-with-index-handlers.html#adding-multiple-json-documents
     * @param   array   $documents      Array of documents (will be json encoded)
     * @return  void
     */
    public function update(array $documents)
    {

      foreach($documents as $document){
        $streamOptions = [
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                ],
                'content' => json_encode([$document])
            ],
        ];

        $endpoint = $this->endpoint . "/solr/" . $this->name . "/update?commit=true";
        /** @var string|false $data */
        $data = file_get_contents(
            $endpoint,
            false,
            stream_context_create($streamOptions)
        );
      }


    }

    /**
     * Blindly deletes the collection on solr.
     *
     * @return  void
     */
    public function delete()
    {
        $this->call('admin/collections?action=DELETE&name='.$this->name);
    }

    /**
     * Tells if collection already exists in solr.
     *
     * @return  boolean
     */
    public function exists()
    {
        $result = $this->call('admin/collections?action=LIST');

        if (isset($result['collections'])) {
            return in_array($this->name, $result['collections']);
        }

        throw new RuntimeException('Unable to query for collections list');
    }

    /**
     * Executes a raw api query, and returns json parsed response.
     *
     * @param   string  $call   API call, relative to Solr endpoint
     * @return  array
     */
    private function call($call)
    {
        /** @var array $streamOptions */
        $streamOptions = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Content-Type: application/json',
                ],
            ],
        ];

        $endpoint = $this->endpoint . "/solr/" . $call;

        /** @var string|false $data */
        $data = @file_get_contents(
            $endpoint,
            false,
            stream_context_create($streamOptions)
        );

        // Catch low level errors
        if ($data === false) {
            $error = error_get_last();
            throw new ErrorException($error['message'], $error['type']);
        }

        return json_decode($data, true);
    }
}
