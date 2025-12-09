// Advanced Obfuscation Plugin for Vite
import JavaScriptObfuscator from 'javascript-obfuscator';

export function obfuscatorPlugin(options = {}) {
    const defaultOptions = {
        compact: true,
        controlFlowFlattening: true,
        controlFlowFlatteningThreshold: 0.75,
        deadCodeInjection: true,
        deadCodeInjectionThreshold: 0.4,
        debugProtection: true,
        debugProtectionInterval: 4000,
        disableConsoleOutput: true,
        identifierNamesGenerator: 'hexadecimal',
        log: false,
        numbersToExpressions: true,
        renameGlobals: false,
        selfDefending: true,
        simplify: true,
        splitStrings: true,
        splitStringsChunkLength: 10,
        stringArray: true,
        stringArrayCallsTransform: true,
        stringArrayEncoding: ['base64'],
        stringArrayIndexShift: true,
        stringArrayRotate: true,
        stringArrayShuffle: true,
        stringArrayWrappersCount: 2,
        stringArrayWrappersChainedCalls: true,
        stringArrayWrappersParametersMaxCount: 4,
        stringArrayWrappersType: 'function',
        stringArrayThreshold: 0.75,
        transformObjectKeys: true,
        unicodeEscapeSequence: false
    };

    const mergedOptions = { ...defaultOptions, ...options };

    return {
        name: 'obfuscator',
        apply: 'build',
        enforce: 'post',
        generateBundle(options, bundle) {
            // Only obfuscate in production
            if (process.env.NODE_ENV !== 'production') return;

            Object.keys(bundle).forEach(fileName => {
                const file = bundle[fileName];
                
                // Only process JavaScript files
                if (file.type === 'chunk' && fileName.endsWith('.js')) {
                    try {
                        console.log(`🔒 Obfuscating ${fileName}...`);
                        
                        // Apply obfuscation
                        const obfuscated = JavaScriptObfuscator.obfuscate(file.code, mergedOptions);
                        file.code = obfuscated.getObfuscatedCode();
                        
                        console.log(`✅ Successfully obfuscated ${fileName}`);
                    } catch (error) {
                        console.warn(`⚠️  Failed to obfuscate ${fileName}:`, error.message);
                    }
                }
            });
        }
    };
}

// Security headers plugin
export function securityHeadersPlugin() {
    return {
        name: 'security-headers',
        apply: 'build',
        generateBundle(options, bundle) {
            // Add security-related files
            this.emitFile({
                type: 'asset',
                fileName: '.htaccess',
                source: `# FOTOKU Security Headers
RewriteEngine On

# Prevent access to sensitive files
<Files ~ "\\.(env|log|md)$">
    Order allow,deny
    Deny from all
</Files>

# Security Headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' cdn.tailwindcss.com cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' cdn.tailwindcss.com cdnjs.cloudflare.com; img-src 'self' data: blob:; font-src 'self' cdnjs.cloudflare.com;"

# Hide server signature
ServerSignature Off
ServerTokens Prod`
            });
        }
    };
}
