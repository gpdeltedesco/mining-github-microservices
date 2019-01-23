<?php

namespace App\Solr;

use App\Index\IndexStore;

/**
 * Builds an index of Solr documents.
 */
class SolrIndexerService
{
    /** @var IndexStore */
    private $store;

    /** @var string */
    private $repositoriesPath;

    /**
     * Initializes this service, injecting required dependencies.
     *
     * @param   IndexStore  $store
     * @param   string      $repositoriesPath
     * @return  void
     */
    public function __construct(IndexStore $store, $repositoriesPath)
    {
        $this->store = $store;
        $this->repositoriesPath = $repositoriesPath;
    }

    /**
     * Creates the collection of solr documents.
     *
     * @return array
     */
    public function buildDocuments()
    {
        $repositories = $this->store->getRepositories();
        $documents = [];

        foreach ($repositories as $repo) {
            $documents[] = $this->buildSolrDocument($repo);
        }

        return $documents;
    }

    /**
     * Builds a single document, given repository metadata.
     *
     * @param   array   $repository
     * @return  array
     */
    private function buildSolrDocument(array $repository)
    {
        // Put all metadata obtained from GitHub
        $document = json_decode($repository['gh_metadata'], true);

        // Obtain readme file (or `null`)
        $readmeFile = $this->getReadmeFile($repository);

        if ($readmeFile === null) {
            $document['readmeFile'] = null;
            $document['readmeText'] = null;
        } else {
            $document['readmeFile'] = basename($readmeFile);
            $document['readmeText'] = file_get_contents($readmeFile);
        }

        return $document;
    }

    /**
     * Returns absolute readme file path, or `null`, for given repository.
     *
     * @param   array   $repository
     * @return  string|null
     */
    private function getReadmeFile(array $repository)
    {
        // Return null, if no git path defined (not fetched)
        if (empty($repository['git_path'])) {
            return null;
        }

        // Compute absolute path
        $path = $this->repositoriesPath . "/" . $repository['git_path'];

        // Valid readme file names
        $files = [
            "README.md", "README.MD", "README.markdown", "README.txt", "README.rst",
            "readme.md", "readme.MD", "readme.markdown", "readme.txt", "readme.rst",
            "Readme.md", "Readme.MD", "Readme.markdown", "Readme.txt", "Readme.rst",
        ];

        $readmeFile = null;

        foreach ($files as $file) {
            $absoluteFilePath = "$path/$file";

            if (is_file($absoluteFilePath) && is_readable($absoluteFilePath)) {
                $readmeFile = $absoluteFilePath;
                break;
            }
        }

        return $readmeFile;
    }
}
