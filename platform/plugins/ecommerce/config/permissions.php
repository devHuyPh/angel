<?php

return [
    [
        'name' => 'Thương mại điện tử',
        'flag' => 'plugins.ecommerce',
    ],
    //kyc
    [
        'name' => 'Quản lý KYC',
        'flag' => 'packages.mlm.kyc',
    ],
    [
        'name' => 'Biểu mẫu KYC',
        'flag' => 'packages.mlm.kyc.identity-form',
        'parent_flag' => 'packages.mlm.kyc',
    ],
    [
        'name' => 'Xem',
        'flag' => 'packages.mlm.kyc.identity-form.view',
        'parent_flag' => 'packages.mlm.kyc.identity-form',
    ],
    [
        'name' => 'Thêm',
        'flag' => 'packages.mlm.kyc.identity-form.create',
        'parent_flag' => 'packages.mlm.kyc.identity-form',
    ],
    [
        'name' => 'Sửa',
        'flag' => 'packages.mlm.kyc.identity-form.edit',
        'parent_flag' => 'packages.mlm.kyc.identity-form',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'packages.mlm.kyc.identity-form.delete',
        'parent_flag' => 'packages.mlm.kyc.identity-form',
    ],
    [
        'name' => 'KYC chờ xử lý',
        'flag' => 'packages.mlm.kyc.pending',
        'parent_flag' => 'packages.mlm.kyc',
    ],
    [
        'name' => 'Xem',
        'flag' => 'packages.mlm.kyc.pending.view',
        'parent_flag' => 'packages.mlm.kyc.pending',
    ],
    [
        'name' => 'Sửa',
        'flag' => 'packages.mlm.kyc.pending.edit',
        'parent_flag' => 'packages.mlm.kyc.pending',
    ],
    [
        'name' => 'Nhật ký KYC',
        'flag' => 'packages.mlm.kyc.log',
        'parent_flag' => 'packages.mlm.kyc',
    ],
    //endkyc
    // mlm
    [
        'name' => 'Quản lý tiếp thị',
        'flag' => 'packages.mlm',
    ],

    // Quản lý khu vực
    [
        'name' => 'Quản lý khu vực',
        'flag' => 'packages.mlm.region',
        'parent_flag' => 'packages.mlm',
    ],
    [
        'name' => 'Xem',
        'flag' => 'packages.mlm.region.view',
        'parent_flag' => 'packages.mlm.region',
    ],
    [
        'name' => 'Thêm',
        'flag' => 'packages.mlm.region.create',
        'parent_flag' => 'packages.mlm.region',
    ],
    [
        'name' => 'Sửa',
        'flag' => 'packages.mlm.region.edit',
        'parent_flag' => 'packages.mlm.region',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'packages.mlm.region.delete',
        'parent_flag' => 'packages.mlm.region',
    ], //done

    // Kích hoạt tài khoản
    [
        'name' => 'Kích hoạt tài khoản',
        'flag' => 'packages.mlm.account_activation',
        'parent_flag' => 'packages.mlm',
    ],
    [
        'name' => 'Xem',
        'flag' => 'packages.mlm.account_activation.view',
        'parent_flag' => 'packages.mlm.account_activation',
    ],
    [
        'name' => 'Sửa',
        'flag' => 'packages.mlm.account_activation.edit',
        'parent_flag' => 'packages.mlm.account_activation',
    ], //done

    // Xếp hạng và đồng chia
    [
        'name' => 'Xếp hạng và đồng chia',
        'flag' => 'packages.mlm.ranking_sharing',
        'parent_flag' => 'packages.mlm',
    ],
    [
        'name' => 'Xem',
        'flag' => 'packages.mlm.ranking_sharing.view',
        'parent_flag' => 'packages.mlm.ranking_sharing',
    ],
    [
        'name' => 'Thêm hạng',
        'flag' => 'packages.mlm.ranking_sharing.createrank',
        'parent_flag' => 'packages.mlm.ranking_sharing',
    ],
    [
        'name' => 'Sửa hạng',
        'flag' => 'packages.mlm.ranking_sharing.editrank',
        'parent_flag' => 'packages.mlm.ranking_sharing',
    ],
    [
        'name' => 'Xóa hạng',
        'flag' => 'packages.mlm.ranking_sharing.deleterank',
        'parent_flag' => 'packages.mlm.ranking_sharing',
    ],
    [
        'name' => 'Gán hạng',
        'flag' => 'packages.mlm.ranking_sharing.assign',
        'parent_flag' => 'packages.mlm.ranking_sharing',
    ],
    [
        'name' => 'Sửa gán hạng',
        'flag' => 'packages.mlm.ranking_sharing.edit',
        'parent_flag' => 'packages.mlm.ranking_sharing',
    ],
    [
        'name' => 'Xóa gán hạng',
        'flag' => 'packages.mlm.ranking_sharing.delete',
        'parent_flag' => 'packages.mlm.ranking_sharing',
    ], // done

    // Thưởng kinh doanh hằng ngày
    [
        'name' => 'Thưởng kinh doanh hằng ngày',
        'flag' => 'packages.mlm.daily_bonus',
        'parent_flag' => 'packages.mlm',
    ],
    [
        'name' => 'Xem',
        'flag' => 'packages.mlm.daily_bonus.view',
        'parent_flag' => 'packages.mlm.daily_bonus',
    ],
    [
        'name' => 'Sửa',
        'flag' => 'packages.mlm.daily_bonus.edit',
        'parent_flag' => 'packages.mlm.daily_bonus',
    ], //done

    // Lịch sử đồng chia
    [
        'name' => 'Lịch sử đồng chia',
        'flag' => 'packages.mlm.profit_sharing_history',
        'parent_flag' => 'packages.mlm',
    ], //done

    // Hoa hồng giới thiệu
    [
        'name' => 'Hoa hồng giới thiệu',
        'flag' => 'packages.mlm.referral_commission',
        'parent_flag' => 'packages.mlm',
    ],
    [
        'name' => 'Xem',
        'flag' => 'packages.mlm.referral_commission.view',
        'parent_flag' => 'packages.mlm.referral_commission',
    ],
    [
        'name' => 'Sửa',
        'flag' => 'packages.mlm.referral_commission.edit',
        'parent_flag' => 'packages.mlm.referral_commission',
    ], //done
	[
      'name'=>'Danh sách khách hàng được hưởng hoa hồng',
      'flag'=>':packages.mlm.referral_commission.list',
      'parent_flag' => 'packages.mlm',
    ],
  [
    'name' => 'Xem chi tiết',
    'flag' => ':packages.mlm.referral_commission.detail',
    'parent_flag' => ':packages.mlm.referral_commission.list',
  ],
//kho bãi
  [
    'name' => 'Kho - cửa hàng',
    'flag' => 'packages.mlm.store-levels',
  ],
  [
    'name' => 'Danh sách loại kho',
    'flag' => 'packages.mlm.store-levels.index',
    'parent_flag' => 'packages.mlm.store-levels',
  ],
  [
    'name'=>'Danh sách kho đã gắn loại',
    'flag'=>'packages.mlm.store-levels.list',
    'parent_flag' => 'packages.mlm.store-levels.index',
  ],
[
    'name' => 'Lịch sử thưởng giao hàng',
    'flag' => 'packages.mlm.store-orders.history-bonus',
    'parent_flag' => 'packages.mlm.store-levels',
  ],
  [
    'name' => 'Gắn loại kho cho cửa hàng',
    'flag' => 'packages.mlm.store-levels.formedit',
    'parent_flag' => 'packages.mlm.store-levels.index',
  ],
  [
    'name' => 'Sửa',
    'flag' => 'packages.mlm.store-levels.edit',
    'parent_flag' => 'packages.mlm.store-levels.formedit',
  ],
  [
    'name' => 'Thưởng giao hàng',
    'flag' => 'packages.mlm.store-orders.pending-bonus',
    'parent_flag' => 'packages.mlm.store-levels',
  ],
  [
    'name' => 'Xem',
    'flag' => 'packages.mlm.store-orders.pending-bonus.view',
    'parent_flag' => 'packages.mlm.store-orders.pending-bonus',
  ],
  [
    'name' => 'Xác nhận thưởng',
    'flag' => 'packages.mlm.store-orders.pending-bonus.confirm-bonus',
    'parent_flag' => 'packages.mlm.store-orders.pending-bonus',
  ],
  [
    'name' => 'Kho chưa xác nhận giao hàng',
    'flag' => 'packages.mlm.store-orders.expired-pending',
    'parent_flag' => 'packages.mlm.store-levels',
  ],
  [
    'name' => 'Xem',
    'flag' => 'packages.mlm.store-orders.edit-from-store',
    'parent_flag' => 'packages.mlm.store-orders.expired-pending',
  ],
  [
    'name' => 'Sửa',
    'flag' => 'packages.mlm.store-orders.update-from-store',
    'parent_flag' => 'packages.mlm.store-orders.expired-pending',
  ],
  [
    'name' => 'Đơn hàng từ nhà máy',
    'flag' => 'packages.mlm.factory-orders',
    'parent_flag' => 'packages.mlm.store-levels',
  ],
  [
    'name' => 'Xem',
    'flag' => 'packages.mlm.factory-view',
    'parent_flag' => 'packages.mlm.factory-orders',
  ],
  [
    'name' => 'Sửa',
    'flag' => 'packages.mlm.factory-update-status',
    'parent_flag' => 'packages.mlm.factory-orders',
  ],
  [
    'name' => 'Đơn kho đến người dùng',
    'flag' => 'packages.mlm.store-to-user.index',
    'parent_flag' => 'packages.mlm.store-levels',
  ],
  [
    'name' => 'Xem',
    'flag' => 'packages.mlm.store-to-user.edit',
    'parent_flag' => 'packages.mlm.store-to-user.index',
  ],
  [
    'name' => 'Sửa',
    'flag' => 'packages.mlm.store-to-user.update',
    'parent_flag' => 'packages.mlm.store-to-user.index',
  ],
  [
    'name' => 'Xóa',
    'flag' => 'packages.mlm.store-to-user.delete',
    'parent_flag' => 'packages.mlm.store-to-user.index',
  ],

    // Người giới thiệu
    [
        'name' => 'Người giới thiệu',
        'flag' => 'packages.mlm.referrers',
        'parent_flag' => 'packages.mlm',
    ], //done

    // Báo cáo
    [
        'name' => 'Báo cáo',
        'flag' => 'packages.mlm.reports',
        'parent_flag' => 'packages.mlm',
    ], //done

    // Rút tiền Marketing
    [
        'name' => 'Rút tiền Marketing',
        'flag' => 'packages.mlm.marketing_withdrawals',
        'parent_flag' => 'packages.mlm',
    ],
    [
        'name' => 'Xem',
        'flag' => 'packages.mlm.marketing_withdrawals.view',
        'parent_flag' => 'packages.mlm.marketing_withdrawals',
    ],
    [
        'name' => 'Sửa',
        'flag' => 'packages.mlm.marketing_withdrawals.edit',
        'parent_flag' => 'packages.mlm.marketing_withdrawals',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'packages.mlm.marketing_withdrawals.delete',
        'parent_flag' => 'packages.mlm.marketing_withdrawals',
    ],


    // endmlm
    [
        'name' => 'Báo cáo',
        'flag' => 'ecommerce.report.index',
        'parent_flag' => 'plugins.ecommerce',
    ],

    /**
     * Products
     */
    [
        'name' => 'Sản phẩm',
        'flag' => 'products.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'products.create',
        'parent_flag' => 'products.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'products.edit',
        'parent_flag' => 'products.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'products.destroy',
        'parent_flag' => 'products.index',
    ],
    [
        'name' => 'Sao chép',
        'flag' => 'products.duplicate',
        'parent_flag' => 'products.index',
    ],

    /**
     * Product Prices
     */
    [
        'name' => 'Giá sản phẩm',
        'flag' => 'ecommerce.product-prices.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Cập nhật',
        'flag' => 'ecommerce.product-prices.edit',
        'parent_flag' => 'ecommerce.product-prices.index',
    ],

    /**
     * Product Inventory
     */
    [
        'name' => 'Tồn kho sản phẩm',
        'flag' => 'ecommerce.product-inventory.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Cập nhật',
        'flag' => 'ecommerce.product-inventory.edit',
        'parent_flag' => 'ecommerce.product-inventory.index',
    ],

    /**
     * Categories
     */
    [
        'name' => 'Danh mục sản phẩm',
        'flag' => 'product-categories.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'product-categories.create',
        'parent_flag' => 'product-categories.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'product-categories.edit',
        'parent_flag' => 'product-categories.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'product-categories.destroy',
        'parent_flag' => 'product-categories.index',
    ],

    [
        'name' => 'Thẻ sản phẩm',
        'flag' => 'product-tag.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'product-tag.create',
        'parent_flag' => 'product-tag.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'product-tag.edit',
        'parent_flag' => 'product-tag.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'product-tag.destroy',
        'parent_flag' => 'product-tag.index',
    ],

    /**
     * Brands
     */
    [
        'name' => 'Thương hiệu',
        'flag' => 'brands.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'brands.create',
        'parent_flag' => 'brands.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'brands.edit',
        'parent_flag' => 'brands.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'brands.destroy',
        'parent_flag' => 'brands.index',
    ],

    /**
     * Product collections
     */
    [
        'name' => 'Bộ sưu tập sản phẩm',
        'flag' => 'product-collections.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'product-collections.create',
        'parent_flag' => 'product-collections.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'product-collections.edit',
        'parent_flag' => 'product-collections.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'product-collections.destroy',
        'parent_flag' => 'product-collections.index',
    ],

    /**
     * Product attribute sets
     */
    [
        'name' => 'Bộ thuộc tính sản phẩm',
        'flag' => 'product-attribute-sets.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'product-attribute-sets.create',
        'parent_flag' => 'product-attribute-sets.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'product-attribute-sets.edit',
        'parent_flag' => 'product-attribute-sets.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'product-attribute-sets.destroy',
        'parent_flag' => 'product-attribute-sets.index',
    ],

    /**
     * Product attributes
     */
    [
        'name' => 'Thuộc tính sản phẩm',
        'flag' => 'product-attributes.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'product-attributes.create',
        'parent_flag' => 'product-attributes.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'product-attributes.edit',
        'parent_flag' => 'product-attributes.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'product-attributes.destroy',
        'parent_flag' => 'product-attributes.index',
    ],

    [
        'name' => 'Thuế',
        'flag' => 'tax.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'tax.create',
        'parent_flag' => 'tax.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'tax.edit',
        'parent_flag' => 'tax.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'tax.destroy',
        'parent_flag' => 'tax.index',
    ],

    [
        'name' => 'Đánh giá',
        'flag' => 'reviews.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'reviews.create',
        'parent_flag' => 'reviews.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'reviews.destroy',
        'parent_flag' => 'reviews.index',
    ],
    [
        'name' => 'Công khai/Ẩn đánh giá',
        'flag' => 'reviews.publish',
        'parent_flag' => 'reviews.index',
    ],
    [
        'name' => 'Trả lời đánh giá',
        'flag' => 'reviews.reply',
        'parent_flag' => 'reviews.index',
    ],

    [
        'name' => 'Vận chuyển',
        'flag' => 'ecommerce.shipments.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'ecommerce.shipments.create',
        'parent_flag' => 'ecommerce.shipments.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'ecommerce.shipments.edit',
        'parent_flag' => 'ecommerce.shipments.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'ecommerce.shipments.destroy',
        'parent_flag' => 'ecommerce.shipments.index',
    ],

    [
        'name' => 'Đơn hàng',
        'flag' => 'orders.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'orders.create',
        'parent_flag' => 'orders.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'orders.edit',
        'parent_flag' => 'orders.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'orders.destroy',
        'parent_flag' => 'orders.index',
    ],

    [
        'name' => 'Khuyến mãi',
        'flag' => 'discounts.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'discounts.create',
        'parent_flag' => 'discounts.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'discounts.edit',
        'parent_flag' => 'discounts.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'discounts.destroy',
        'parent_flag' => 'discounts.index',
    ],

    [
        'name' => 'Khách hàng',
        'flag' => 'customers.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'customers.create',
        'parent_flag' => 'customers.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'customers.edit',
        'parent_flag' => 'customers.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'customers.destroy',
        'parent_flag' => 'customers.index',
    ],

    [
        'name' => 'Bán nhanh',
        'flag' => 'flash-sale.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'flash-sale.create',
        'parent_flag' => 'flash-sale.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'flash-sale.edit',
        'parent_flag' => 'flash-sale.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'flash-sale.destroy',
        'parent_flag' => 'flash-sale.index',
    ],

    [
        'name' => 'Nhãn sản phẩm',
        'flag' => 'product-label.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'product-label.create',
        'parent_flag' => 'product-label.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'product-label.edit',
        'parent_flag' => 'product-label.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'product-label.destroy',
        'parent_flag' => 'product-label.index',
    ],

    [
        'name' => 'Nhập sản phẩm',
        'flag' => 'ecommerce.import.products.index',
        'parent_flag' => 'tools.data-synchronize',
    ],

    [
        'name' => 'Xuất sản phẩm',
        'flag' => 'ecommerce.export.products.index',
        'parent_flag' => 'tools.data-synchronize',
    ],

    [
        'name' => 'Đổi trả đơn hàng',
        'flag' => 'order_returns.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'order_returns.edit',
        'parent_flag' => 'order_returns.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'order_returns.destroy',
        'parent_flag' => 'order_returns.index',
    ],

    /**
     * Global option
     */
    [
        'name' => 'Tùy chọn sản phẩm',
        'flag' => 'global-option.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'global-option.create',
        'parent_flag' => 'global-option.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'global-option.edit',
        'parent_flag' => 'global-option.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'global-option.destroy',
        'parent_flag' => 'global-option.index',
    ],

    [
        'name' => 'Hóa đơn',
        'flag' => 'ecommerce.invoice.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'ecommerce.invoice.edit',
        'parent_flag' => 'ecommerce.invoice.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'ecommerce.invoice.destroy',
        'parent_flag' => 'ecommerce.invoice.index',
    ],

    [
        'name' => 'Thương mại điện tử',
        'flag' => 'ecommerce.settings',
        'parent_flag' => 'settings.index',
    ],
    [
        'name' => 'Chung',
        'flag' => 'ecommerce.settings.general',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Mẫu hóa đơn',
        'flag' => 'ecommerce.invoice-template.index',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Tiền tệ',
        'flag' => 'ecommerce.settings.currencies',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Sản phẩm',
        'flag' => 'ecommerce.settings.products',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Tìm kiếm sản phẩm',
        'flag' => 'ecommerce.settings.product-search',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Sản phẩm số',
        'flag' => 'ecommerce.settings.digital-products',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Địa điểm cửa hàng',
        'flag' => 'ecommerce.settings.store-locators',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Hóa đơn',
        'flag' => 'ecommerce.settings.invoices',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Đánh giá sản phẩm',
        'flag' => 'ecommerce.settings.product-reviews',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Khách hàng',
        'flag' => 'ecommerce.settings.customers',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Mua sắm',
        'flag' => 'ecommerce.settings.shopping',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Thuế',
        'flag' => 'ecommerce.settings.taxes',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Vận chuyển',
        'flag' => 'ecommerce.settings.shipping',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Quy tắc vận chuyển',
        'flag' => 'ecommerce.shipping-rule-items.index',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'ecommerce.shipping-rule-items.create',
        'parent_flag' => 'ecommerce.shipping-rule-items.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'ecommerce.shipping-rule-items.edit',
        'parent_flag' => 'ecommerce.shipping-rule-items.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'ecommerce.shipping-rule-items.destroy',
        'parent_flag' => 'ecommerce.shipping-rule-items.index',
    ],
    [
        'name' => 'Nhập hàng loạt',
        'flag' => 'ecommerce.shipping-rule-items.bulk-import',
        'parent_flag' => 'ecommerce.shipping-rule-items.index',
    ],
    [
        'name' => 'Theo dõi',
        'flag' => 'ecommerce.settings.tracking',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Tiêu chuẩn và định dạng',
        'flag' => 'ecommerce.settings.standard-and-format',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Thanh toán',
        'flag' => 'ecommerce.settings.checkout',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Đổi trả',
        'flag' => 'ecommerce.settings.return',
        'parent_flag' => 'ecommerce.settings',
    ],

    [
        'name' => 'Bán nhanh',
        'flag' => 'ecommerce.settings.flash-sale',
        'parent_flag' => 'ecommerce.settings',
    ],
    [
        'name' => 'Thông số sản phẩm',
        'flag' => 'ecommerce.settings.product-specification',
        'parent_flag' => 'ecommerce.settings',
    ],

    [
        'name' => 'Xuất danh mục sản phẩm',
        'flag' => 'product-categories.export',
        'parent_flag' => 'tools.data-synchronize',
    ],
    [
        'name' => 'Nhập danh mục sản phẩm',
        'flag' => 'product-categories.import',
        'parent_flag' => 'tools.data-synchronize',
    ],
    [
        'name' => 'Xuất đơn hàng',
        'flag' => 'orders.export',
        'parent_flag' => 'tools.data-synchronize',
    ],

    [
        'name' => 'Thông số sản phẩm',
        'flag' => 'ecommerce.product-specification.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'Nhóm thông số',
        'flag' => 'ecommerce.specification-groups.index',
        'parent_flag' => 'ecommerce.product-specification.index',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'ecommerce.specification-groups.create',
        'parent_flag' => 'ecommerce.specification-groups.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'ecommerce.specification-groups.edit',
        'parent_flag' => 'ecommerce.specification-groups.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'ecommerce.specification-groups.destroy',
        'parent_flag' => 'ecommerce.specification-groups.index',
    ],
    [
        'name' => 'Thuộc tính thông số',
        'flag' => 'ecommerce.specification-attributes.index',
        'parent_flag' => 'ecommerce.product-specification.index',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'ecommerce.specification-attributes.create',
        'parent_flag' => 'ecommerce.specification-attributes.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'ecommerce.specification-attributes.edit',
        'parent_flag' => 'ecommerce.specification-attributes.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'ecommerce.specification-attributes.destroy',
        'parent_flag' => 'ecommerce.specification-attributes.index',
    ],
    [
        'name' => 'Bảng thông số',
        'flag' => 'ecommerce.specification-tables.index',
        'parent_flag' => 'ecommerce.product-specification.index',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'ecommerce.specification-tables.create',
        'parent_flag' => 'ecommerce.specification-tables.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'ecommerce.specification-tables.edit',
        'parent_flag' => 'ecommerce.specification-tables.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'ecommerce.specification-tables.destroy',
        'parent_flag' => 'ecommerce.specification-tables.index',
    ],
];
