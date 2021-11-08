<?php

namespace Kentico\Kontent\Tests\Unit;

use Kentico\Kontent\Delivery\ContentLinkUrlResolverInterface;
use Kentico\Kontent\Delivery\InlineLinkedItemsResolverInterface;
use Kentico\Kontent\Delivery\ModelBinder;
use Kentico\Kontent\Delivery\DefaultMapper;
use PHPUnit\Framework\TestCase;

class ModelBinderTest extends TestCase
{
    public function test_BindModel_LinksCorrectlyResolved()
    {
        $defaultMapper = new DefaultMapper();

        $contentLinkUrlResolver = $this->createMock(ContentLinkUrlResolverInterface::class);
        $contentLinkUrlResolver
            ->method('resolveLinkUrl')
            ->will($this->returnCallback(function ($link) {
                return '/custom/' . $link->urlSlug;
            }));
        $contentLinkUrlResolver
            ->method('resolveBrokenLinkUrl')
            ->willReturn('/404');
        $modelBinder = new ModelBinder($defaultMapper, $defaultMapper, $defaultMapper);
        $modelBinder->contentLinkUrlResolver = $contentLinkUrlResolver;
        $modelBinder->inlineLinkedItemsResolver = $defaultMapper;

        $itemJson = file_get_contents('./tests/Unit/Data/ContentItemWithRichTextContainingBrokenAndNonbrokenLinks.json');
        $data = json_decode($itemJson);

        $model = $modelBinder->bindModel(\Kentico\Kontent\Delivery\Models\Items\ContentItem::class, $data);

        // Test content links
        $this->assertContains('<a data-item-id="00000000-0000-0000-0000-000000000002" href="/custom/link-1">Link 1</a>', $model->bodyCopy);
        $this->assertContains('<a data-item-id="00000000-0000-0000-0000-000000000003" href="/custom/link-2">Link 2</a>', $model->bodyCopy);

        // Test broken links
        $this->assertContains('<a data-item-id="FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF" href="/404">404</a>', $model->bodyCopy);
    }

    public function test_BindModel_DefaultImplementation_InlineLinkedItemsNotChanged()
    {
        $defaultMapper = new DefaultMapper();

        $modelBinder = new ModelBinder($defaultMapper, $defaultMapper, $defaultMapper);
        $modelBinder->contentLinkUrlResolver = $defaultMapper;
        $modelBinder->inlineLinkedItemsResolver = $defaultMapper;

        $itemJson = file_get_contents('./tests/Unit/Data/ContentItemWithRichTextContainingInlineLinkedItems.json');
        $data = json_decode($itemJson);

        $model = $modelBinder->bindModel(\Kentico\Kontent\Delivery\Models\Items\ContentItem::class, $data->item, $data->modular_content);

        $this->assertContains('<object type="application/kenticocloud" data-type="item" data-codename="modular_item_1"></object>', $model->bodyCopy);
        $this->assertContains('<object type="application/kenticocloud" data-type="item" data-codename="modular_item_2"></object>', $model->bodyCopy);
    }

    public function test_BindModel_DefaultImplementationEmptyRichTextValue_ResolvedWithNoError()
    {
        $defaultMapper = new DefaultMapper();

        $modelBinder = new ModelBinder($defaultMapper, $defaultMapper, $defaultMapper);
        $modelBinder->contentLinkUrlResolver = $defaultMapper;
        $modelBinder->inlineLinkedItemsResolver = $defaultMapper;

        $itemJson = file_get_contents('./tests/Unit/Data/ContentItemWithRichTextWithEmptyValue.json');
        $data = json_decode($itemJson);

        $model = $modelBinder->bindModel(\Kentico\Kontent\Delivery\Models\Items\ContentItem::class, $data->item, $data->modular_content);

        $this->assertTrue(is_string($model->bodyCopy));
        $this->assertEmpty($model->bodyCopy);
    }

    public function test_BindModel_MockImplementation_InlineLinkedItemsResolved()
    {
        $defaultMapper = new DefaultMapper();
        $inlineLinkedItemsResolver = $this->createMock(InlineLinkedItemsResolverInterface::class);
        $inlineLinkedItemsResolver
            ->expects($this->exactly(3))
            ->method('resolveInlineLinkedItems')
            ->will($this->returnCallback(function ($input, $item, $linkedItems) {
                $this->assertCount(2, get_object_vars($linkedItems));
                return '<div>' . $item->system->name . '</div>';
            }));

        $modelBinder = new ModelBinder($defaultMapper, $defaultMapper, $defaultMapper);
        $modelBinder->contentLinkUrlResolver = $defaultMapper;
        $modelBinder->inlineLinkedItemsResolver = $inlineLinkedItemsResolver;

        $itemJson = file_get_contents('./tests/Unit/Data/ContentItemWithRichTextContainingInlineLinkedItems.json');
        $data = json_decode($itemJson);

        $model = $modelBinder->bindModel(\Kentico\Kontent\Delivery\Models\Items\ContentItem::class, $data->item, $data->modular_content);

        $this->assertContains('<div>Modular item 1</div>', $model->bodyCopy);
        $this->assertContains('<div>Modular item 2</div>', $model->bodyCopy);
        $this->assertContains('<object type="application/kenticocloud" data-type="noitem" data-codename="modular_item_1"></object>', $model->bodyCopy);
        $this->assertContains('<object type="text/xml" data-type="item" data-codename="modular_item_1"></object>', $model->bodyCopy);
    }

    public function test_BindModel_MockImplementation_TableInRichTextResolved()
    {
        $defaultMapper = new DefaultMapper();
        $modelBinder = new ModelBinder($defaultMapper, $defaultMapper, $defaultMapper);

        $itemJson = file_get_contents('./tests/Unit/Data/ContentItemWithRichTextContainingComplexTable.json');
        $data = json_decode($itemJson);

        $model = $modelBinder->bindModel(\Kentico\Kontent\Delivery\Models\Items\ContentItem::class, $data->item, $data->modular_content);
        $this->assertEquals($data->item->elements->rich_text->value,  $model->richText);
    }

    public function test_BindModel_MockImplementation_WorkflowStepNotSetForComponent()
    {

        $defaultMapper = new DefaultMapper();
        $inlineLinkedItemsResolver = $this->createMock(InlineLinkedItemsResolverInterface::class);
        $inlineLinkedItemsResolver
            ->expects($this->exactly(2))
            ->method('resolveInlineLinkedItems')
            ->will($this->returnCallback(function ($input, $item, $linkedItems) {
                $this->assertCount(2, get_object_vars($linkedItems));
                if($item->system->codename == 'n8bf1055d_7b61_0180_e1c6_3a09d88f0396') {
                    $this->assertFalse(isset($item->system->workflow_step));
                } else {
                    $this->assertTrue(isset($item->system->workflow_step));
                }
                return $input;
            }));

        $modelBinder = new ModelBinder($defaultMapper, $defaultMapper, $defaultMapper);
        $modelBinder->contentLinkUrlResolver = $defaultMapper;
        $modelBinder->inlineLinkedItemsResolver = $inlineLinkedItemsResolver;

        $itemJson = file_get_contents('./tests/Unit/Data/ContentItemWithRichTextContainingAllEntities.json');
        $data = json_decode($itemJson);

        $modelBinder->bindModel(\Kentico\Kontent\Delivery\Models\Items\ContentItem::class, $data->item, $data->modular_content);
    }

    public function test_BindModel_ModelWithCustomElement_ElementBoundProperly()
    {
        $defaultMapper = new DefaultMapper();
        $modelBinder = new ModelBinder($defaultMapper, $defaultMapper, $defaultMapper);

        $itemJson = file_get_contents('./tests/Unit/Data/ContentItemWithCustomElement.json');
        $data = json_decode($itemJson);

        $model = $modelBinder->bindModel(\Kentico\Kontent\Delivery\Models\Items\ContentItem::class, $data->item, $data->modular_content);
        $this->assertEquals($data->item->elements->colorpicker->value,  $model->colorpicker);
    }
}
