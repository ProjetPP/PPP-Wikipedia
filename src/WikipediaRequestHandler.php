<?php

namespace PPP\Wikipedia;

use Mediawiki\Api\MediawikiApi;
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

		return array(new ModuleResponse(
			$request->getLanguageCode(),
			$treeSimplifier->newNodeSimplifier()->simplify($request->getSentenceTree())
		));
	}

	private function getApiForLanguage($languageCode) {
		return new MediawikiApi('https://' . $languageCode . '.wikipedia.org/w/api.php');
	}
}
