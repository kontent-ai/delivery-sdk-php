<?php

namespace KenticoCloud\Tests\Unit;

use KenticoCloud\Delivery\{
    TypeMapperInterface,
    ContentLinkUrlResolverInterface, 
    InlineModularContentResolverInterface,
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
        $modelBinder = new ModelBinder($defaultMapper, $defaultMapper, $defaultMapper);
        $modelBinder->contentLinkUrlResolver = $contentLinkUrlResolver;
        $modelBinder->inlineModularContentResolver = $defaultMapper;

        $itemJson = file_get_contents('./tests/Unit/Data/ContentItemWithRichTextContainingBrokenAndNonbrokenLinks.json');
        $data = json_decode($itemJson);

        $model = $modelBinder->bindModel(\KenticoCloud\Delivery\Models\Items\ContentItem::class, $data);

        $this->assertContains('<a data-item-id="00000000-0000-0000-0000-000000000002" href="/custom/link-1">Link 1</a>', $model->bodyCopy);
        $this->assertContains('<a data-item-id="00000000-0000-0000-0000-000000000003" href="/custom/link-2">Link 2</a>', $model->bodyCopy);        
        $this->assertContains('<a data-item-id="FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF" href="/404">404</a>', $model->bodyCopy);        
    }

    public function test_BindModel_DefaultImplementation_InlineModularContentNotChanged()
    {
        $defaultMapper = new DefaultMapper();

        $modelBinder = new ModelBinder($defaultMapper, $defaultMapper, $defaultMapper);
        $modelBinder->contentLinkUrlResolver = $defaultMapper;
        $modelBinder->inlineModularContentResolver = $defaultMapper;

        $itemJson = file_get_contents('./tests/Unit/Data/ContentItemWithRichTextContainingInlineModularContent.json');
        $data = json_decode($itemJson);

        $model = $modelBinder->bindModel(\KenticoCloud\Delivery\Models\Items\ContentItem::class, $data->item, $data->modular_content);

        $this->assertContains('<object type="application/kenticocloud" data-type="item" data-codename="modular_item_1"></object>', $model->bodyCopy);
        $this->assertContains('<object type="application/kenticocloud" data-type="item" data-codename="modular_item_2"></object>', $model->bodyCopy);      
    }

    public function test_BindModel_MockImplementation_InlineModularContentResolved()
    {
        $defaultMapper = new DefaultMapper();
        $inlineModularContentResolver = $this->createMock(InlineModularContentResolverInterface::class);
        $inlineModularContentResolver
        ->expects($this->exactly(3))
        ->method('resolveInlineModularContent')
        ->will($this->returnCallback(function($input, $item){
            return '<div>'.$item->system->name.'</div>';
        }));

        $modelBinder = new ModelBinder($defaultMapper, $defaultMapper, $defaultMapper);
        $modelBinder->contentLinkUrlResolver = $defaultMapper;
        $modelBinder->inlineModularContentResolver = $inlineModularContentResolver;

        $itemJson = file_get_contents('./tests/Unit/Data/ContentItemWithRichTextContainingInlineModularContent.json');
        $data = json_decode($itemJson);

        $model = $modelBinder->bindModel(\KenticoCloud\Delivery\Models\Items\ContentItem::class, $data->item, $data->modular_content);

        $this->assertContains('<div>Modular item 1</div>', $model->bodyCopy);
        $this->assertContains('<div>Modular item 2</div>', $model->bodyCopy);
        $this->assertContains('<object type="application/kenticocloud" data-type="noitem" data-codename="modular_item_1"></object>', $model->bodyCopy);        
        $this->assertContains('<object type="text/xml" data-type="item" data-codename="modular_item_1"></object>', $model->bodyCopy);        
    }
} 
