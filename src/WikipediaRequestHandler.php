<?php

namespace PPP\Wikipedia;

use Mediawiki\Api\MediawikiApi;
use PPP\DataModel\AbstractNode;
use PPP\DataModel\ResourceListNode;
use PPP\Module\AbstractRequestHandler;
use PPP\Module\DataModel\ModuleRequest;
use PPP\Module\DataModel\ModuleResponse;
use PPP\Module\TreeSimplifier\NodeSimplifierFactory;
use PPP\Wikipedia\TreeSimplifier\IdentityTripleNodeSimplifier;

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

		$treeSimplifier = new NodeSimplifierFactory(array(
			new IdentityTripleNodeSimplifier($this->getApiForLanguage($request->getLanguageCode()))
		));
		$simplifiedTree = $treeSimplifier->newNodeSimplifier()->simplify($request->getSentenceTree());

		return array(new ModuleResponse(
			$request->getLanguageCode(),
			$simplifiedTree,
			$this->buildMeasures($simplifiedTree, $request->getMeasures())
		));
	}

	private function buildMeasures(AbstractNode $node, array $measures) {
		if($node instanceof ResourceListNode) {
			$measures['relevance'] = 1;
		}

		return $measures;
	}

	private function getApiForLanguage($languageCode) {
		return new MediawikiApi('https://' . $languageCode . '.wikipedia.org/w/api.php');
	}
}
