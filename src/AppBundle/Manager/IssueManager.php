<?php

namespace AppBundle\Manager;

use GithubIntegrationBundle\Client\Client;

/**
 * Class IssueManager
 */
class IssueManager
{
    /**
     * @var Client
     */
    private $client;

    /**
     * IssueManager constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $state
     * @param int $page
     *
     * @return array
     */
    public function getList(string $state, int $page = 1): array
    {
        $issues = $this->client->getIssuesByState($state, $page);
        $result = $this->parseIssuesList($issues);

        return $result;
    }

    /**
     * @param string $owner
     * @param string $repo
     * @param int $number
     *
     * @return array
     */
    public function getIssue(string $owner, string $repo, int $number): array
    {
        $issue = $this->client->getIssue($owner, $repo, $number);
        $result = $this->parseIssue($issue);

        $comments = $this->client->getComments($owner, $repo, $number);
        $result['comments'] = $this->parseComments($comments);

        return $result;
    }

    /**
     * @param string $state
     * @return int
     */
    public function getPageCountByState(string $state): int
    {
        return $this->client->getPageCountByState($state);
    }

    /**
     * @param string $state
     * @return int
     */
    public function getIssuesCountByState(string $state): int
    {
        return $this->client->getIssuesCountByState($state);
    }

    /**
     * @param array $issues
     *
     * @return array
     */
    private function parseIssuesList(array $issues): array
    {
        return array_map(function ($issue) {
            $result = [
                'number' => $issue['number'],
                'title' => $issue['title'],
                'state' => $issue['state'],
                'commentsCount' => $issue['comments'],
                'createdAt' => $issue['created_at'],
                'user' => $issue['user']['login'],
                'repo' => $issue['repository']['name'],
                'owner' => $issue['repository']['owner']['login'],
            ];

            return $result;
        }, $issues);
    }

    /**
     * @param array $issue
     * @return array
     */
    private function parseIssue(array $issue): array
    {
        return [
            'number' => $issue['number'],
            'title' => $issue['title'],
            'state' => $issue['state'],
            'commentsCount' => $issue['comments'],
            'createdAt' => $issue['created_at'],
            'user' => $issue['user']['login'],
        ];
    }

    /**
     * @param array $comments
     * @return array
     */
    private function parseComments(array $comments): array
    {
        return array_map(function ($issue) {
            $result = [
                'user' => $issue['user']['login'],
                'avatar' => $issue['user']['avatar_url'],
                'createdAt' => $issue['created_at'],
                'body' => $issue['body'],
            ];

            return $result;
        }, $comments);
    }
}