/**
 * API Key Authentication Middleware
 * Protects Node waserver and socket express endpoints.
 * Fail-closed: if env key not set, returns 503.
 */
const apiKeyAuth = (envVar) => (req, res, next) => {
    const expectedKey = process.env[envVar];

    if (!expectedKey) {
        console.error(`[apiKeyAuth] ${envVar} not set in environment — refusing all requests (fail-closed)`);
        return res.status(503).json({ status: 'error', message: 'Service unavailable: auth not configured' });
    }

    const providedKey = req.headers['x-api-key'];

    if (!providedKey || providedKey !== expectedKey) {
        return res.status(401).json({ status: 'error', message: 'Unauthorized' });
    }

    next();
};

export default apiKeyAuth;

