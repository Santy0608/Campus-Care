(function initCampusCareApi(window) {
    const DEFAULT_API_BASE_URL = 'http://localhost:8080';

    function normalizeBaseUrl(url) {
        return (url || DEFAULT_API_BASE_URL).replace(/\/$/, '');
    }

    function getApiBaseUrl() {
        const configured = window.CAMPUS_CARE_API_BASE_URL
            || sessionStorage.getItem('apiBaseUrl')
            || localStorage.getItem('apiBaseUrl');

        return normalizeBaseUrl(configured);
    }

    function getToken() {
        return sessionStorage.getItem('token') || '';
    }

    function parseJwt(token) {
        if (!token) {
            return {};
        }

        const parts = token.split('.');
        if (parts.length < 2) {
            return {};
        }

        try {
            const base64 = parts[1].replace(/-/g, '+').replace(/_/g, '/');
            const padded = base64.padEnd(base64.length + ((4 - (base64.length % 4)) % 4), '=');
            const payload = atob(padded);
            return JSON.parse(decodeURIComponent(Array.from(payload)
                .map((char) => `%${char.charCodeAt(0).toString(16).padStart(2, '0')}`)
                .join('')));
        } catch (error) {
            console.error('No se pudo decodificar el JWT:', error);
            return {};
        }
    }

    function getStoredUser() {
        const raw = sessionStorage.getItem('usuario');
        if (!raw) {
            return null;
        }

        try {
            return JSON.parse(raw);
        } catch {
            return null;
        }
    }

    function normalizeAuthorities(authorities) {
        if (Array.isArray(authorities)) {
            return authorities;
        }

        if (typeof authorities === 'string') {
            try {
                const parsed = JSON.parse(authorities);
                return Array.isArray(parsed) ? parsed : [];
            } catch {
                return [];
            }
        }

        return [];
    }

    function mapUserFromToken(token, fallbackUsername) {
        const payload = parseJwt(token);
        const username = payload.username || payload.sub || fallbackUsername || '';
        const authorities = normalizeAuthorities(payload.authorities);
        const isAdmin = Boolean(payload.isAdmin) || authorities.some((authority) => {
            if (typeof authority === 'string') {
                return authority === 'ROLE_ADMIN';
            }

            return authority && authority.authority === 'ROLE_ADMIN';
        });

        return {
            username,
            nombre: username,
            role: isAdmin ? 'admin' : 'estudiante',
            isAdmin,
            authorities,
        };
    }

    function storeAuthSession(authResponse) {
        if (!authResponse || !authResponse.token) {
            throw new Error('La respuesta de autenticacion no incluyo un token.');
        }

        const token = authResponse.token;
        const user = mapUserFromToken(token, authResponse.username);

        sessionStorage.setItem('token', token);
        sessionStorage.setItem('usuario', JSON.stringify(user));
        return user;
    }

    function clearSession() {
        sessionStorage.removeItem('token');
        sessionStorage.removeItem('usuario');
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value == null ? '' : String(value);
        return div.innerHTML;
    }

    async function readResponse(response) {
        if (response.status === 204) {
            return null;
        }

        const contentType = response.headers.get('content-type') || '';
        if (contentType.includes('application/json')) {
            return response.json();
        }

        return response.text();
    }

    async function request(path, options = {}) {
        const token = getToken();
        const headers = new Headers(options.headers || {});
        const hasBody = options.body !== undefined && !(options.body instanceof FormData);

        if (hasBody && !headers.has('Content-Type')) {
            headers.set('Content-Type', 'application/json');
        }

        if (token && !headers.has('Authorization')) {
            headers.set('Authorization', `Bearer ${token}`);
        }

        const response = await fetch(`${getApiBaseUrl()}${path}`, {
            ...options,
            headers,
            body: hasBody && typeof options.body !== 'string'
                ? JSON.stringify(options.body)
                : options.body,
        });

        const payload = await readResponse(response);
        if (!response.ok) {
            const message = payload && typeof payload === 'object'
                ? payload.message || payload.error || 'La solicitud fallo.'
                : 'La solicitud fallo.';

            const error = new Error(message);
            error.status = response.status;
            error.payload = payload;
            throw error;
        }

        return payload;
    }

    function requireRole(expectedRole) {
        const user = getStoredUser();

        if (!user) {
            window.location.href = '/includes/no_autorizado.html';
            return null;
        }

        if (expectedRole && user.role !== expectedRole) {
            window.location.href = '/includes/no_autorizado.html';
            return null;
        }

        return user;
    }

    window.CampusCareApi = {
        clearSession,
        escapeHtml,
        getApiBaseUrl,
        getStoredUser,
        getToken,
        mapUserFromToken,
        request,
        requireRole,
        storeAuthSession,
    };
})(window);