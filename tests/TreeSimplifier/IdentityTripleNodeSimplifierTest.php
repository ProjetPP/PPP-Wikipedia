<?php

namespace PPP\Wikipedia\TreeSimplifier;

use PPP\DataModel\MissingNode;
use PPP\DataModel\ResourceListNode;
use PPP\DataModel\StringResourceNode;
use PPP\DataModel\TripleNode;
use PPP\Module\TreeSimplifier\NodeSimplifierBaseTest;
use PPP\Module\TreeSimplifier\NodeSimplifierFactory;

/**
 * @covers PPP\Wikipedia\TreeSimplifier\IdentityTripleNodeSimplifier
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class IdentityTripleNodeSimplifierTest extends NodeSimplifierBaseTest {

	public function buildSimplifier() {
		$mediawikiApiMock = $this->getMock('Mediawiki\Api\MediawikiApi', array(), array(''));
		$mediawikiApiMock->expects($this->any())
			->method('getAction')
			->willReturn(array(
				'query' => array(
					'pages' => array(
						'1' => array(
							'title' => 'Foo',
							'extract' => 'bar'
						)
					)
				)
			));


		return new IdentityTripleNodeSimplifier(
			new NodeSimplifierFactory(),
			$mediawikiApiMock
		);
	}

	/**
	 * @see NodeSimplifierBaseTest::simplifiableProvider
	 */
	public function simplifiableProvider() {
		return array(
			array(
				new TripleNode(
					new StringResourceNode('Foo'),
					new StringResourceNode('Identity'),
					new MissingNode()
				)
			),
			array(
				new TripleNode(
					new StringResourceNode('Foo'),
					new ResourceListNode(array(new StringResourceNode('Identity'))),
					new MissingNode()
				)
			),
		);
	}

	/**
	 * @see NodeSimplifierBaseTest::nonSimplifiableProvider
	 */
	public function nonSimplifiableProvider() {
		return array(
			array(
				new MissingNode()
			),
			array(
				new TripleNode(
					new MissingNode(),
					new StringResourceNode('foo'),
					new StringResourceNode('bar')
				)
			),
			array(
				new TripleNode(
					new StringResourceNode('foo'),
					new MissingNode(),
					new StringResourceNode('bar')
				)
			),
		);
	}

	public function simplificationProvider() {
		return array(
			array(
				new ResourceListNode(array(new StringResourceNode('bar'))),
				new TripleNode(
					new StringResourceNode('Foo'),
					new StringResourceNode('Identity'),
					new MissingNode()
				)
			),
			array(
				new TripleNode(
					new ResourceListNode(array(new StringResourceNode('Foo'))),
					new ResourceListNode(array(new StringResourceNode('Identities'))),
					new MissingNode()
				),
				new TripleNode(
					new StringResourceNode('Foo'),
					new StringResourceNode('Identities'),
					new MissingNode()
				)
			),
		);
	}
}