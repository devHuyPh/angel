<ul class="nav nav-pills flex-column bg-light overflow-auto" style="touch-action: pan-y;">
    @foreach (DashboardMenu::getAll('bitsgold') as $item)
        @continue(! $item['name'])
        <li class="nav-item">
            <a
                href="{{ $item['url'] }}"
                @class([
                    'nav-link',
                    'active' => $item['active'],
                    'text-dark' => !$item['active'],
                    'bg-success' => $item['active'],
                    'text-white' => $item['active'],
                    'd-flex',
                    'align-items-center',
                    'py-3',
                    'px-4',
                    'mb-1',
                    'rounded-0'
                ])
            >
                <x-core::icon :name="$item['icon']" class="me-2" />
                {{ $item['name'] }}
            </a>
        </li>
    @endforeach
</ul>