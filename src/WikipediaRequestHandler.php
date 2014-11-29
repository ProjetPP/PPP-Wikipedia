<?php

namespace PPP\Wikipedia;

use Mediawiki\Api\MediawikiApi;
use PPP\DataModel\AbstractNode;
use PPP\DataModel\ResourceListNode;
use PPP\DataModel\ResourceNode;
use PPP\Module\AbstractRequestHandler;
use PPP\Module\DataModel\ModuleRequest;
use PPP\Module\DataModel\ModuleResponse;
use PPP\Wikipedia\TreeSimplifier\WikipediaNodeSimplifierFactory;

/**
 * Module entry point.
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 * @todo allow more languages
 */
class WikipediaRequestHandler extends AbstractRequestHandler {

	private static $ALLOWED_LANGUAGES = array('en', 'de', 'fr', 'ru', 'zh');

	/**
	 * @see RequestHandler::buildResponse
	 */
	public function buildResponse(ModuleRequest $request) {
		if(!in_array($request->getLanguageCode(), self::$ALLOWED_LANGUAGES)) {
			return array();
		}
		$treeSimplifier = new WikipediaNodeSimplifierFactory($this->getApiForLanguage($request->getLanguageCode()));
		$simplifiedTrees = $this->toOldRepresentation($treeSimplifier->newNodeSimplifier()->simplify($request->getSentenceTree()));

		$responses = array();
		foreach($simplifiedTrees as $tree) {
			$responses[] = new ModuleResponse(
				$request->getLanguageCode(),
				$tree,
				$this->buildMeasures($tree, $request->getMeasures())
			);
		}

		return $responses;
	}

	private function buildMeasures(AbstractNode $node, array $measures) {
		if($node instanceof ResourceNode) {
			$measures['relevance'] = 1;
		}

		return $measures;
	}

	/**
	 * @todo move to new representation
	 */
	private function toOldRepresentation(AbstractNode $node) {
		if($node instanceof ResourceListNode) {
			return iterator_to_array($node);
		} else {
			return array($node);
		}
	}

	private function getApiForLanguage($languageCode) {
		return new MediawikiApi('https://' . $languageCode . '.wikipedia.org/w/api.php');
	}
}
