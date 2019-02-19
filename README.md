# GitHub microservices miner - GraphQL version

Fetches repositories data, using [GitHub's GraphQL API], and keeps a local index for
further analysis.

[GitHub's GraphQL API]: https://developer.github.com/v4/

## What does it do?

The program obtains data in two stages:
  - **index**: Fetches, given a seto of GraphQL queries, a list of repositories data.
  - **fetch**: Fetches, for every indexed repository, it's corresponding git repository.

Also, it pushes an abstract of collected data to [Solr], creating a collection ready
to be queried.

[Solr]: http://lucene.apache.org/solr/

## Usage with [Docker]

[Docker]: https://www.docker.com

This tool is ready for usage with docker compose. It will configure two services:

 - `php`: based on the [official php image](https://hub.docker.com/_/php/), includes
   the tool and dependencies.

 - `solr`: with the [official Apache Solr image](https://hub.docker.com/_/solr/),
   configured for launch in cloud mode.

### Requirements

This version requires:

 - [Docker 17.05+](https://www.docker.com)
 - [docker-compose](https://docs.docker.com/compose/)

### Install

 1. Clone this repo, and go to project directory
    ```
    git clone https://github.com/gpdeltedesco/mining-github-microservices
    cd mining-github-microservices
    ```

 2. Configure application, setting (at least) your OAuth token
    ```
    cp config.ini.dist config.ini
    sensible-editor config.ini
    ```

Everything else will be built/configured on first run:

```
docker-compose up
```

If you make config changes
later, please remember to rebuild the `php` container, executing:

```
docker-compose build php
```

### Run

The main executable lives in the `php` container. Run `docker-compose run php bin/miner`
for a list of available commands.

Note that, for persistente, a `runtime` directory will be created. This directory,
and any file on it, will be owned by the `root` user.

## Usage from CLI (development)

### Requirements

This version requires:

 - [SQLite 3.9+](https://www.sqlite.org), with JSON support.
 - [PHP 7.1.3+](http://php.net)
 - [Composer](https://getcomposer.org/)
 - A [GitHub OAuth token](https://help.github.com/articles/creating-an-access-token-for-command-line-use/)

### Install

 1. Clone this repo, and go to project directory
    ```
    git clone https://github.com/gpdeltedesco/mining-github-microservices
    cd mining-github-microservices
    ```

 2. Create `runtime` directory (for local data storage, and log)
    ```
    mkdir runtime
    ```

 3. Create and provision database
    ```
    sqlite3 runtime/store.sqlite < database.sql
    ```

 4. Install composer dependencies
    ```
    composer install
    ```

 5. Configure application, setting (at least) your OAuth token
    ```
    cp config.ini.dist config.ini
    sensible-editor config.ini
    ```

You are ready to go!

### Run

Execute `bin/miner` for a list of commands.
