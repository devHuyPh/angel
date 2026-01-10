
<style>
.zalo-icon {
    position: fixed;
    bottom: 20px; 
    right: 14px;
    width: 40px; 
    height: 40px;
    z-index: 1000;
}

.zalo-icon img {
    width: 100%;
    height: 100%;
    filter: drop-shadow(0px 0px 5px #00AEEF);
    animation: glow 1s infinite alternate;
}

@keyframes glow {
    0% { filter: drop-shadow(0px 0px 5px #00AEEF); }
    100% { filter: drop-shadow(0px 0px 15px #00AEEF); }
}
</style>


@php
    $footerPrimarySidebar = dynamic_sidebar('footer_primary_sidebar');
    $footerBottomSidebar = dynamic_sidebar('footer_bottom_sidebar');
@endphp

{!! dynamic_sidebar('footer_top_sidebar') !!}

<footer>
    <div class="tp-footer-area">
        @if($footerPrimarySidebar)
            <div class="tp-footer-top">
                <div class="container">
                    <div class="row">
                        {!! $footerPrimarySidebar !!}
                    </div>
                </div>
            </div>
        @endif
        @if ($footerBottomSidebar)
            <div class="tp-footer-bottom">
                <div class="container">
                    <div class="tp-footer-bottom-wrapper" @if(! $footerPrimarySidebar) style="border-top: 0" @endif>
                        <div class="row align-items-center">
                            {!! $footerBottomSidebar !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</footer>
