<!-- Footer Start -->
<footer class="mt-auto footer flex items-center py-5 border-t border-default-200">
    <div class="lg:px-8 px-6 w-full flex md:justify-between justify-center gap-4">
        <div>
            {{ $siteConfig['apariencia.pie_pagina'] ?? '© ' . date('Y') . ' UGEL HUACAYBAMBA - SATA-QR v1.0.0' }}
        </div>
        <div class="md:flex hidden gap-5 item-center md:justify-end">
            @if (!empty($siteConfig['sistema.email_soporte']))
                <a class="text-default-500 hover:text-primary transition-all text-xs"
                    href="mailto:{{ $siteConfig['sistema.email_soporte'] }}">Soporte</a>
            @endif
            <a class="text-default-500 hover:text-primary transition-all text-xs" href="#">Privacidad</a>
            <a class="text-default-500 hover:text-primary transition-all text-xs" href="#">Términos</a>
        </div>
    </div>
</footer>
<!-- Footer End -->
