<?php

namespace PPP\Wikipedia\TreeSimplifier;
use Mediawiki\Api\MediawikiApi;

/**
 * @covers PPP\Wikipedia\TreeSimplifier\WikibaseNodeSimplifierFactory
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class WikibaseNodeSimplifierFactoryTest extends \PHPUnit_Framework_TestCase {

	public function testNewSentenceTreeSimplifier() {
		$factory = new WikipediaNodeSimplifierFactory(new MediawikiApi(''));

		$this->assertInstanceOf(
			'PPP\Module\TreeSimplifier\NodeSimplifier',
			$factory->newNodeSimplifier()
		);
	}
}
