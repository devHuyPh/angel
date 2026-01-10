<?php

namespace App\Http\Controllers\Vendor;

use Botble\Marketplace\Http\Controllers\BaseController;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
  public function __construct()
  {
    $version = get_cms_version();

    Theme::asset()
      ->add('customer-style', 'vendor/core/plugins/ecommerce/css/customer.css', ['bootstrap-css'], version: $version);

    Theme::asset()
      ->container('footer')
      ->add('ecommerce-utilities-js', 'vendor/core/plugins/ecommerce/js/utilities.js', ['jquery'], version: $version)
      ->add('cropper-js', 'vendor/core/plugins/ecommerce/libraries/cropper.js', ['jquery'], version: $version)
      ->add('avatar-js', 'vendor/core/plugins/ecommerce/js/avatar.js', ['jquery'], version: $version);
  }
  public const PATH_VIEW = 'vendor.notifications.';
  public function notification()
  {
    return view(self::PATH_VIEW . 'index');
  }
}
