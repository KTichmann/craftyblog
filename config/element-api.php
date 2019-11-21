<?php

use craft\elements\Entry;

return [
    'endpoints' => [
        'blogs.json' => function () {
            return [
                'elementType' => Entry::class,
                'criteria' => ['section' => 'blog'],
                'transformer' => function (Entry $entry) {
                    return [
                        'title' => $entry->title,
                        'description' => $entry->description,
                        'slug' => $entry->slug,
                        'created' => $entry->dateCreated,
                        'author' => $entry->author->firstName
                    ];
                }
            ];
        },
        'blog/<slug:.*>.json' => function ($slug) {
            return [
                'elementType' => Entry::class,
                'criteria' => [
                    'section' => 'blog',
                    'slug' => $slug
                ],
                'one' => true,
                'transformer' => function (Entry $entry) {
                    $contentArr = [];
                    $matrixValues = $entry->getFieldValue('blogContent')->all();
                    foreach ($matrixValues as $block) {
                        switch ($block->type->handle) {
                            case 'subHeader':
                                $contentArr[] = ['type' => 'title', 'text' => $block->headerText];
                                break;
                            case 'codeBlock':
                                $contentArr[] = ['type' => 'code', 'code' => $block->code, 'language' => $block->language->value, 'side' => $block->side->value];
                                break;
                            case 'image':
                                $contentArr[] = ['type' => 'image', 'url' => $block->image[0]->url, 'side' => $block->side->value];
                                break;
                            case 'textBlock':
                                $contentArr[] = ['type' => 'text', 'text' => $block->blockContent, 'side' => $block->side->value];
                                break;
                            default:
                                break;
                        }
                    }

                    return [
                        'title' => $entry->title,
                        'content' => $contentArr,
                        'slug' => $entry->slug,
                        'created' => $entry->dateCreated,
                        'author' => $entry->author->firstName
                    ];
                }
            ];
        },
        // 'blog/<entryId:\d+>.json' => function ($entryId) {
        //     return [
        //         'elementType' => Entry::class,
        //         'criteria' => ['id' => $entryId],
        //         'one' => true,
        //         'transformer' => function (Entry $entry) {
        //             return [
        //                 'title' => $entry->title,
        //                 'url' => $entry->url,
        //                 'summary' => $entry->summary,
        //                 'body' => $entry->body,
        //             ];
        //         },
        //     ];
        // },
    ]
];
