<div class="table-responsive desktop">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>{{ __('Image') }}</th>
                <th>{{ __('Product Name') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Star') }}</th>
                <th>{{ __('Comment') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($reviews->total() > 0)
                @foreach ($reviews as $item)
                        <tr>
                            <th scope="row">
                                <img class="img-thumb"
                                    src="{{ RvMedia::getImageUrl($item->product->image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                                    alt="{{ $item->product->name }}" style="max-width: 70px">
                            </th>
                            <th scope="row">
                                <a href="{{ $item->product->url }}">{!! BaseHelper::clean($item->product->name) !!}</a>

                                @if ($sku = $item->product->sku)
                                    <p><small>({{ $sku }})</small></p>
                                @endif

                                @if (is_plugin_active('marketplace') && $item->product->store->id)
                                    <p class="d-block mb-0 sold-by">
                                        <small>{{ __('Sold by') }}: <a href="{{ $item->product->original_product->store->url }}"
                                                class="text-primary">{{ $item->product->store->name }}</a>
                                        </small>
                                    </p>
                                @endif
                            </th>
                            <td>{{ $item->created_at->translatedFormat('M d, Y h:m') }}</td>
                            <td>
                                <span>{{ $item->star }}</span>
                                <x-core::icon name="ti ti-star" class="ecommerce-icon text-warning" />
                            </td>
                            <td><span title="{{ $item->comment }}">{{ Str::limit($item->comment, 120) }}</span></td>
                            <td>
                                {!! Form::open([
                        'url' => route('public.reviews.destroy', $item->id),
                        'onSubmit' => 'return confirm("' . __('Do you really want to delete the review?') . '")',
                    ]) !!}
                                <input name="_method" type="hidden" value="DELETE">
                                <button class="btn btn-danger btn-sm">{{ __('Delete') }}</button>
                                {!! Form::close() !!}
                            </td>
                        </tr>
                @endforeach
            @else
                <tr>
                    <td class="text-center" colspan="6">{{ __('No reviews!') }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<style>
    .product-reviews-page .ecommerce-product-star-done>label {
        color: #ffc107 !important;
    }
</style>
@if ($reviews->total() > 0)
    @foreach ($reviews as $item)
        <div class="card mobile mb-2 mx-2">
            <div class="card-body">
                <div class="card-title d-flex head-mobile-order justify-content-between align-items-center">
                    <div class="col-7">
                        <h3 class="text-success">
                            Unigreen - Linh Chi
                        </h3>
                    </div>
                </div>

                <div class="ecommerce-product-item" data-id="{{ $item->product->id }}">
                    <div class="products p-2 bg-light text-white mx-2 mb-1 row align-items-center">
                        <div class="img-pro col-4">
                            <img src="{{ url('/storage') . '/' . $item->product->image }}" alt="{{ $item->product->name }}">
                        </div>
                        <div class="content-pro col-8">
                            <div class="head-ct-pro ecommerce-product-name">
                                <p>{{ $item->product->name }}</p>
                            </div>
                            <div class="text-muted mt-1">
                                <time>{{ $item->created_at->translatedFormat('M d, Y h:m') }}</time>
                            </div>
                            <div class="d-flex ecommerce-product-star-done mt-1 w-50 col-6">
                                @for ($i = $item->star; $i >= 1; $i--)
                                    <label class="order-{{ $i }}">
                                        <x-core::icon name="ti ti-star-filled" class="text-yellow-500" />

                                    </label>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="review border p-2 bg-white text-dark mx-2 mb-1">
                        {{ $item->comment }}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

<div class="pagination">
    {!! $reviews->links() !!}
</div>