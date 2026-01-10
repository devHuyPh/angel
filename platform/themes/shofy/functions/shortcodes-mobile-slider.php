<?php 

use Botble\Base\Forms\FieldOptions\MediaImagesFieldOption;
use Botble\Base\Forms\Fields\MediaImagesField;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Arr;


app()->booted(function (): void {
    if (! is_plugin_active('ecommerce')) {
        return;
    }

    Shortcode::register('mobile-slider', __('Mobile Slider'), __('Mobile Slider'), function (ShortcodeCompiler $shortcode) {
        // Parse images từ shortcode - images được lưu dưới dạng array hoặc JSON string
        $images = Shortcode::fields()->getIds('images', $shortcode);
        
        return Theme::partial('shortcodes.mobile-slider.index', compact('shortcode', 'images'));
    });


    Shortcode::setPreviewImage('mobile-slider', Theme::asset()->url('images/shortcodes/mobile-slider/slider.png'));


    // Thêm nhiều ảnh với MediaImagesField
    Shortcode::setAdminConfig('mobile-slider', function (array $attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->withLazyLoading()
            ->add(
                'images[]',
                MediaImagesField::class,
                MediaImagesFieldOption::make()
                    ->label(__('Slider Images'))
                    ->helperText(__('Upload multiple images for the slider. Recommended size: 700x350px'))
                    ->values(Arr::get($attributes, 'images', []))
            );
    });




});