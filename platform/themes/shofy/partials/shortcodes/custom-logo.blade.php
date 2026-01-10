@php
    use Botble\Media\Facades\RvMedia;

    // $logo được truyền từ Theme::partial(...)
    $logoUrl = !empty($logo) ? RvMedia::getImageUrl($logo) : null;

    $h = trim((string) ($height ?? ''));
    if ($h === '') {
        $h = 'calc(100vh / 3)';
    } elseif (is_numeric($h)) {
        $h .= 'px';
    }
    $w = trim((string) ($width ?? ''));
    if ($w === '') {
        $w = '100%';
    } elseif (is_numeric($h)) {
        $w .= 'px';
    }
		 $visibility = $visibility ?? 'both';
		  $visibilityClass = match ($visibility) {
         'desktop' => 'd-none d-lg-block',
    'mobile'  => 'd-block d-lg-none',
    default   => 'd-block',
    };
@endphp

@if ($logoUrl)
<span class="{{ $visibilityClass }}">
    <img
        src="{{ $logoUrl }}"
        alt="{{ e($alt ?? 'Logo') }}"
        style="height: {{ $h }}; width: {{ $w }};"
        loading="lazy"
    />
		</span>
@endif
