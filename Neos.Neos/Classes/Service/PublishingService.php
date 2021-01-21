<?php
namespace Neos\Neos\Service;

/*
 * This file is part of the Neos.Neos package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\Model\Workspace;

/**
 * The workspaces service adds some basic helper methods for getting workspaces,
 * unpublished nodes and methods for publishing nodes or whole workspaces.
 *
 * @api
 * @Flow\Scope("singleton")
 */
class PublishingService extends \Neos\ContentRepository\Domain\Service\PublishingService
{
    /**
     * Publishes the given node to the specified target workspace. If no workspace is specified, the base workspace
     * is assumed.
     *
     * If the given node is a Document or has ContentCollection child nodes, these nodes are published as well.
     *
     * @param NodeInterface $node
     * @param Workspace $targetWorkspace If not set the base workspace is assumed to be the publishing target
     * @return void
     * @api
     */
    public function publishNode(NodeInterface $node, Workspace $targetWorkspace = null): void
    {
        if ($targetWorkspace === null) {
            $targetWorkspace = $node->getWorkspace()->getBaseWorkspace();
        }
        if (!$targetWorkspace instanceof Workspace) {
            return;
        }
        $nodes = [$node];
        $nodeType = $node->getNodeType();

        if ($nodeType->isOfType('Neos.Neos:Document') || $nodeType->hasConfiguration('childNodes')) {
            $this->collectAllContentChildNodes($node, $nodes, $node->getWorkspace());
        }
        $sourceWorkspace = $node->getWorkspace();
        $sourceWorkspace->publishNodes($nodes, $targetWorkspace);

        $this->emitNodePublished($node, $targetWorkspace);
    }

    /**
     * Discards the given node from its workspace.
     *
     * If the given node is a Document or has ContentCollection child nodes, these nodes are discarded as well.
     *
     * @param NodeInterface $node
     */
    public function discardNode(NodeInterface $node): void
    {
        $nodes = [$node];
        $nodeType = $node->getNodeType();

        if ($nodeType->isOfType('Neos.Neos:Document') || $nodeType->hasConfiguration('childNodes')) {
            $this->collectAllContentChildNodes($node, $nodes, $node->getWorkspace());
        }

        $this->discardNodes($nodes);
    }

    /**
     * @param NodeInterface $parentNode
     * @param array $collectedNodes
     * @param Workspace $workspace
     */
    protected function collectAllContentChildNodes(NodeInterface $parentNode, array &$collectedNodes, Workspace $workspace): void
    {
        // !!! Alternative: only include auto created childnodes of type ContentCollection?
        // !!! Verify: does this cause a performance issue when publishing lots of nodes with lots of content?
        foreach ($parentNode->getChildNodes('!Neos.Neos:Document') as $contentNode) {
            if ($contentNode->getWorkspace()->getName() === $workspace->getName()) {
                $collectedNodes[] = $contentNode;
            }
//            $this->collectAllContentChildNodes($contentNode, $collectedNodes, $workspace);
        }
    }
}
