<?php
// Landing page for Entry Operators who don't have the 'dashboard' permission.
$welcomeName = $_SESSION['userfullname'] ?? ($_SESSION['username'] ?? 'User');
$welcomeImage = $_SESSION['userimage'] ?? '';
$welcomeLogo = $obj->getSettingValue('logo', 'logo');
$welcomeInitial = mb_strtoupper(mb_substr($welcomeName, 0, 1));

$hour = (int) date('G');
$welcomeGreeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');

// permission => [page, title, description, icon, background class, text class]
$welcomeLinks = [
    'customer_view'          => ['customer_view', 'Customers', 'Browse and search customers', 'mdi-account-group-outline', 'bg-primary-50', 'text-primary-600'],
    'customer_create'        => ['customer_create', 'Create Customer', 'Add a new customer', 'mdi-account-plus-outline', 'bg-success-focus', 'text-success-main'],
    'billcollection_view'    => ['billcollection_view', 'Bill Collection', 'Collect monthly bills', 'mdi-cash-multiple', 'bg-warning-focus', 'text-warning-main'],
    'previous_due'           => ['previous_due', 'Previous Due', 'Customers with pending dues', 'mdi-cash-clock', 'bg-danger-focus', 'text-danger-main'],
    'all_paid'               => ['all_paid', 'Full Paid', 'Customers who paid in full', 'mdi-cash-check', 'bg-success-focus', 'text-success-main'],
    'add_complain'           => ['add_complain', 'Add Complain', 'Log a customer complain', 'mdi-comment-plus-outline', 'bg-info-focus', 'text-info-main'],
    'view_complain'          => ['view_complain', 'Complains', 'Track open complains', 'mdi-comment-question-outline', 'bg-lilac-100', 'text-lilac-600'],
    'mikrotik_online_secret' => ['mikrotik_online_secret', 'Online Users', 'Live Mikrotik sessions', 'mdi-router-wireless', 'bg-primary-50', 'text-primary-600'],
    'mikrotik_connection'    => ['mikrotik_connection', 'Mikrotik List', 'Manage Mikrotik routers', 'mdi-server-network', 'bg-info-focus', 'text-info-main'],
    'device_list'            => ['device_list', 'OLT Devices', 'OLT device status', 'mdi-access-point-network', 'bg-warning-focus', 'text-warning-main'],
    'sms'                    => ['sms', 'SMS', 'Send messages to customers', 'mdi-message-text-outline', 'bg-lilac-100', 'text-lilac-600'],
    'view_expence'           => ['view_expence', 'Expense', 'Record daily expenses', 'mdi-currency-usd-off', 'bg-danger-focus', 'text-danger-main'],
    'stock'                  => ['stock', 'Stock', 'Inventory stock overview', 'mdi-package-variant-closed', 'bg-success-focus', 'text-success-main'],
    'view_diagram'           => ['view_diagram', 'Network Diagram', 'View the network map', 'mdi-sitemap-outline', 'bg-primary-50', 'text-primary-600'],
];

$welcomeAllowed = [];
foreach ($welcomeLinks as $permission => $link) {
    if ($obj->userMenuePermission($permission)) {
        $welcomeAllowed[] = $link;
    }
}
?>
<style>
    .welcome-hero {
        position: relative;
        overflow: hidden;
        border: 0;
        border-radius: 16px;
        background: linear-gradient(110deg, #ECF9F1 0%, #D6F2E1 45%, #A7E2BE 100%);
    }

    /* soft background shapes */
    .welcome-hero::before,
    .welcome-hero::after {
        content: "";
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }

    .welcome-hero::before {
        width: 520px;
        height: 520px;
        top: -260px;
        left: 32%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.55) 0%, rgba(255, 255, 255, 0) 70%);
    }

    .welcome-hero::after {
        width: 460px;
        height: 460px;
        right: -120px;
        bottom: -300px;
        background: rgba(34, 197, 94, 0.18);
    }

    .welcome-hero .card-body {
        position: relative;
        z-index: 1;
    }

    .welcome-greeting {
        color: #374151;
        font-size: 16px;
        font-weight: 500;
    }

    .welcome-name {
        color: #111827;
        font-size: clamp(28px, 3vw, 40px);
        font-weight: 700;
        line-height: 1.15;
        word-break: break-word;
    }

    .welcome-subtitle {
        color: #374151;
        font-size: 15px;
    }

    .welcome-avatar {
        position: relative;
        width: 104px;
        height: 104px;
        flex-shrink: 0;
        border-radius: 50%;
        border: 4px solid #fff;
        background: var(--success-800);
        box-shadow: 0 10px 24px rgba(22, 101, 52, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 44px;
        font-weight: 600;
        color: #fff;
    }

    .welcome-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .welcome-avatar-status {
        position: absolute;
        right: 2px;
        bottom: 6px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 3px solid #fff;
        background: var(--success-500);
    }

    .welcome-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 10px;
        border: 1px solid rgba(22, 163, 74, 0.15);
        background: rgba(255, 255, 255, 0.85);
        color: #1F2937;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(22, 101, 52, 0.06);
    }

    .welcome-chip i {
        color: var(--success-600);
        font-size: 18px;
    }

    .welcome-illustration {
        width: 100%;
        max-width: 420px;
        height: auto;
    }

    /* dark theme */
    html[data-theme=dark] .welcome-hero {
        background: linear-gradient(110deg, #12241A 0%, #14402A 50%, #166534 100%);
    }

    html[data-theme=dark] .welcome-name {
        color: #fff;
    }

    html[data-theme=dark] .welcome-greeting,
    html[data-theme=dark] .welcome-subtitle {
        color: #D1D5DB;
    }

    html[data-theme=dark] .welcome-chip {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.12);
        color: #F3F4F6;
    }

    html[data-theme=dark] .welcome-chip i {
        color: var(--success-400);
    }

    /* quick access tiles */
    .welcome-tile {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 16px;
        height: 100%;
        padding: 20px;
        border-radius: 12px;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .welcome-tile:hover {
        transform: translateY(-4px);
        border-color: var(--success-300) !important;
        box-shadow: 0 12px 24px rgba(22, 163, 74, 0.12);
    }

    .welcome-tile-icon {
        width: 52px;
        height: 52px;
        flex-shrink: 0;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
    }

    .welcome-tile-arrow {
        margin-left: auto;
        font-size: 20px;
        opacity: 0.4;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .welcome-tile:hover .welcome-tile-arrow {
        opacity: 1;
        transform: translateX(4px);
    }

    @media (max-width: 575px) {
        .welcome-avatar {
            width: 80px;
            height: 80px;
            font-size: 34px;
        }
    }
</style>

<div class="col-12">
    <div class="card welcome-hero">
        <div class="card-body p-24 px-lg-5 py-lg-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-7 col-xl-8">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-4">
                        <div class="welcome-avatar">
                            <?php if (!empty($welcomeImage)) { ?>
                                <img src="<?= htmlspecialchars($welcomeImage) ?>" alt="<?= htmlspecialchars($welcomeName) ?>" onerror="this.replaceWith(document.createTextNode('<?= htmlspecialchars($welcomeInitial, ENT_QUOTES) ?>'));">
                            <?php } else { ?>
                                <?= htmlspecialchars($welcomeInitial) ?>
                            <?php } ?>
                            <span class="welcome-avatar-status" title="Online"></span>
                        </div>
                        <div class="flex-grow-1">
                            <p class="welcome-greeting mb-1" id="welcomeGreeting"><?= $welcomeGreeting ?> 👋</p>
                            <h2 class="welcome-name mb-2"><?= htmlspecialchars($welcomeName) ?></h2>
                            <p class="welcome-subtitle mb-3">Welcome back! Pick up where you left off using the shortcuts below or the menu on the left.</p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="welcome-chip"><i class="mdi mdi-calendar-month-outline"></i> <?= date('l, d F Y') ?></span>
                                <span class="welcome-chip"><i class="mdi mdi-clock-outline"></i> <span id="welcomeClock"><?= date('h:i:s A') ?></span></span>
                                <span class="welcome-chip"><i class="mdi mdi-shield-account-outline"></i> Entry Operator</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-xl-4 d-none d-lg-flex justify-content-end">
                    <svg class="welcome-illustration" viewBox="0 0 420 220" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <defs>
                            <linearGradient id="welcomeChartFill" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0" stop-color="#22C55E" stop-opacity="0.3" />
                                <stop offset="1" stop-color="#22C55E" stop-opacity="0" />
                            </linearGradient>
                            <filter id="welcomeShadow" x="-20%" y="-20%" width="140%" height="140%">
                                <feDropShadow dx="0" dy="8" stdDeviation="8" flood-color="#166534" flood-opacity="0.15" />
                            </filter>
                        </defs>

                        <!-- ground -->
                        <ellipse cx="270" cy="232" rx="170" ry="34" fill="#fff" fill-opacity="0.45" />

                        <!-- dotted grid -->
                        <g fill="#16A34A" fill-opacity="0.25">
                            <?php for ($gy = 0; $gy < 4; $gy++) for ($gx = 0; $gx < 5; $gx++) { ?>
                                <circle cx="<?= 20 + $gx * 12 ?>" cy="<?= 30 + $gy * 12 ?>" r="2" />
                            <?php } ?>
                        </g>

                        <!-- left leaves -->
                        <g fill="#2E9D57">
                            <path d="M84 214 C62 186 60 152 78 128 C92 154 96 186 84 214Z" />
                            <path d="M84 214 C70 196 50 186 30 186 C40 206 60 216 84 214Z" fill="#45B369" />
                            <path d="M86 214 C96 192 114 178 132 176 C126 198 108 212 86 214Z" fill="#1F8A47" />
                        </g>

                        <!-- dashboard window -->
                        <g filter="url(#welcomeShadow)">
                            <rect x="104" y="34" width="200" height="156" rx="12" fill="#fff" stroke="#CDEFD9" stroke-width="2" />
                        </g>
                        <circle cx="118" cy="48" r="3" fill="#86EFAC" />
                        <circle cx="128" cy="48" r="3" fill="#86EFAC" />
                        <circle cx="138" cy="48" r="3" fill="#86EFAC" />
                        <?php if (!empty($welcomeLogo)) { ?>
                            <image href="<?= htmlspecialchars($welcomeLogo) ?>" x="116" y="60" width="38" height="16" preserveAspectRatio="xMinYMid meet" />
                        <?php } else { ?>
                            <rect x="116" y="62" width="34" height="12" rx="3" fill="#16A34A" />
                        <?php } ?>
                        <rect x="164" y="62" width="112" height="6" rx="3" fill="#E5F6EC" />
                        <rect x="164" y="74" width="72" height="5" rx="2.5" fill="#E5F6EC" />
                        <rect x="116" y="92" width="44" height="5" rx="2.5" fill="#E5F6EC" />
                        <rect x="116" y="103" width="34" height="5" rx="2.5" fill="#E5F6EC" />
                        <rect x="116" y="114" width="40" height="5" rx="2.5" fill="#E5F6EC" />

                        <!-- chart -->
                        <polygon points="118,172 142,150 164,160 188,128 212,140 236,112 258,120 288,92 288,180 118,180" fill="url(#welcomeChartFill)" />
                        <polyline points="118,172 142,150 164,160 188,128 212,140 236,112 258,120 288,92" stroke="#16A34A" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        <g fill="#16A34A" stroke="#fff" stroke-width="2">
                            <circle cx="142" cy="150" r="4" />
                            <circle cx="164" cy="160" r="4" />
                            <circle cx="188" cy="128" r="4" />
                            <circle cx="212" cy="140" r="4" />
                            <circle cx="236" cy="112" r="4" />
                            <circle cx="258" cy="120" r="4" />
                            <circle cx="288" cy="92" r="4" />
                        </g>

                        <!-- wifi -->
                        <g stroke="#fff" stroke-width="5" stroke-linecap="round" fill="none">
                            <path d="M324 62 Q352 38 380 62" />
                            <path d="M334 74 Q352 60 370 74" />
                        </g>
                        <circle cx="352" cy="86" r="4.5" fill="#fff" />

                        <!-- server stack -->
                        <g filter="url(#welcomeShadow)">
                            <?php foreach ([112, 142, 172] as $sy) { ?>
                                <rect x="314" y="<?= $sy ?>" width="80" height="26" rx="6" fill="#22C55E" stroke="#15803D" stroke-width="2" />
                                <rect x="324" y="<?= $sy + 9 ?>" width="36" height="8" rx="4" fill="#fff" fill-opacity="0.9" />
                                <circle cx="380" cy="<?= $sy + 13 ?>" r="3" fill="#fff" />
                            <?php } ?>
                        </g>

                        <!-- right leaves -->
                        <g>
                            <path d="M402 214 C392 190 394 164 410 146 C420 170 418 194 402 214Z" fill="#2E9D57" />
                            <path d="M400 214 C390 200 374 194 358 196 C368 210 382 216 400 214Z" fill="#45B369" />
                        </g>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-12">
    <div class="d-flex align-items-center justify-content-between mb-16">
        <h6 class="mb-0 fw-semibold">Quick Access</h6>
        <?php if ($welcomeAllowed) { ?>
            <span class="text-secondary-light text-sm"><?= count($welcomeAllowed) ?> shortcut<?= count($welcomeAllowed) > 1 ? 's' : '' ?></span>
        <?php } ?>
    </div>

    <?php if ($welcomeAllowed) { ?>
        <div class="row g-3">
            <?php foreach ($welcomeAllowed as [$linkPage, $linkTitle, $linkDesc, $linkIcon, $linkBg, $linkText]) { ?>
                <div class="col-xxl-3 col-lg-4 col-sm-6">
                    <a href="?page=<?= $linkPage ?>" class="card welcome-tile border">
                        <span class="welcome-tile-icon <?= $linkBg ?> <?= $linkText ?>">
                            <i class="mdi <?= $linkIcon ?>"></i>
                        </span>
                        <span class="d-block">
                            <span class="d-block fw-semibold text-primary-light"><?= $linkTitle ?></span>
                            <span class="d-block text-sm text-secondary-light"><?= $linkDesc ?></span>
                        </span>
                        <i class="mdi mdi-arrow-right welcome-tile-arrow text-success-600"></i>
                    </a>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="card border radius-12">
            <div class="card-body text-center py-5">
                <span class="welcome-tile-icon bg-success-focus text-success-main mx-auto mb-3">
                    <i class="mdi mdi-lock-outline"></i>
                </span>
                <h6 class="mb-2">No pages assigned yet</h6>
                <p class="text-secondary-light mb-0">Please contact your administrator to get access to the pages you need.</p>
            </div>
        </div>
    <?php } ?>
</div>

<div class="col-12">
    <div class="d-flex align-items-center gap-2 px-16 py-12 radius-8 bg-success-focus text-success-main text-sm">
        <i class="mdi mdi-information-outline text-lg"></i>
        <span>Need access to another page? Ask your administrator to update your menu permissions.</span>
    </div>
</div>

<?php $obj->start_script(); ?>
<script>
    (function() {
        const greetingEl = document.getElementById('welcomeGreeting');
        const clockEl = document.getElementById('welcomeClock');

        function tick() {
            const now = new Date();
            const hour = now.getHours();
            const greeting = hour < 12 ? 'Good Morning' : (hour < 17 ? 'Good Afternoon' : 'Good Evening');
            greetingEl.textContent = greeting + ' 👋';
            clockEl.textContent = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }

        tick();
        setInterval(tick, 1000);
    })();
</script>
<?php $obj->end_script(); ?>
