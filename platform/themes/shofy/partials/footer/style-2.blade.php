
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


{!! dynamic_sidebar('footer_top_sidebar') !!}

<footer>
    <div class="tp-footer-area tp-footer-style-2 tp-footer-style-primary">
        <div class="tp-footer-top pt-95 pb-40">
            <div class="container">
                <div class="row">
                    {!! dynamic_sidebar('footer_primary_sidebar') !!}
                </div>
            </div>
        </div>
        <div class="tp-footer-bottom">
            <div class="container">
                <div class="tp-footer-bottom-wrapper">
                    <div class="row align-items-center">
                        {!! dynamic_sidebar('footer_bottom_sidebar') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
