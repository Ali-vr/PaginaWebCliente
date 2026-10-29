(() => {
    const shell = document.getElementById('accountPanelShell');
    const panel = document.getElementById('panelMiCuenta');
    const triggers = [...document.querySelectorAll('.account-panel-trigger')];
    const status = document.getElementById('accountPanelStatus');
    const ordersSection = document.getElementById('accountOrdersSection');
    const ordersList = document.getElementById('accountOrdersList');
    const requestsList = document.getElementById('accountRequestsList');

    if (!shell || !panel || triggers.length === 0 || !status || !ordersSection || !ordersList || !requestsList) {
        return;
    }

    const nameElement = document.getElementById('accountUserName');
    const emailElement = document.getElementById('accountUserEmail');
    let active = false;
    let activeTrigger = triggers[0];

    const dateLabel = (value) => {
        const match = /^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2}))?/.exec(String(value ?? ''));
        if (!match) {
            return String(value ?? '');
        }

        const date = `${match[3]}/${match[2]}/${match[1]}`;
        return match[4] ? `${date} ${match[4]}:${match[5]}` : date;
    };

    const currencyLabel = (value) => {
        const amount = Number(value);
        return Number.isFinite(amount)
            ? new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(amount)
            : '';
    };

    const element = (tag, className, text) => {
        const node = document.createElement(tag);
        if (className) {
            node.className = className;
        }
        if (text !== undefined) {
            node.textContent = String(text);
        }
        return node;
    };

    const badge = (value, labels, classes) => {
        const rawValue = String(value ?? '');
        const knownClass = Object.prototype.hasOwnProperty.call(classes, rawValue) ? classes[rawValue] : '';
        const knownLabel = Object.prototype.hasOwnProperty.call(labels, rawValue) ? labels[rawValue] : rawValue;
        const node = element('span', `account-panel-badge${knownClass ? ` ${knownClass}` : ''}`, knownLabel);
        return node;
    };

    const showEmpty = (container, text) => {
        container.append(element('p', 'account-panel-card-copy text-muted', text));
    };

    const renderOrders = (orders) => {
        ordersList.replaceChildren();
        if (!Array.isArray(orders) || orders.length === 0) {
            showEmpty(ordersList, 'Todavía no realizaste ningún pedido.');
            return;
        }

        const labels = {
            pendiente: 'Pendiente',
            confirmado: 'Confirmado',
            'en preparacion': 'En preparación',
            enviado: 'Enviado',
            entregado: 'Entregado',
            cancelado: 'Cancelado',
        };
        const classes = {
            pendiente: 'account-panel-badge--pending',
            entregado: 'account-panel-badge--delivered',
            cancelado: 'account-panel-badge--cancelled',
        };

        orders.forEach((order) => {
            const card = element('article', 'account-panel-card');
            const heading = element('div', 'account-panel-card-heading');
            heading.append(element('strong', '', `Pedido #${order.numero ?? ''}`));
            heading.append(badge(order.estado, labels, classes));
            card.append(heading);
            card.append(element('p', 'account-panel-meta', `Fecha: ${dateLabel(order.created_at)}`));
            card.append(element('p', 'account-panel-card-copy', `Total: ${currencyLabel(order.total)}`));

            const products = String(order.productos_resumen ?? '').trim();
            if (products) {
                card.append(element('p', 'account-panel-card-copy', `Productos: ${products}`));
            } else if (Number(order.items_count) > 0) {
                card.append(element('p', 'account-panel-card-copy', `${Number(order.items_count)} artículos`));
            }

            ordersList.append(card);
        });
    };

    const renderRequests = (requests) => {
        requestsList.replaceChildren();
        if (!Array.isArray(requests) || requests.length === 0) {
            showEmpty(requestsList, 'Todavía no tenés solicitudes de productos personalizados.');
            return;
        }

        const labels = {
            pendiente: 'Pendiente',
            aceptada: 'Aprobada',
            rechazada: 'Rechazada',
        };
        const classes = {
            pendiente: 'account-panel-badge--pending',
            aceptada: 'account-panel-badge--accepted',
            rechazada: 'account-panel-badge--rejected',
        };

        requests.forEach((request) => {
            const card = element('article', 'account-panel-card');
            const heading = element('div', 'account-panel-card-heading');
            heading.append(element('strong', '', `Solicitud #${request.id}`));
            heading.append(badge('Personalizado', {}, { 'Personalizado': '' }));
            heading.append(badge(request.estado, labels, classes));
            card.append(heading);
            card.append(element('p', 'account-panel-meta', `Fecha: ${dateLabel(request.created_at)}`));
            card.append(element('p', 'account-panel-card-copy', request.descripcion ?? ''));

            const requestId = Number(request.id);
            if (Number.isSafeInteger(requestId) && requestId > 0) {
                const fileLink = element('a', 'account-panel-file', `Descargar archivo: ${request.archivo_nombre ?? 'archivo adjunto'}`);
                fileLink.href = `/mi-cuenta/personalizaciones/${requestId}/archivo`;
                card.append(fileLink);
            }

            if (request.estado === 'aceptada' && request.presupuesto !== null && request.presupuesto !== undefined) {
                card.append(element('p', 'account-panel-card-copy', `Presupuesto estimado: ${currencyLabel(request.presupuesto)}`));
            }
            if (request.estado !== 'pendiente' && String(request.respuesta ?? '').trim() !== '') {
                card.append(element('p', 'account-panel-card-copy', `Respuesta: ${request.respuesta}`));
            }

            requestsList.append(card);
        });
    };

    const loadAccountData = async () => {
        status.textContent = 'Cargando tus datos…';
        ordersSection.hidden = true;
        ordersList.replaceChildren();
        requestsList.replaceChildren();

        try {
            const response = await fetch('/mi-cuenta/datos', {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
                cache: 'no-store',
            });

            if (response.redirected) {
                window.location.assign(response.url);
                return;
            }

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.error || 'No se pudieron cargar los datos de tu cuenta.');
            }

            if (nameElement) {
                nameElement.textContent = data.usuario?.nombre ?? '';
            }
            if (emailElement) {
                emailElement.textContent = data.usuario?.email ?? '';
            }

            renderOrders(data.pedidos);
            renderRequests(data.solicitudes);
            ordersSection.hidden = false;
            status.textContent = '';
        } catch (error) {
            status.textContent = error instanceof Error
                ? error.message
                : 'No se pudieron cargar los datos de tu cuenta. Intentá de nuevo más tarde.';
        }
    };

    const openPanel = (trigger = triggers[0]) => {
        if (active) {
            return;
        }
        active = true;
        activeTrigger = trigger;
        shell.inert = false;
        shell.setAttribute('aria-hidden', 'false');
        shell.classList.add('is-open');
        activeTrigger.setAttribute('aria-expanded', 'true');
        document.body.classList.add('account-panel-open');
        panel.querySelector('.account-panel-close')?.focus();
        loadAccountData();
    };

    const closePanel = () => {
        if (!active) {
            return;
        }
        active = false;
        shell.classList.remove('is-open');
        shell.setAttribute('aria-hidden', 'true');
        shell.inert = true;
        activeTrigger.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('account-panel-open');
        activeTrigger.focus();
        if (window.location.hash === '#mi-cuenta') {
            window.history.replaceState(null, '', `${window.location.pathname}${window.location.search}`);
        }
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', () => {
            if (active) {
                closePanel();
            } else {
                openPanel(trigger);
            }
        });
    });

    shell.querySelectorAll('[data-account-close]').forEach((button) => {
        button.addEventListener('click', closePanel);
    });

    document.addEventListener('keydown', (event) => {
        if (!active) {
            return;
        }
        if (event.key === 'Escape') {
            closePanel();
            return;
        }
        if (event.key !== 'Tab') {
            return;
        }

        const focusable = [...panel.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])')]
            .filter((node) => !node.hidden);
        if (focusable.length === 0) {
            event.preventDefault();
            panel.focus();
            return;
        }

        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    if (window.location.hash === '#mi-cuenta') {
        openPanel();
    }
})();
