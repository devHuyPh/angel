<script
    src="{{ asset('vendor/core/plugins/marketplace/js/marketplace.js') }}?v={{ MarketplaceHelper::getAssetVersion() }}"
></script>
<script>
function closeSidebar(event) {
        if (event.target.id === 'sidebarOverlay') {
            document.getElementById('sidebarOverlay').style.display = 'none';
        }
    }
</script>