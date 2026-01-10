<style>
    .desktop {
        display: block !important;
    }

    .mobile {
        display: none !important;
    }

    .mobile-container {
        background: linear-gradient(135deg, #e8e4f3 0%, #d1c7e8 100%) !important;
        width: 100% !important;
        max-width: 100vw !important;
        padding: 15px !important;
        margin: 0 !important;
    }

    .header-wrapper {
        background: rgba(255, 255, 255, 0.95) !important;
        border-radius: 20px !important;
        padding: 5px 20px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
        width: 80% !important;
        margin: 0 auto !important;
    }

    .search-row {
        display: flex !important;
        align-items: center !important;
        width: 100% !important;
        gap: 12px !important;
    }

    .search-icon-wrapper {
        flex-shrink: 0 !important;
        width: 24px !important;
        height: 24px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .search-icon {
        color: #9ca3af !important;
        font-size: 18px !important;
    }

    .search-input-wrapper {
        flex: 1 !important;
        min-width: 0 !important;
    }

    .search-input {
        width: 100% !important;
        height: 40px !important;
        border: none !important;
        outline: none !important;
        background: transparent !important;
        font-size: 16px !important;
        color: #374151 !important;
        padding: 8px 0 !important;
        line-height: 1.4 !important;
    }

    .search-input::placeholder {
        color: #9ca3af !important;
        font-size: 16px !important;
    }

    .cart-wrapper {
        flex-shrink: 0 !important;
        position: relative !important;
        width: 44px !important;
        height: 44px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        border-radius: 12px !important;
        transition: background-color 0.2s ease !important;
    }

    .cart-wrapper:active {
        background-color: rgba(0, 0, 0, 0.05) !important;
    }

    .cart-icon {
        color: #6b7280 !important;
        font-size: 30px !important;
        width: 30px !important;
        height: 30px !important;
    }

    .cart-badge {
        position: absolute !important;
        top: 2px !important;
        right: 0 !important;
        background: #dc2626 !important;
        color: white !important;
        border-radius: 50% !important;
        width: 20px !important;
        height: 20px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        border: 2px solid white !important;
        line-height: 1 !important;
    }

    /* iOS Safari specific fixes */
    @supports (-webkit-touch-callout: none) {
        .search-input {
            font-size: 16px !important;
            /* Prevents zoom on iOS */
        }
    }

    /* Android specific optimizations */
    @media screen and (max-width: 480px) {
        .desktop {
            display: none !important;
        }

        .mobile {
            display: block !important;
        }

        .mobile-container {
            padding: 10px !important;
        }

        .header-wrapper {
            padding: 5px 16px !important;
            border-radius: 16px !important;
        }

        .search-row {
            gap: 10px !important;
        }

        .cart-wrapper {
            width: 40px !important;
            height: 40px !important;
        }
    }

    /* Very small screens */
    @media screen and (max-width: 360px) {
        .search-input {
            font-size: 14px !important;
        }

        .search-input::placeholder {
            font-size: 14px !important;
        }
    }

    /* Landscape orientation */
    @media screen and (orientation: landscape) and (max-height: 500px) {
        .mobile-container {
            padding: 8px !important;
        }

        .header-wrapper {
            padding: 5px 16px !important;
        }
    }

    /* High DPI displays */
    @media (-webkit-min-device-pixel-ratio: 2),
    (min-resolution: 192dpi) {
        .header-wrapper {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
        }
    }

    /* Demo styling */
    .demo-info {
        text-align: center !important;
        color: #6b7280 !important;
        font-size: 14px !important;
        margin-top: 20px !important;
        padding: 0 20px !important;
    }
    /* Back to top button adjustments */
    .back-to-top-wrapper {
        z-index: 9999 !important;
    }

    @media (max-width: 1280px) {
        .back-to-top-wrapper{
            right: auto !important;  
            left: 20px !important;   
        }
        .back-to-top-wrapper.back-to-top-btn-show {
            bottom: 70px !important;
            
        }
    }
</style>
