<?php

namespace GithubIntegrationBundle\Client;

use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class Client
 */
class Client
{
    /**
     * @var array
     */
    private $settings;

    /**
     * @GuzzleClient array
     */
    private $client;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * Client constructor.
     * @param TokenStorage $tokenStorage
     * @param array $settings
     * @param GuzzleClient $client
     */
    public function __construct(TokenStorage $tokenStorage, array $settings, GuzzleClient $client)
    {
        $this->tokenStorage = $tokenStorage;
        $this->settings = $settings;
        $this->client = $client;
    }

    /**
     * @param string $state
     * @param int $page
     * @return array
     */
    public function getIssuesByState(string $state, int $page = 1): array
    {
        $response = $this->client->get('issues', [
            'query' => [
                'access_token' => $this->tokenStorage->getToken()->getAccessToken(),
                'state' => $state,
                'per_page' => $this->settings['per_page'],
                'page' => $page,
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $state
     * @return int
     */
    public function getPageCountByState(string $state): int
    {
        return ceil($this->getIssuesCountByState($state) / $this->settings['per_page']);
    }

    /**
     * @param string $state
     * @return int
     */
    public function getIssuesCountByState(string $state): int
    {
        $response = $this->client->get('issues', [
            'query' => [
                'access_token' => $this->tokenStorage->getToken()->getAccessToken(),
                'state' => $state,
            ]
        ]);

        return count(json_decode($response->getBody()->getContents(), true));
    }

    /**
     * @param string $owner
     * @param string $repo
     * @param int $number
     * @return array
     */
    public function getIssue(string $owner, string $repo, int $number): array
    {
        $response = $this->client->get(
            'repos/' . $owner . '/' . $repo . '/issues/' . $number, [
            'query' => [
                'access_token' => $this->tokenStorage->getToken()->getAccessToken(),
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $owner
     * @param string $repo
     * @param int $number
     * @return array
     */
    public function getComments(string $owner, string $repo, int $number): array
    {
        $response = $this->client->get(
            'repos/' . $owner . '/' . $repo . '/issues/' . $number . '/comments', [
            'query' => [
                'access_token' => $this->tokenStorage->getToken()->getAccessToken(),
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
