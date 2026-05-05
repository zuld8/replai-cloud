/**
 * Simple i18n plugin for Vue 3
 * Gets translations from window.i18n (passed from Laravel)
 */

export default {
    install: (app) => {
        // Get translations from window (set by Laravel)
        const locale = window.i18n?.locale || 'id';
        const messages = window.i18n?.translations || {};

        /**
         * Translation function
         * Usage: $t('sidebar.welcome') or $t('chat.send')
         */
        app.config.globalProperties.$t = (key, replacements = {}) => {
            // Split the key by dots to access nested objects
            const keys = key.split('.');
            let translation = messages;

            // Navigate through the nested object
            for (const k of keys) {
                if (translation && typeof translation === 'object' && k in translation) {
                    translation = translation[k];
                } else {
                    // Return the key if translation not found
                    return key;
                }
            }

            // Handle replacements like :name, :count, etc.
            if (typeof translation === 'string' && Object.keys(replacements).length > 0) {
                Object.keys(replacements).forEach(placeholder => {
                    translation = translation.replace(
                        new RegExp(`:${placeholder}`, 'g'),
                        replacements[placeholder]
                    );
                });
            }

            return translation;
        };

        /**
         * Get current locale
         */
        app.config.globalProperties.$locale = () => {
            return locale;
        };

        /**
         * Check if translation exists
         */
        app.config.globalProperties.$hasTranslation = (key) => {
            const keys = key.split('.');
            let translation = messages;

            for (const k of keys) {
                if (translation && typeof translation === 'object' && k in translation) {
                    translation = translation[k];
                } else {
                    return false;
                }
            }

            return true;
        };
    }
};