<?php
/**
 * Created by PhpStorm.
 * User: Exodus 4D
 * Date: 28.01.2019
 * Time: 22:38
 */

namespace Exodus4D\ESI\Client\GitHub;

use Exodus4D\ESI\Client;
use Exodus4D\ESI\Config\ConfigInterface;
use Exodus4D\ESI\Config\GitHub\Config;
use Exodus4D\ESI\Mapper;

class GitHub extends Client\AbstractApi implements GitHubInterface {

    /**
     * @param string $projectName e.g. "exodus4d/pathfinder"
     * @param int $count
     * @return array
     */
    public function getProjectReleases(string $projectName, int $count = 1) : array {
        $uri = $this->getConfig()->getEndpoint(['releases', 'GET'], [$projectName]);
        $releasesData = [];

        $requestOptions = [
            'query' => [
                'page' => 1,
                'per_page' => $count
            ]
        ];

        $response = $this->request('GET', $uri, $requestOptions)->getContents();

        if(!$response->error){
            foreach((array)$response as $data){
                $releasesData[] = (new Mapper\GitHub\Release($data))->getData();
            }
        }

        return $releasesData;
    }

    /**
     * @param string $context
     * @param string $markdown
     * @return string
     */
    public function markdownToHtml(string $context, string $markdown) : string {
        $uri = $this->getConfig()->getEndpoint(['markdown', 'POST']);
        $html = '';

        $requestOptions = [
            'json_enabled' => false, // disable JSON Middleware
            'json' => [
                'text' => $markdown,
                'mode' => 'gfm',
                'context' => $context
            ]
        ];

        $response = $this->request('POST', $uri, $requestOptions)->getContents();

        if(!$response->error){
            $html = (string)$response;
        }

        return $html;
    }

    /**
     * @return ConfigInterface
     */
    protected function getConfig() : ConfigInterface {
        return ($this->config instanceof ConfigInterface) ? $this->config : $this->config = new Config();
    }
}