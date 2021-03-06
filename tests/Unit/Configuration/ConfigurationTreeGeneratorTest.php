<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Configuration;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\ConfigurationTreeGenerator;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigurationTreeGeneratorTest extends TestCase
{
    /** @test */
    public function getConfigTreeBuilder_noParameters_treeBuilderReturned(): void
    {
        $configuration = new ConfigurationTreeGenerator();

        $treeBuilder = $configuration->getConfigTreeBuilder();

        $this->assertInstanceOf(TreeBuilder::class, $treeBuilder);
    }
}
