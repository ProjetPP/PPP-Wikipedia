<?php

namespace PPP\Wikipedia\TreeSimplifier;

use Mediawiki\Api\MediawikiApi;
use PPP\Module\TreeSimplifier\NodeSimplifierFactory;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class WikipediaNodeSimplifierFactory extends NodeSimplifierFactory {

	/**
	 * @param MediawikiApi $mediawikiApi
	 */
	public function __construct(MediawikiApi $mediawikiApi) {
		$this->mediawikiApi = $mediawikiApi;

		parent::__construct(array(
			new IdentityTripleNodeSimplifier($this, $mediawikiApi)
		));
	}
}
