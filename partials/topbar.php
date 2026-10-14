<?php

$user = $obj->getSingleData('vw_user_info', ['where' => ['UserId', '=', $userId]]);
?>
 <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<style>
    .sms-balance-card {
    background: linear-gradient(135deg, #45b369, #2fa46f);
    padding: 3px 3px;
    border-radius: 8px;
    color: #fff;
    margin-bottom: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.12);
}

.sms-icon {
    width: 29px;
    height: 29px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sms-icon iconify-icon {
    font-size: 18px;
    color: #fff;
}

.sms-text small {
    font-size: 9px;
    opacity: 0.9;
    display: block;
    line-height: 0;
}

.sms-text strong {
    font-size: 13px;
    font-weight: 600;
}



@media (min-width: 300px) {
  .navbar-header {
      background-color: var(--white);
      position: sticky;
      top: 0;
      padding: 1rem 0.5rem;
      z-index: 2;
      height: 100%;
    }
    .count-down-notice {
        font-weight: bold;
        font-size: 13px;
        background: linear-gradient(45deg, #dc3545, #ff6b6b);
        color: white;
        padding: 5px 10px;
        border-radius: 25px;
        display: inline-block;
    }
    
}


</style>

<div class="navbar-header">
    <div class="row align-items-center justify-content-between ">
        <div class="col-12 col-md-auto">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="sidebar-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon>
                    <iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
                </button>
                <button type="button" class="sidebar-mobile-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
                </button>
                <!-- <form class="navbar-search">
                    <input type="text" name="search" placeholder="Search">
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form> -->

                <?php
                $supportLink = $obj->getSettingValue('supportLink ', 'supportLink');
                $smsapi = $obj->getSettingValue('sms', 'pass');
                $supportLink = isset($supportLink) ? $supportLink : "";
                $email = $obj->getSettingValue('email', 'email');
                $email = isset($email) ? $email : "";
                $token = urlencode('HA%rnP#)27{hsS');
                $mobile = $obj->getSettingValue('mobile ', 'mobile');
                $paymentUrl = "https://billing.bsdisp.com/userportal/directLogin/" . $mobile;
                
                $balance = $obj->getSmsBalance();
                ?>
                <a target="_blank" href="<?php echo $paymentUrl ?>" class="btn waves-effect waves-light btn-sm d-flex justify-content-center align-items-center" style="
    background-color: #E2136E;
    color: white;
    margin-bottom: 10px;
    gap: 2px;
    padding: 6px;
">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 60 54" style="color: white; flex-shrink: 0;">
                        <g fill="currentColor" transform="translate(-62 0)">
                            <path d="m82.9 25.9 3.3 14.6 21.5-10.7zM89 3.8 83.2 25l24 3.8zM62.8.6l25.5 3.1-6 21.8zM62.5 4.8h3l8 10.3zM108.4 29.6l-7.5-10.3 12-2.3zM107.2 32.5l.7-2.2-18.7 9.6zM82.4 26.3 86.3 44l-11.6 9.4zM111.8 22.3h7l-5.1-5.1z"></path>
                        </g>
                    </svg>
                    Payment
                </a>
                 <?php if (!empty($email)) { ?>
                    <a target="_blank" href="<?php echo $supportLink; ?>/third-party-login?email=<?php echo $email; ?>&token=<?php echo $token; ?>" class="btn btn-sm btn-primary d-flex justify-content-center align-items-center" style="
    margin-bottom: 10px;
    padding: 6px;
">
                        Support
                    </a>
                <?php } ?>
                <?php if($smsapi){ ?>
                   <div class="sms-balance-card d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="sms-icon">
                                <iconify-icon icon="lucide:message-square"></iconify-icon>
                            </div>
                            <div class="sms-text">
                                <small>SMS Balance</small>
                                <strong><?php echo $balance['balance'] ?? 0; ?> TK</strong>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        
            <div class="col-auto">
                <div class="d-flex flex-wrap align-items-center">
                   
    
                    <?php
                    $supportLink = $obj->getSettingValue('supportLink ', 'supportLink');
                    $smsapi = $obj->getSettingValue('sms', 'pass');
                    $supportLink = isset($supportLink) ? $supportLink : "";
                    $email = $obj->getSettingValue('email', 'email');
                    $email = isset($email) ? $email : "";
                    $token = urlencode('HA%rnP#)27{hsS');
                    $mobile = $obj->getSettingValue('mobile ', 'mobile');
                    $paymentUrl = "https://billing.bsdisp.com/userportal/directLogin/" . $mobile;
                    
                    $balance = $obj->getSmsBalance();
                    ?>
                   
                    <?php
                    $noticeInfo = $obj->rawSqlSingle("SELECT * FROM countdown_notice WHERE id = 1");
    
                    if (!$noticeInfo) {
                        // কোনো নোটিশ ডাটা না থাকলে কিছুই দেখাবে না
                        $showNotice = false;
                    } else {
                        $showNotice = true;
    
                        // বর্তমান তারিখ
                        $today = new DateTime();
    
                        // বিলের ডেটা
                        $billDate = (int)$noticeInfo['bill_date'];         // 1-31
                        $billMonth = (int)$noticeInfo['bill_month'];       // 1-12
                        $billYear = (int)$noticeInfo['bill_year'];         // 2025, 2026
                        $displayBefore = (int)$noticeInfo['notice_display_before']; // নোটিশ কত দিন আগে
                        $gracePeriod = (int)$noticeInfo['grace_period'];   // গ্রেস পিরিয়ড দিন (0,3,7)
    
                        // টার্গেট বিলের তারিখ তৈরি (মাসের শেষ দিন অটো হ্যান্ডেল)
                        $targetDate = DateTime::createFromFormat('Y-m-d', "$billYear-$billMonth-$billDate");
                        if (!$targetDate) {
                            // ইনভ্যালিড তারিখ (যেমন ফেব্রুয়ারি 30) → মাসের শেষ দিন
                            $targetDate = new DateTime();
                            $targetDate->setDate($billYear, $billMonth, 1);
                            $targetDate->modify('last day of this month');
                            $targetDate->setTime(23, 59, 59);
                        } else {
                            $targetDate->setTime(23, 59, 59);
                        }
    
                        // গ্রেস পিরিয়ডের শেষ তারিখ
                        $graceEndDate = clone $targetDate;
                        $graceEndDate->modify("+{$gracePeriod} days");
    
                        // নোটিশ শুরুর তারিখ (বিলের displayBefore দিন আগে)
                        $startDisplayDate = clone $targetDate;
                        $startDisplayDate->modify("-{$displayBefore} days");
    
                        // কাউন্টডাউন টার্গেট ও লেবেল
                        $countdownTarget = $targetDate;
                        $countdownLabel = "";
                        $showCountdown = true;
    
                        if ($today > $targetDate && $today <= $graceEndDate) {
                            // গ্রেস পিরিয়ড চলছে
                            $countdownTarget = $graceEndDate;
                            $countdownLabel = "Grace period: ";
                        } elseif ($today > $graceEndDate) {
                            $showCountdown = false;
                        }
    
                        $amethod = "POST";
    
                        if ($noticeInfo['complete_shutdown'] == 1) {
                            $amethod = "";
                        } elseif ($noticeInfo['forcefully_stop'] == 2 && $noticeInfo['permanently_stop'] == 2) {
                            if ($today > $graceEndDate) {
                                $amethod = "";
                            }
                        }
                    }
                    ?>
    
                    <?php if ($showNotice && $noticeInfo['complete_shutdown'] != 1): ?>
                        <?php if ($noticeInfo['notice_displayable'] == 1 && $noticeInfo['forcefully_stop'] == 2 && $noticeInfo['permanently_stop'] == 2): ?>
    
                            <?php if ($today >= $startDisplayDate && $today <= $targetDate): ?>
                                <p>
                                    <span
                                        style="font-weight: bold; font-size: 20px;"
                                        class="text-danger"
                                        id="countdown">
                                    </span>
                                </p>
                            <?php elseif ($today > $targetDate && $today <= $graceEndDate): ?>
                                <p>
                                    <span style="font-weight: bold; font-size: 13px; background: linear-gradient(45deg, #dc3545, #ff6b6b); color: white; padding: 5px 10px; border-radius: 25px; display: inline-block;" id="countdown">
                                        <?= htmlspecialchars($countdownLabel) ?>
                                    </span>
                                </p>
    
                            <?php elseif ($today > $graceEndDate): ?>
                                <p>
                                    <span style="font-weight: bold; font-size: 13px; background: linear-gradient(45deg, #dc3545, #ff6b6b); color: white; padding: 5px 10px; border-radius: 25px; display: inline-block;" id="countdown">
                                        Service Suspended
                                    </span>
                                </p>
                            <?php endif; ?>
                            <?php if ($showCountdown): ?>
                                <script>
                                    (function() {
                                        const targetTimestamp = <?= $countdownTarget->getTimestamp() * 1000 ?>;
                                        const label = "<?= addslashes($countdownLabel) ?>";
    
                                        function updateCountdown() {
                                            const now = new Date().getTime();
                                            const diff = targetTimestamp - now;
    
                                            if (diff <= 0) {
                                                document.getElementById("countdown").innerHTML = "EXPIRED";
                                                // location.reload(); 
                                                return;
                                            }
    
                                            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                                            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
                                            let text = days + "d " + hours + "h " + minutes + "m " + seconds + "s";
                                            if (label) {
                                                text = label + text;
                                            }
    
                                            document.getElementById("countdown").innerHTML = text;
                                        }
    
                                        updateCountdown();
                                        setInterval(updateCountdown, 1000);
                                    })();
                                </script>
                            <?php endif; ?>
    
                        <?php endif; ?>
                    <?php elseif ($showNotice && $noticeInfo['complete_shutdown'] == 1): ?>
                        <div class="alert alert-danger text-center" role="alert">
                            <h6 style="text-decoration: underline;">System Shutdown</h6>
                            <p><strong>Service is completely suspended.</strong><br>
                                Please contact administrator to restore service.
                            </p>
                        </div>
                    <?php endif; ?>
                    
                
    
                       
                    
                </div>
            </div>
            <div class="col-auto">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <button type="button" data-theme-toggle class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"></button>
                    <div class="dropdown d-none d-sm-inline-block">
                        <button class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center" type="button" data-bs-toggle="dropdown">
                            <img src="assets/images/flags/flag6.png" alt="image" class="w-24 h-24 object-fit-cover rounded-circle">
                        </button>
                        <!-- <div class="dropdown-menu to-top dropdown-menu-sm">
                            <div class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <h6 class="text-lg text-primary-light fw-semibold mb-0">Choose Your Language</h6>
                                </div>
                            </div>
    
                            <div class="max-h-400-px overflow-y-auto scroll-sm pe-8">
                                <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                    <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="english">
                                        <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <img src="assets/images/flags/flag1.png" alt="" class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                            <span class="text-md fw-semibold mb-0">English</span>
                                        </span>
                                    </label>
                                    <input class="form-check-input" type="radio" name="crypto" id="english">
                                </div>
    
                                <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                    <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="japan">
                                        <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <img src="assets/images/flags/flag2.png" alt="" class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                            <span class="text-md fw-semibold mb-0">Japan</span>
                                        </span>
                                    </label>
                                    <input class="form-check-input" type="radio" name="crypto" id="japan">
                                </div>
    
                                <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                    <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="france">
                                        <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <img src="assets/images/flags/flag3.png" alt="" class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                            <span class="text-md fw-semibold mb-0">France</span>
                                        </span>
                                    </label>
                                    <input class="form-check-input" type="radio" name="crypto" id="france">
                                </div>
    
                                <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                    <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="germany">
                                        <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <img src="assets/images/flags/flag4.png" alt="" class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                            <span class="text-md fw-semibold mb-0">Germany</span>
                                        </span>
                                    </label>
                                    <input class="form-check-input" type="radio" name="crypto" id="germany">
                                </div>
    
                                <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                    <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="korea">
                                        <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <img src="assets/images/flags/flag5.png" alt="" class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                            <span class="text-md fw-semibold mb-0">South Korea</span>
                                        </span>
                                    </label>
                                    <input class="form-check-input" type="radio" name="crypto" id="korea">
                                </div>
    
                                <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                    <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="bangladesh">
                                        <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <img src="assets/images/flags/flag6.png" alt="" class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                            <span class="text-md fw-semibold mb-0">Bangladesh</span>
                                        </span>
                                    </label>
                                    <input class="form-check-input" type="radio" name="crypto" id="bangladesh">
                                </div>
    
                                <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                    <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="india">
                                        <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <img src="assets/images/flags/flag7.png" alt="" class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                            <span class="text-md fw-semibold mb-0">India</span>
                                        </span>
                                    </label>
                                    <input class="form-check-input" type="radio" name="crypto" id="india">
                                </div>
                                <div class="form-check style-check d-flex align-items-center justify-content-between">
                                    <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="canada">
                                        <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <img src="assets/images/flags/flag8.png" alt="" class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                                            <span class="text-md fw-semibold mb-0">Canada</span>
                                        </span>
                                    </label>
                                    <input class="form-check-input" type="radio" name="crypto" id="canada">
                                </div>
                            </div>
                        </div> -->
                    </div><!-- Language dropdown end -->
    
                    <!-- <div class="dropdown">
                        <button class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center" type="button" data-bs-toggle="dropdown">
                            <iconify-icon icon="mage:email" class="text-primary-light text-xl"></iconify-icon>
                        </button>
                        <div class="dropdown-menu to-top dropdown-menu-lg p-0">
                            <div class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <h6 class="text-lg text-primary-light fw-semibold mb-0">Message</h6>
                                </div>
                                <span class="text-primary-600 fw-semibold text-lg w-40-px h-40-px rounded-circle bg-base d-flex justify-content-center align-items-center">05</span>
                            </div>
    
                            <div class="max-h-400-px overflow-y-auto scroll-sm pe-4">
    
                                <a href="javascript:void(0)" class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                    <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <span class="w-40-px h-40-px rounded-circle flex-shrink-0 position-relative">
                                            <img src="assets/images/notification/profile-3.png" alt="">
                                            <span class="w-8-px h-8-px bg-success-main rounded-circle position-absolute end-0 bottom-0"></span>
                                        </span>
                                        <div>
                                            <h6 class="text-md fw-semibold mb-4">Kathryn Murphy</h6>
                                            <p class="mb-0 text-sm text-secondary-light text-w-100-px">hey! there i’m...</p>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="text-sm text-secondary-light flex-shrink-0">12:30 PM</span>
                                        <span class="mt-4 text-xs text-base w-16-px h-16-px d-flex justify-content-center align-items-center bg-warning-main rounded-circle">8</span>
                                    </div>
                                </a>
    
                                <a href="javascript:void(0)" class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                    <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <span class="w-40-px h-40-px rounded-circle flex-shrink-0 position-relative">
                                            <img src="assets/images/notification/profile-4.png" alt="">
                                            <span class="w-8-px h-8-px  bg-neutral-300 rounded-circle position-absolute end-0 bottom-0"></span>
                                        </span>
                                        <div>
                                            <h6 class="text-md fw-semibold mb-4">Kathryn Murphy</h6>
                                            <p class="mb-0 text-sm text-secondary-light text-w-100-px">hey! there i’m...</p>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="text-sm text-secondary-light flex-shrink-0">12:30 PM</span>
                                        <span class="mt-4 text-xs text-base w-16-px h-16-px d-flex justify-content-center align-items-center bg-warning-main rounded-circle">2</span>
                                    </div>
                                </a>
    
                                <a href="javascript:void(0)" class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between bg-neutral-50">
                                    <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <span class="w-40-px h-40-px rounded-circle flex-shrink-0 position-relative">
                                            <img src="assets/images/notification/profile-5.png" alt="">
                                            <span class="w-8-px h-8-px bg-success-main rounded-circle position-absolute end-0 bottom-0"></span>
                                        </span>
                                        <div>
                                            <h6 class="text-md fw-semibold mb-4">Kathryn Murphy</h6>
                                            <p class="mb-0 text-sm text-secondary-light text-w-100-px">hey! there i’m...</p>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="text-sm text-secondary-light flex-shrink-0">12:30 PM</span>
                                        <span class="mt-4 text-xs text-base w-16-px h-16-px d-flex justify-content-center align-items-center bg-neutral-400 rounded-circle">0</span>
                                    </div>
                                </a>
    
                                <a href="javascript:void(0)" class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between bg-neutral-50">
                                    <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <span class="w-40-px h-40-px rounded-circle flex-shrink-0 position-relative">
                                            <img src="assets/images/notification/profile-6.png" alt="">
                                            <span class="w-8-px h-8-px bg-neutral-300 rounded-circle position-absolute end-0 bottom-0"></span>
                                        </span>
                                        <div>
                                            <h6 class="text-md fw-semibold mb-4">Kathryn Murphy</h6>
                                            <p class="mb-0 text-sm text-secondary-light text-w-100-px">hey! there i’m...</p>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="text-sm text-secondary-light flex-shrink-0">12:30 PM</span>
                                        <span class="mt-4 text-xs text-base w-16-px h-16-px d-flex justify-content-center align-items-center bg-neutral-400 rounded-circle">0</span>
                                    </div>
                                </a>
    
                                <a href="javascript:void(0)" class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                    <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <span class="w-40-px h-40-px rounded-circle flex-shrink-0 position-relative">
                                            <img src="assets/images/notification/profile-7.png" alt="">
                                            <span class="w-8-px h-8-px bg-success-main rounded-circle position-absolute end-0 bottom-0"></span>
                                        </span>
                                        <div>
                                            <h6 class="text-md fw-semibold mb-4">Kathryn Murphy</h6>
                                            <p class="mb-0 text-sm text-secondary-light text-w-100-px">hey! there i’m...</p>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="text-sm text-secondary-light flex-shrink-0">12:30 PM</span>
                                        <span class="mt-4 text-xs text-base w-16-px h-16-px d-flex justify-content-center align-items-center bg-warning-main rounded-circle">8</span>
                                    </div>
                                </a>
    
                            </div>
                            <div class="text-center py-12 px-16">
                                <a href="javascript:void(0)" class="text-primary-600 fw-semibold text-md">See All Message</a>
                            </div>
                        </div>
                    </div> -->
                    <!-- Message dropdown end -->
    
                    <!-- <div class="dropdown">
                        <button class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center" type="button" data-bs-toggle="dropdown">
                            <iconify-icon icon="iconoir:bell" class="text-primary-light text-xl"></iconify-icon>
                        </button>
                        <div class="dropdown-menu to-top dropdown-menu-lg p-0">
                            <div class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <h6 class="text-lg text-primary-light fw-semibold mb-0">Notifications</h6>
                                </div>
                                <span class="text-primary-600 fw-semibold text-lg w-40-px h-40-px rounded-circle bg-base d-flex justify-content-center align-items-center">05</span>
                            </div>
    
                            <div class="max-h-400-px overflow-y-auto scroll-sm pe-4">
                                <a href="javascript:void(0)" class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                    <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <span class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                            <iconify-icon icon="bitcoin-icons:verify-outline" class="icon text-xxl"></iconify-icon>
                                        </span>
                                        <div>
                                            <h6 class="text-md fw-semibold mb-4">Congratulations</h6>
                                            <p class="mb-0 text-sm text-secondary-light text-w-200-px">Your profile has been Verified. Your profile has been Verified</p>
                                        </div>
                                    </div>
                                    <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                                </a>
    
                                <a href="javascript:void(0)" class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between bg-neutral-50">
                                    <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <span class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                            <img src="assets/images/notification/profile-1.png" alt="">
                                        </span>
                                        <div>
                                            <h6 class="text-md fw-semibold mb-4">Ronald Richards</h6>
                                            <p class="mb-0 text-sm text-secondary-light text-w-200-px">You can stitch between artboards</p>
                                        </div>
                                    </div>
                                    <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                                </a>
    
                                <a href="javascript:void(0)" class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                    <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <span class="w-44-px h-44-px bg-info-subtle text-info-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                            AM
                                        </span>
                                        <div>
                                            <h6 class="text-md fw-semibold mb-4">Arlene McCoy</h6>
                                            <p class="mb-0 text-sm text-secondary-light text-w-200-px">Invite you to prototyping</p>
                                        </div>
                                    </div>
                                    <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                                </a>
    
                                <a href="javascript:void(0)" class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between bg-neutral-50">
                                    <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <span class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                            <img src="assets/images/notification/profile-2.png" alt="">
                                        </span>
                                        <div>
                                            <h6 class="text-md fw-semibold mb-4">Annette Black</h6>
                                            <p class="mb-0 text-sm text-secondary-light text-w-200-px">Invite you to prototyping</p>
                                        </div>
                                    </div>
                                    <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                                </a>
    
                                <a href="javascript:void(0)" class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                    <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <span class="w-44-px h-44-px bg-info-subtle text-info-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                            DR
                                        </span>
                                        <div>
                                            <h6 class="text-md fw-semibold mb-4">Darlene Robertson</h6>
                                            <p class="mb-0 text-sm text-secondary-light text-w-200-px">Invite you to prototyping</p>
                                        </div>
                                    </div>
                                    <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                                </a>
                            </div>
    
                            <div class="text-center py-12 px-16">
                                <a href="javascript:void(0)" class="text-primary-600 fw-semibold text-md">See All Notification</a>
                            </div>
    
                        </div>
                    </div> -->
                    <!-- Notification dropdown end -->
                    <?php $logo = $obj->getSettingValue('logo', 'logo'); ?>
                    <div class="dropdown">
                        <button class="d-flex justify-content-center align-items-center rounded-circle" type="button" data-bs-toggle="dropdown">
    
                            <img src="<?php echo $logo ?>" alt="image" class="w-40-px h-40-px object-fit-cover rounded-circle">
                        </button>
                        <div class="dropdown-menu to-top dropdown-menu-sm">
                            <div class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <h6 class="text-lg text-primary-light fw-semibold mb-2"><?php echo $fullName ?></h6>
                                    <span class="text-secondary-light fw-medium text-sm"><?php echo $ty ?></span>
                                </div>
                                <button type="button" class="hover-text-danger">
                                    <iconify-icon icon="radix-icons:cross-1" class="icon text-xl"></iconify-icon>
                                </button>
                            </div>
                            <ul class="to-top-list">
                                <!-- <li>
                                    <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3" href="view-profile.html">
                                        <iconify-icon icon="solar:user-linear" class="icon text-xl"></iconify-icon> My Profile</a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3" href="email.html">
                                        <iconify-icon icon="tabler:message-check" class="icon text-xl"></iconify-icon> Inbox</a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3" href="company.html">
                                        <iconify-icon icon="icon-park-outline:setting-two" class="icon text-xl"></iconify-icon> Setting</a>
                                </li> -->
                                <li>
                                    <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3" href="?page=logout">
                                        <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon> Log Out</a>
                                </li>
                            </ul>
                        </div>
                    </div><!-- Profile dropdown end -->
                </div>
            </div>
    </div>
</div>