<?php

namespace KenticoCloud\Tests\Unit;

use KenticoCloud\Delivery\{
    TypeMapperInterface,
    ContentLinkUrlResolverInterface, 
    ModelBinder,
    DefaultMapper
};

use PHPUnit\Framework\TestCase;



class ModelBinderTest extends TestCase
{
    public function test_BindModel_LinksCorrectlyResolved()
    {  
        $defaultMapper = new DefaultMapper();

        $contentLinkUrlResolver = $this->createMock(ContentLinkUrlResolverInterface::class);
        $contentLinkUrlResolver
            ->method('resolveLinkUrl')
            ->will($this->returnCallback(function($link){
                return "/custom/" . $link->urlSlug;
            }));
        $contentLinkUrlResolver
            ->method('resolveBrokenLinkUrl')
            ->willReturn("/404");
        $modelBinder = new ModelBinder($defaultMapper, $defaultMapper, $defaultMapper, $contentLinkUrlResolver);

        $itemJson = file_get_contents('./tests/Unit/Data/ContentItemWithRichTextContainingBrokenAndNonbrokenLinks.json');
        $data = json_decode($itemJson);

        $model = $modelBinder->bindModel(\KenticoCloud\Delivery\Models\Items\ContentItem::class, $data);

        $this->assertContains('<a data-item-id="00000000-0000-0000-0000-000000000002" href="/custom/link-1">Link 1</a>', $model->bodyCopy);
        $this->assertContains('<a data-item-id="00000000-0000-0000-0000-000000000003" href="/custom/link-2">Link 2</a>', $model->bodyCopy);        
        $this->assertContains('<a data-item-id="FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF" href="/404">404</a>', $model->bodyCopy);        
    }
} 