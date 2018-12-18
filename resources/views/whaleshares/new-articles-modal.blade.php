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
                    <li><a href="https://steemit.com/accusta/@semasping/accusta-for-steem-weekly-support-2-v">[Accusta for Steem] - Weekly Support: #2 (v)</a></li>
                    <li><a href="https://steemit.com/utopian-io/@semasping/accusta-for-steem-charts-for-author-rewards-v1-3-1">Charts for Author Rewards(v1.3.1)</a></li>
                    <li><a href="https://steemit.com/utopian-io/@semasping/accusta-for-steem-export-to-csv-block-listener-v1-2-1">Export to csv, Block listener (v1.2.1)</a></li>
                    <li><a href="https://steemit.com/utopian-io/@semasping/accusta-for-steem-curation-rewards-and-update-layout-v1-1-1">Curation rewards and update layout (v1.1.1)</a></li>
                    {{--<li><a href="https://steemit.com/utopian-io/@semasping/accusta-mongodb-as-storage-more-witness-info-and-etc-v-0-9">[Accusta] MongoDB as storage, More witness info and etc. (v 0.9)</a></li>
                    <li><a href="https://steemit.com/utopian-io/@semasping/accusta-witness-s-rewards-v-0-7">[Accusta] Witness`s rewards (v 0.7)</a></li>
                    <li><a href="https://steemit.com/utopian-io/@semasping/accusta-now-opensorce-v-0-6-account-statistics-service-for-steemit">[Accusta] Now opensorce (v:0.6) - Account statistics service for Steemit</a></li>--}}
                    <li><a href=""></a></li>
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
                delayTime: 15000,
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