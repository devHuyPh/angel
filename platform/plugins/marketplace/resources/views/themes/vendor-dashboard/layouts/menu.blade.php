<ul class="menu">
  @if(in_array(Route::currentRouteName(), ['bitsgold.dashboard', 'bitsgold.plan']))
    @foreach (DashboardMenu::getAll('bitsgold') as $item)
    @continue(!$item['name'])
    <li>
    <a href="{{ $item['url'] }}" @class(['active' => $item['active']])>
      <x-core::icon :name="$item['icon']" />

      {{ $item['name'] }}
    </a>
    </li>
  @endforeach
  @else
    @foreach (DashboardMenu::getAll('vendor') as $item)
    @continue(!$item['name'])
    <li>
    <a href="{{ $item['url'] }}" @class(['active' => $item['active']])>
      <x-core::icon :name="$item['icon']" />

      {{ $item['name'] }}
    </a>
    </li>
  @endforeach
  @endif
</ul>
