<style>
    .mobile {
        display: none !important;
    }

    .product-meta {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        color: #6b7280 !important;
        font-size: 1rem !important;
    }

    @media (max-width: 768px) {
        .mobile {
            display: block !important;
        }

        .desktop {
            display: none !important;
        }

        .breadcrumb__area {
            margin-bottom: 0 !important;
        }

        .tp-page-area {
            padding-top: 0 !important;
        }

        .main-title {
            font-size: 1.8rem !important;
            font-weight: bold !important;
            color: #2c3e50 !important;
            margin: 1rem 0 !important;
            text-align: left !important;
            padding: 0 1rem !important;
        }

        .horizontal-scroll-container {
            overflow-x: auto !important;
            overflow-y: hidden !important;
            -webkit-overflow-scrolling: touch !important;
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
            padding: 0 0.5rem !important;
            /* margin-bottom: 1.5rem !important; */
        }

        .horizontal-scroll-container::-webkit-scrollbar {
            display: none !important;
        }

        .product-row {
            display: flex !important;
            gap: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }

        .product-card {
            background: white !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
            overflow: hidden !important;
            flex: 0 0 160px !important;
            width: 160px !important;
            position: relative !important;
        }

        .discount-badge {
            position: absolute !important;
            top: 8px !important;
            left: 8px !important;
            background: #4f46e5 !important;
            color: white !important;
            padding: 4px 8px !important;
            border-radius: 6px !important;
            font-weight: bold !important;
            font-size: 0.75rem !important;
            z-index: 2 !important;
        }

        .discount-badge.high-discount {
            background: #dc2626 !important;
        }

        .discount-badge.premium {
            background: #059669 !important;
        }

        .special-badge {
            position: absolute !important;
            top: 8px !important;
            right: 8px !important;
            background: white !important;
            color: #4f46e5 !important;
            padding: 2px 6px !important;
            border-radius: 10px !important;
            font-size: 0.6rem !important;
            font-weight: bold !important;
            border: 1px solid #4f46e5 !important;
            z-index: 2 !important;
        }

        .product-image {
            width: 100% !important;
            /* height: 120px !important; */
            object-fit: cover !important;
        }

        /* .product-info {
            padding: 0.75rem !important;
        } */

        .product-title {
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            color: #1f2937 !important;
            margin-bottom: 0.5rem !important;
            line-height: 1.2 !important;

            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;

            overflow: hidden !important;
            text-overflow: ellipsis !important;
            height: calc(1.2em * 2) !important;
            /* line-height * số dòng */
        }

        .product-price,
        .new-price {
            font-size: 0.8rem !important;
            font-weight: bold !important;
            color: #4f46e5 !important;
            margin-bottom: 0.5rem !important;
        }

        .old-price {
            font-size: 12px !important;
        }

        .product-meta {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            color: #6b7280 !important;
            font-size: 0.7rem !important;
        }

        .sold-count,
        .location {
            display: flex !important;
            align-items: center !important;
            gap: 2px !important;
        }

        .section-title {
            font-size: 1.3rem !important;
            font-weight: bold !important;
            color: #2c3e50 !important;
            margin: 2rem 1rem 1rem 1rem !important;
            border-bottom: 2px solid #4f46e5 !important;
            padding-bottom: 0.5rem !important;
            display: inline-block !important;
        }

        /* Scroll indicator */
        .scroll-indicator {
            text-align: center !important;
            color: #6b7280 !important;
            font-size: 0.8rem !important;
            margin-bottom: 1rem !important;
            padding: 0 1rem !important;
        }

        .tp-product-badge {
            left: 30px !important;
            right: 0 !important;
            top: 0 !important;
            text-align: end !important;
        }

        .tp-product-action-5 {
            opacity: 1 !important;
            visibility: visible !important;
            top: 40px !important;
            left: 10px !important;
        }

        .tp-product-add-to-compare-btn {
            display: none !important;
        }

        .tp-product-action-btn-2 {
            width: 30px !important;
            height: 30px !important;
            line-height: 30px !important;
        }

        .icon-ft-item {
            width: 14px !important;
        }
    }

    /* Mobile optimizations */
    @media (max-width: 576px) {
        .product-card {
            flex: 0 0 180px !important;
            width: 180px !important;
        }

        .main-title {
            font-size: 1.6rem !important;
        }
    }

    /* Custom scrollbar for desktop */
    @media (min-width: 768px) {
        .horizontal-scroll-container {
            scrollbar-width: thin !important;
            scrollbar-color: #cbd5e1 transparent !important;
        }

        .horizontal-scroll-container::-webkit-scrollbar {
            display: block !important;
            height: 4px !important;
        }

        .horizontal-scroll-container::-webkit-scrollbar-track {
            background: transparent !important;
        }

        .horizontal-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
            border-radius: 2px !important;
        }
    }
</style>
