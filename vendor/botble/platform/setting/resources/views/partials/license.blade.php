@php
    $manageLicense = auth()
        ->user()
        ->hasPermission('core.manage.license');

    $licenseTitle = __('License');
    $licenseDescription = __('Setup license code');
@endphp


