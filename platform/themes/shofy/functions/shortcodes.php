<?php

use Botble\Base\Forms\FieldOptions\ColorFieldOption;
use Botble\Base\Forms\FieldOptions\CoreIconFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\UiSelectorFieldOption;
use Botble\Base\Forms\Fields\ColorField;
use Botble\Base\Forms\Fields\CoreIconField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\UiSelectorField;
use Botble\Ecommerce\Models\Brand;
use Botble\Newsletter\Forms\Fronts\NewsletterForm;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Shortcode\Forms\FieldOptions\ShortcodeTabsFieldOption;
use Botble\Shortcode\Forms\Fields\ShortcodeTabsField;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Shortcode\ShortcodeField;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Supports\ThemeSupport;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

app()->booted(function (): void {
	ThemeSupport::registerGoogleMapsShortcode(Theme::getThemeNamespace('partials.shortcodes.google-maps'));
	ThemeSupport::registerYoutubeShortcode();

	Shortcode::register('site-features', __('Site Features'), __('Site Features'), function (ShortcodeCompiler $shortcode) {
		$tabs = Shortcode::fields()->getTabsData(['title', 'description', 'icon'], $shortcode);

		return Theme::partial('shortcodes.site-features.index', compact('shortcode', 'tabs'));
	});

	Shortcode::setPreviewImage('site-features', Theme::asset()->url('images/shortcodes/site-features/style-1.png'));

	Shortcode::setAdminConfig('site-features', function (array $attributes) {
		$styles = [];

		foreach (range(1, 4) as $i) {
			$styles[$i] = [
				'label' => __('Style :number', ['number' => $i]),
				'image' => Theme::asset()->url(sprintf('images/shortcodes/site-features/style-%s.png', $i)),
			];
		}

		return ShortcodeForm::createFromArray($attributes)
			->add(
				'style',
				UiSelectorField::class,
				UiSelectorFieldOption::make()
					->choices($styles)
					->selected(Arr::get($attributes, 'style', 1))
			)
			->add(
				'features',
				ShortcodeTabsField::class,
				ShortcodeTabsFieldOption::make()
					->fields([
						'title' => [
							'type' => 'text',
							'title' => __('Title'),
							'required' => true,
						],
						'description' => [
							'type' => 'textarea',
							'title' => __('Description'),
							'required' => false,
						],
						'icon' => [
							'type' => 'coreIcon',
							'title' => __('Icon'),
							'required' => true,
						],
					])
					->attrs($attributes)
			)
			->add(
				'icon_color',
				ColorField::class,
				ColorFieldOption::make()
					->label(__('Icon color'))
					->defaultValue('#fd4b6b')
			);
	});

	Shortcode::register('app-downloads', __('App Downloads'), __('App Downloads'), function (ShortcodeCompiler $shortcode): ?string {
		$platforms = Shortcode::fields()->getTabsData(['image', 'url'], $shortcode);

		return Theme::partial('shortcodes.app-downloads.index', compact('shortcode', 'platforms'));
	});

	Shortcode::setPreviewImage('app-downloads', Theme::asset()->url('images/shortcodes/app-downloads.png'));

	Shortcode::setAdminConfig('app-downloads', function (array $attributes) {
		return ShortcodeForm::createFromArray($attributes)
			->withLazyLoading()
			->add(
				'title',
				TextField::class,
				TextFieldOption::make()
					->label(__('Title'))
			)
			->add(
				'open_wrapper_google',
				HtmlField::class,
				['html' => '<div class="form-fieldset">']
			)
			->add(
				'google_label',
				TextField::class,
				TextFieldOption::make()
					->label(__('Google label'))
					->placeholder(__('Enter Google label'))
			)
			->add(
				'google_icon',
				CoreIconField::class,
				CoreIconFieldOption::make()
					->label(__('Google Play icon'))
			)
			->add(
				'google_url',
				TextField::class,
				TextFieldOption::make()
					->label(__('Google URL'))
					->placeholder(__('Enter Google URL'))
			)
			->add('close_wrapper_google', HtmlField::class, ['html' => '</div>'])
			->add('open_wrapper_apple', HtmlField::class, ['html' => '<div class="form-fieldset">'])
			->add(
				'apple_label',
				TextField::class,
				TextFieldOption::make()
					->label(__('Apple label'))
					->placeholder(__('Enter Apple label'))
			)
			->add(
				'apple_icon',
				CoreIconField::class,
				CoreIconFieldOption::make()
					->label(__('Apple icon'))
			)
			->add(
				'apple_url',
				TextField::class,
				TextFieldOption::make()
					->label(__('Apple URL'))
					->placeholder(__('Enter Apple URL'))
			)
			->add('close_wrapper_apple', HtmlField::class, ['html' => '</div>'])
			->add(
				'screenshot',
				MediaImageField::class,
				MediaImageFieldOption::make()
					->label(__('Mobile screenshot'))
			)
			->add(
				'shape_image_left',
				MediaImageField::class,
				MediaImageFieldOption::make()
					->label(__('Shape image left'))
			)
			->add(
				'shape_image_right',
				MediaImageField::class,
				MediaImageFieldOption::make()
					->label(__('Shape image right'))
			);
	});

	Shortcode::register(
		'image-slider',
		__('Image Slider'),
		__('Dynamic carousel for featured content with customizable links.'),
		function (ShortcodeCompiler $shortcode) {
			$tabs = [];
			$brands = [];

			switch ($shortcode->type) {
				case 'custom':
					$tabs = Shortcode::fields()->getTabsData(['name', 'image', 'url'], $shortcode);

					if (empty($tabs)) {
						return null;
					}

					break;

				case 'brands':
					$brandIds = Shortcode::fields()->getIds('brand_ids', $shortcode);

					if (empty($brandIds)) {
						return null;
					}

					$brands = Brand::query()
						->wherePublished()
						->whereIn('id', $brandIds)
						->get();

					if (empty($brands)) {
						return null;
					}

					break;
			}

			return Theme::partial('shortcodes.image-slider.index', compact('shortcode', 'tabs', 'brands'));
		}
	);

	Shortcode::setPreviewImage('image-slider', Theme::asset()->url('images/shortcodes/image-slider.png'));

	Shortcode::setAdminConfig('image-slider', function (array $attributes) {
		$types = [
			'custom' => __('Custom'),
		];

		if (is_plugin_active('ecommerce')) {
			$types['brands'] = __('Brands');
		}

		return ShortcodeForm::createFromArray($attributes)
			->withLazyLoading()
			->add(
				'type',
				RadioField::class,
				RadioFieldOption::make()
					->label(__('Get data from to show'))
					->choices($types)
					->attributes([
						'data-bb-toggle' => 'collapse',
						'data-bb-target' => '.image-slider',
					]),
			)
			->when(is_plugin_active('ecommerce'), function (ShortcodeForm $form) use ($attributes): void {
				$form->add(
					'brand_ids',
					SelectField::class,
					SelectFieldOption::make()
						->label(__('Brands'))
						->choices(
							Brand::query()
								->wherePublished()
								->pluck('name', 'id')
								->all()
						)
						->selected(ShortcodeField::parseIds(Arr::get($attributes, 'brand_ids')))
						->searchable()
						->multiple()
						->wrapperAttributes([
							'class' => 'mb-3 position-relative image-slider',
							'data-bb-value' => 'brands',
							'style' => sprintf('display: %s', Arr::get($attributes, 'type') === 'brands' ? 'block' : 'none'),
						]),
				);
			})
			->add(
				'open_tabs_wrapper',
				HtmlField::class,
				['html' => sprintf('<div class="image-slider" data-bb-value="custom" style="display: %s">', Arr::get($attributes, 'type') === 'custom' ? 'block' : 'none')]
			)
			->add(
				'tabs',
				ShortcodeTabsField::class,
				ShortcodeTabsFieldOption::make()
					->fields([
						'name' => [
							'type' => 'text',
							'title' => __('Name'),
						],
						'image' => [
							'type' => 'image',
							'title' => __('Image'),
							'required' => true,
						],
						'url' => [
							'type' => 'text',
							'title' => __('URL'),
						],
					])
					->attrs($attributes)
			)
			->add('close_tabs_wrapper', HtmlField::class, ['html' => '</div>']);
	});

	Shortcode::register('about', __('About'), __('About'), function (ShortcodeCompiler $shortcode) {
		return Theme::partial('shortcodes.about.index', compact('shortcode'));
	});

	Shortcode::setPreviewImage('about', Theme::asset()->url('images/shortcodes/about.png'));

	Shortcode::setAdminConfig('about', function (array $attributes) {
		return ShortcodeForm::createFromArray($attributes)
			->withLazyLoading()
			->columns()
			->add(
				'image_1',
				MediaImageField::class,
				MediaImageFieldOption::make()
					->label(__('Image 1'))
			)
			->add(
				'image_2',
				MediaImageField::class,
				MediaImageFieldOption::make()
					->label(__('Image 2'))
			)
			->add(
				'subtitle',
				TextField::class,
				TextFieldOption::make()
					->label(__('Subtitle'))
					->colspan(2)
			)
			->add(
				'title',
				TextField::class,
				TextFieldOption::make()
					->label(__('Title'))
					->colspan(2)
			)
			->add(
				'description',
				TextareaField::class,
				TextareaFieldOption::make()
					->label(__('Description'))
					->colspan(2)
			)
			->add(
				'action_label',
				TextField::class,
				TextFieldOption::make()
					->label(__('Action label')),
			)
			->add(
				'action_url',
				TextField::class,
				TextFieldOption::make()
					->label(__('Action URL')),
			);
	});

	Shortcode::register('coming-soon', __('Coming Soon'), __('Coming Soon'), function (ShortcodeCompiler $shortcode): string {
		try {
			$countdownTime = Carbon::parse($shortcode->countdown_time);
		} catch (Exception) {
			$countdownTime = null;
		}

		$form = null;

		if (is_plugin_active('newsletter')) {
			$form = NewsletterForm::create();
		}

		return Theme::partial('shortcodes.coming-soon.index', compact('shortcode', 'countdownTime', 'form'));
	});

	Shortcode::setAdminConfig('coming-soon', function (array $attributes): ShortcodeForm {
		return ShortcodeForm::createFromArray($attributes)
			->add(
				'title',
				TextField::class,
				TextFieldOption::make()
					->label(__('Title'))
			)
			->add(
				'countdown_time',
				'datetime',
				[
					'label' => __('Countdown time'),
					'default_value' => Carbon::now()->addDays(7)->format('Y-m-d H:i'),
				]
			)
			->add(
				'address',
				TextField::class,
				TextFieldOption::make()
					->label(__('Address'))
			)
			->add(
				'hotline',
				TextField::class,
				TextFieldOption::make()
					->label(__('Hotline'))
			)
			->add(
				'business_hours',
				TextField::class,
				TextFieldOption::make()
					->label(__('Business hours'))
			)
			->add(
				'show_social_links',
				OnOffField::class,
				OnOffFieldOption::make()
					->label(__('Show social links'))
					->defaultValue(true)
			)
			->add(
				'image',
				MediaImageField::class,
				MediaImageFieldOption::make()
					->label(__('Image'))
			);
	});

	Shortcode::register('custom-logo', __('Custom Logo'), __('Display a logo block'), function (ShortcodeCompiler $shortcode) {
		return Theme::partial('shortcodes.custom-logo', [
			'logo' => $shortcode->logo,
			'height' => $shortcode->height,
			'width' => $shortcode->width,
			'class' => $shortcode->class,
			'visibility' => $shortcode->visibility,
		]);
	});
	Shortcode::setAdminConfig('custom-logo', function (array $attributes) {
		return ShortcodeForm::createFromArray($attributes)
			->withLazyLoading()
			->add(
				'logo',
				MediaImageField::class,
				MediaImageFieldOption::make()
					->label(__('Logo'))
					->required()
			)
			->add(
				'height',
				TextField::class,
				TextFieldOption::make()
					->label(__('Height (px)'))
					->placeholder('calc(100vh / 3)')
					->defaultValue(Arr::get($attributes, 'height', 'calc(100vh / 3)'))
			)
			->add(
				'width',
				TextField::class,
				TextFieldOption::make()
					->label(__('Width (px)'))
					->placeholder('100%')
					->defaultValue(Arr::get($attributes, 'width', '100%'))
			)
			->add(
				'class',
				TextField::class,
				TextFieldOption::make()
					->label(__('CSS class (optional)'))
			)
			->add(
				'visibility',
				SelectField::class,
				SelectFieldOption::make()
					->label(__('Visibility'))
					->choices([
						'both' => __('Desktop & Mobile'),
						'desktop' => __('Desktop only'),
						'mobile' => __('Mobile only'),
					])
					->selected(Arr::get($attributes, 'visibility', 'both'))
			);
	});
	Shortcode::register('rank-stats', __('Rank Stats'), __('Show rank list with user counts'), function (ShortcodeCompiler $shortcode) {
		// IMPORTANT: chỉnh lại đúng bảng của bố
		$ranksTable = 'rankings';
		$usersTable = 'ec_customers';
		$rankCol = 'rank_id';

	 $vShow = strtolower(trim((string) ($shortcode->show_total ?? '')));
    $showTotal = $vShow === '' ? true : !in_array($vShow, ['0','false','off','no'], true);
		 $vZero = strtolower(trim((string) ($shortcode->include_zero ?? '')));
    $includeZero = $vZero === '' ? true : !in_array($vZero, ['0','false','off','no'], true);

		$cacheKey = sprintf(
			'rank_stats:%s:%s:%s:%s:%s',
			$ranksTable,
			$usersTable,
			$rankCol,
			$includeZero ? '1' : '0',
			$showTotal ? '1' : '0'
		);

		[$ranks, $totalRankedUsers] = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($ranksTable, $usersTable, $rankCol, $includeZero, $showTotal) {
			$countsSub = DB::table($usersTable)
				->select($rankCol, DB::raw('COUNT(*) as users_count'))
				->whereNotNull($rankCol)
				->groupBy($rankCol);

			$ranks = DB::table($ranksTable . ' as r')
				->leftJoinSub($countsSub, 'c', fn($join) => $join->on('r.id', '=', 'c.' . $rankCol))
				->select([
					'r.rank_name',
					'r.rank_icon',
					'r.rank_lavel',
					DB::raw('COALESCE(c.users_count, 0) as users_count'),
				])
				->orderBy('r.rank_lavel', 'desc')
				->get();

			if (!$includeZero) {
				$ranks = $ranks->filter(fn($x) => (int) $x->users_count > 0)->values();
			}

			$totalRankedUsers = $showTotal
				? DB::table($usersTable)->whereNotNull($rankCol)->count()
				: 0;

			return [$ranks, $totalRankedUsers];
		});

		return Theme::partial('shortcodes.rank-stats.index', compact('shortcode', 'ranks', 'totalRankedUsers'));
	});

	Shortcode::setAdminConfig('rank-stats', function (array $attributes) {
		return ShortcodeForm::createFromArray($attributes)
			->add(
				'show_total',
				OnOffField::class,
				OnOffFieldOption::make()
					->label(__('Show total ranked users'))
					->defaultValue(true)
			)
			->add(
				'include_zero',
				OnOffField::class,
				OnOffFieldOption::make()
					->label(__('Include ranks with 0 users'))
					->defaultValue(true)
			)
			->add(
				'title',
				TextField::class,
				TextFieldOption::make()
					->label(__('Title (optional)'))
					->placeholder(__('Rank statistics'))
			);
	});
});
