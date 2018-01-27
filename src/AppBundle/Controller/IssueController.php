<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IssueController
 */
class IssueController extends Controller
{
    const STATE_OPEN = 'open';
    const STATE_CLOSED = 'closed';

    /**
     * @Route(
     *     "/{state}",
     *     name="issues",
     *     defaults={"state": "open"},
     *     requirements= {"state": "open|closed"}
     * )
     * @param Request $request
     * @param string $state
     *
     * @return Response
     */
    public function issueListAction(Request $request, string $state): Response
    {
        $issueManager = $this->get('app.manager.issue');
        $currentPage = $request->query->get('page', 1);

        return $this->render(
            '@App/issue/list.html.twig',
            [
                'data' => $issueManager->getList($state, $currentPage),
                'currentPage' => $currentPage,
                'pageCount' => $issueManager->getPageCountByState($state),
                'openIssuesCount' => $issueManager->getIssuesCountByState(self::STATE_OPEN),
                'closedIssuesCount' => $issueManager->getIssuesCountByState(self::STATE_CLOSED),
            ]
        );
    }

    /**
     * @Route(
     *     "/view/{owner}/{repo}/{number}",
     *     name="issue",
     *     requirements= {"number": "\d+"}
     * )
     * @param Request $request
     * @param string $owner
     * @param string $repo
     * @param int $number
     *
     * @return Response
     */
    public function viewAction(Request $request, string $owner, string $repo, int $number): Response
    {
        $issueManager = $this->get('app.manager.issue');

        return $this->render(
            '@App/issue/view.html.twig',
            [
                'data' => $issueManager->getIssue($owner, $repo, $number),
            ]
        );
    }
}
