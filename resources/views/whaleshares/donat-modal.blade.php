<style>
    .slickWindow{
        align-content: center;
        text-align: center;
    }
    .slickWindow .posts{
        margin-left: 20px;
    }
    .slickWindow .posts li{
        margin-top: 10px;
    }
    .slickWindow .title{
        margin-top: 15px;

    }
    .slickWindow .btn {
        background: #52a6c8;
        color: #fff;
        border-radius: 3px;
        height: 40px;
        line-height: 40px;
        padding: 0 20px;
        display: inline-block;
        border: 0;
        font-size: 14px;
        cursor: pointer;
        margin-top: 10px;
    }
</style>
<!-- ========== START COPYING HERE ========== -->

<div id="popup-d" class="slickModal d-modal-1302">
    <div class="slickWindow">
        <div>
            <p class="title">If you like Accusta & you're feeling generous...</p>
            <a href="https://commerce.coinbase.com/checkout/64550931-4373-4d39-81b8-e197aaa136ac" target="_blank" class="btn">Donate with CryptoCurrency</a><br>
            <a href="https://www.paypal.me/semasping" target="_blank" class="btn">Donate with Paypal</a>
        </div>
    </div>
</div>


@section('js')

    <!-- Slick modal settings -->
    <script type="text/javascript">
        $(document).ready(function () {
            // Modal 1
            $('#popup-d').slickModals({
                // Hide on pages
                hideOnPages: ['/page1/', '/page2/', '/page3/'],
                // Popup type
                popupType: 'delayed',
                delayTime: 1500,
                scrollTopDistance: 400,
                // Auto closing
                autoClose: false,
                autoCloseDelay: 10000,
                // Statistics
                enableStats: false,
                fileLocation: 'slickStats/collect.php',
                modalName: 'My awesome modal 1',
                modalSummary: 'Lorem ipsum dolor sit amet',
                callToAction: 'cta',
                // Popup cookies
                setCookie: false,
                cookieDays: 365,
                cookieTriggerClass: 'd-modal-1302',
                cookieName: 'd-modal-1302',
                cookieScope: 'domain',
                // Overlay styling
                overlayVisible: false,
                overlayClosesModal: true,
                overlayColor: 'rgba(0, 0, 0, 0.8)',
                overlayAnimationDuration: '0.4',
                overlayAnimationEffect: 'fadeIn',
                // Background effects
                pageAnimationDuration: '0.4',
                pageAnimationEffect: 'none',
                pageBlurRadius: '1px',
                pageScaleValue: '0.9',
                pageMoveDistance: '30%',
                // Popup styling
                popupWidth: '280px',
                popupHeight: '180px',
                popupLocation: 'bottomLeft',
                popupAnimationDuration: '0.4',
                popupAnimationEffect: 'unFold',
                popupBoxShadow: '0 0 20px rgba(0,0,0,0.4)',
                popupBackground: 'rgba(255, 255, 255, 1)',
                popupRadius: '4px',
                popupMargin: '10px',
                popupPadding: '10px',
                // Mobile rules
                showOnMobile: true,
                responsive: true,
                mobileBreakPoint: '280px',
                mobileLocation: 'center',
                mobileWidth: '90%',
                mobileHeight: '180px',
                mobileRadius: '0px',
                mobileMargin: '0px',
                mobilePadding: '24px',
                // Animate content
                contentAnimation: true,
                contentAnimationEffect: 'slideBottom',
                contentAnimationDuration: '0.4',
                contentAnimationDelay: '0.4',
                // Youtube videos
                videoSupport: false,
                videoAutoPlay: false,
                videoStopOnClose: false,
                // Close and reopen button
                addCloseButton: true,
                buttonStyle: 'icon',
                enableESC: true,
                reopenClass: 'openSlickModal-1',
                // Additional events
                onSlickLoad: function () {
                    // Your code goes here
                },
                onSlickClose: function () {
                    // Your code goes here
                }
            });
        });
    </script>
@append