<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class=" nav-item"><a href="{{ route('dashboard.chat.index') }}" @class(['active' => request()->routeIs('dashboard.chat.index')])><i class="la la-comments"></i><span class="menu-title"
                        data-i18n="">Chat Application</span></a>
            </li>
    </div>
</div>
