<?php

namespace PPP\Wikipedia;

use PPP\DataModel\MissingNode;
use PPP\DataModel\ResourceListNode;
use PPP\DataModel\StringResourceNode;
use PPP\DataModel\TripleNode;
use PPP\Module\DataModel\ModuleRequest;
use PPP\Module\DataModel\ModuleResponse;

/**
 * @covers PPP\Wikipedia\WikipediaRequestHandler
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class WikipediaRequestHandlerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider requestAndResponseProvider
	 */
	public function testBuildResponse(ModuleRequest $request, array $response) {
		$requestHandler = new WikipediaRequestHandler();
		$this->assertEquals($response, $requestHandler->buildResponse($request));
	}

	public function requestAndResponseProvider() {
		return array(
			array(
				new ModuleRequest(
					'en',
					new MissingNode(),
					'a'
				),
				array(new ModuleResponse(
					'en',
					new MissingNode()
				))
			),
			array(
				new ModuleRequest(
					'fr',
					new TripleNode(
						new StringResourceNode('Léon de la Brière'),
						new StringResourceNode('Identity'),
						new MissingNode()
					),
					'a'
				),
				array(new ModuleResponse(
					'fr',
					new StringResourceNode('Léon Leroy de la Brière (14 janvier 1845 - 12 septembre 1899) est un écrivain politique français de la fin du XIXe siècle.'),
					array(
						'relevance' => 1
					)
				)),
			)
		);
	}
}
