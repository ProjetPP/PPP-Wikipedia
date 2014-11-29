<?php

namespace PPP\Wikipedia\TreeSimplifier;

use Mediawiki\Api\MediawikiApi;
use PPP\DataModel\AbstractNode;
use PPP\DataModel\MissingNode;
use PPP\DataModel\ResourceListNode;
use PPP\DataModel\StringResourceNode;
use PPP\DataModel\TripleNode;
use PPP\Module\TreeSimplifier\AbstractTripleNodeSimplifier;
use PPP\Module\TreeSimplifier\NodeSimplifierFactory;

/**
 * Simplifies triples with identity predicate
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class IdentityTripleNodeSimplifier extends AbstractTripleNodeSimplifier {

	/**
	 * @var MediawikiApi
	 */
	private $mediawikiApi;

	/**
	 * @param NodeSimplifierFactory $simplifierFactory
	 * @param MediawikiApi $mediawikiApi
	 */
	public function __construct(NodeSimplifierFactory $simplifierFactory, MediawikiApi $mediawikiApi) {
		$this->mediawikiApi = $mediawikiApi;

		parent::__construct($simplifierFactory);
	}

	/**
	 * @see NodeSimplifier::isSimplifierFor
	 */
	public function isSimplifierFor(AbstractNode $node) {
		return $node instanceof TripleNode && $node->getObject() instanceof MissingNode;
	}

	/**
	 * @see AbstractTripleNodeSimplifier::doSimplification
	 * @param ResourceListNode $subjects
	 * @param ResourceListNode $predicates
	 * @param MissingNode $objects
	 */
	protected function doSimplification(AbstractNode $subjects, AbstractNode $predicates, AbstractNode $objects) {
		if(!$this->isPredicateIdentity($predicates)) {
			return new TripleNode($subjects, $predicates, $objects);
		}

		return $this->getDescriptionsForSubjects($subjects);
	}

	protected function isPredicateIdentity(ResourceListNode $predicates) {
		if($predicates->count() !== 1) {
			return false;
		}

		foreach($predicates as $predicate) {
			return strtolower($predicate->getValue()) === 'identity';
		}
	}

	protected function getDescriptionsForSubjects(ResourceListNode $subjects) {
		$result = $this->mediawikiApi->getAction('query', array(
			'titles' => $this->buildTitlesString($subjects),
			'prop' => 'extracts',
			'redirects' => true,
			'exintro' => true,
			'exsectionformat' => 'plain',
			'explaintext' => true
		));

		$descriptions = array();
		foreach($result['query']['pages'] as $pageResult) {
			$descriptions[] = new StringResourceNode($pageResult['extract']);
		}

		return new ResourceListNode($descriptions);
	}

	protected function buildTitlesString(ResourceListNode $subjects) {
		$titles = array();

		foreach($subjects as $subject) {
			$titles[] = $subject->getValue();
		}
		return implode('|', $titles);
	}
}
