<?php

namespace PPP\Wikipedia\TreeSimplifier;

use InvalidArgumentException;
use Mediawiki\Api\MediawikiApi;
use PPP\DataModel\AbstractNode;
use PPP\DataModel\MissingNode;
use PPP\DataModel\ResourceListNode;
use PPP\DataModel\ResourceNode;
use PPP\DataModel\SentenceNode;
use PPP\DataModel\StringResourceNode;
use PPP\DataModel\TripleNode;
use PPP\Module\TreeSimplifier\NodeSimplifier;

/**
 * Simplifies triples with identity predicate or sentence nodes
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class IdentityTripleNodeSimplifier implements NodeSimplifier {

	/**
	 * @var MediawikiApi
	 */
	private $mediawikiApi;

	/**
	 * @param MediawikiApi $mediawikiApi
	 */
	public function __construct(MediawikiApi $mediawikiApi) {
		$this->mediawikiApi = $mediawikiApi;
	}

	/**
	 * @see NodeSimplifier::isSimplifierFor
	 */
	public function isSimplifierFor(AbstractNode $node) {
		return $node instanceof SentenceNode || (
			$node instanceof TripleNode &&
			$node->getSubject() instanceof ResourceListNode &&
			$node->getPredicate() instanceof ResourceListNode &&
			$node->getObject() instanceof MissingNode
		);
	}

	/**
	 * @see NodeSimplifier::doSimplification
	 */
	public function simplify(AbstractNode $node) {
		if(!$this->isSimplifierFor($node)) {
			throw new InvalidArgumentException('IdentityTripleNodeSimplifier can only simplify TripleNode with a missing object');
		}

		if($node instanceof SentenceNode) {
			return $this->getDescriptionsForSubjects(new ResourceListNode(array(new StringResourceNode($node->getValue()))));
		} else {
			return $this->doSimplification($node);
		}
	}

	private function doSimplification(TripleNode $node) {
		if(!$this->isPredicateIdentity($node->getPredicate())) {
			return $node;
		}

		return $this->getDescriptionsForSubjects($node->getSubject());
	}

	protected function isPredicateIdentity(ResourceListNode $predicates) {
		if($predicates->count() !== 1) {
			return false;
		}

		/** @var ResourceNode $resource */
		return strtolower($predicates->getIterator()->current()->getValue()) === 'identity';
	}

	protected function getDescriptionsForSubjects(ResourceListNode $subjects) {
		$result = $this->mediawikiApi->getAction('query', array(
			'titles' => $this->buildTitlesString($subjects),
			'prop' => 'extracts',
			'redirects' => true,
			'exintro' => true,
			'exsectionformat' => 'plain',
			'explaintext' => true,
			'exsentences' => 3
		));

		$descriptions = array();
		foreach($result['query']['pages'] as $pageResult) {
			$descriptions[] = new StringResourceNode($pageResult['extract']);
		}

		return new ResourceListNode($descriptions);
	}

	protected function buildTitlesString(ResourceListNode $subjects) {
		$titles = array();

		/** @var ResourceNode $subject */
		foreach($subjects as $subject) {
			$titles[] = $subject->getValue();
		}
		return implode('|', $titles);
	}
}
