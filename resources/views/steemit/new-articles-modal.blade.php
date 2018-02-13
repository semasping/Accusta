<style>
    .slickWindow .posts{
        margin-left: 20px;
    }
    .slickWindow .posts li{
        margin-top: 10px;
    }
    .slickWindow .title{
        font-size: 15pt;
    }
</style>
<!-- ========== START COPYING HERE ========== -->

<div id="popup-1" class="slickModal new-articles-modal-1302">
    <div class="slickWindow">
        <div>
            <div class="title">Please consider to support my posts about developing this Service</div>
            <div class="posts">
                <ul>
                    <li>
                        <a href="https://steemit.com/steem-dev/@semasping/introduction-of-new-service-statistic-of-steemit-accounts-accusta" target="_blank">Introduction
                            of new service 'Statistic of steemit Accounts' - [Accusta]</a></li>
                    <li><a href="https://steemit.com/accusta/@semasping/accusta-update-new-functions-v-0-3" target="_blank">[Accusta]
                            Update. New functions (v:0.3)</a></li>
                    <li>
                        <a href="https://steemit.com/accusta/@semasping/accusta-new-functions-added-history-transaction-with-filters-v-0-4" target="_blank">[Accusta]
                            New functions added: History transaction with filters (v:0.4)</a></li>
                    <li><a href="https://steemit.com/accusta/@semasping/accusta-export-to-csv-v-0-55" target="_blank">[Accusta] Export
                            to CSV (v:0.55)</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>


@section('js')

    <!-- Slick modal settings -->
    <script type="text/javascript">
        $(document).ready(function () {
            // Modal 1
            $('#popup-1').slickModals({
                // Hide on pages
                hideOnPages: ['/page1/', '/page2/', '/page3/'],
                // Popup type
                popupType: 'delayed',
                delayTime: 5000,
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
                setCookie: true,
                cookieDays: 7,
                cookieTriggerClass: 'new-articles-modal-1302',
                cookieName: 'new-articles-modal-1302',
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
                popupWidth: '480px',
                popupHeight: '280px',
                popupLocation: 'bottomRight',
                popupAnimationDuration: '0.4',
                popupAnimationEffect: 'unFold',
                popupBoxShadow: '0 0 20px rgba(0,0,0,0.4)',
                popupBackground: 'rgba(255, 255, 255, 1)',
                popupRadius: '4px',
                popupMargin: '30px',
                popupPadding: '30px',
                // Mobile rules
                showOnMobile: true,
                responsive: true,
                mobileBreakPoint: '480px',
                mobileLocation: 'center',
                mobileWidth: '90%',
                mobileHeight: '280px',
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