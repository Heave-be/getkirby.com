<?php

namespace Kirby\Cms;

/**
 * HasPanelImage
 *
 * @package   Kirby Cms
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier GmbH
 * @license   https://getkirby.com/license
 */
trait HasPanelImage
{

    /**
     * @internal
     * @param string|array|false $settings
     * @return array|null
     */
    public function panelImage($settings = null): ?array
    {
        $defaults = [
            'ratio' => '3/2',
            'back'  => 'pattern',
            'cover' => false
        ];

        // switch the image off
        if ($settings === false) {
            return null;
        }

        if (is_string($settings) === true) {
            $settings = [
                'query' => $settings
            ];
        }

        if ($image = $this->panelImageSource($settings['query'] ?? null)) {

            // for cards
            $settings['cards'] = [
                'url' => $image->thumb([
                    'width'  => 128
                ])->url(true),
                'srcset' => $image->srcset([
                    128,
                    256,
                    512,
                    768,
                    1024,
                    2048
                ])
            ];

            // for lists
            $settings['list'] = [
                'url' => $image->thumb([
                    'width'  => 38,
                    'height' => 38,
                    'crop' => 'center'
                ])->url(true),
                'srcset' => $image->thumb([
                    'width' => 38,
                    'height' => 38,
                    'crop' => 'center'
                ])->url(true) . ' 1x, ' . $image->thumb([
                    'width' => 76,
                    'height' => 76,
                    'crop' => 'center'
                ])->url(true) . ' 2x, ' . $image->thumb([
                    'width' => 152,
                    'height' => 152,
                    'crop' => 'center'
                ])->url(true) . ' 3x'
            ];

            unset($settings['query']);
        }

        return array_merge($defaults, (array)$settings);
    }

    /**
     * Returns the image file object based on provided query
     *
     * @internal
     * @param string|null $query
     * @return Kirby\Cms\File|Kirby\Cms\Asset|null
     */
    protected function panelImageSource(string $query = null)
    {
        // define default for pages
        if (is_a($this, Page::class) === true) {
            $default = 'page.image';
        }

        $image = $this->query($query ?? $default ?? null);

        // validate the query result
        if (is_a($image, File::class) === false && is_a($image, Asset::class) === false) {
            $image = null;
        }

        // fallback for files
        if ($image === null && is_a($this, File::class) === true && $this->isViewable() === true) {
            $image = $this;
        }

        return $image;
    }
}
