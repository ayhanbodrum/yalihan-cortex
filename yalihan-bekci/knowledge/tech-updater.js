/**
 * Technology Updater
 * Kullanılan teknolojilerin son versiyonlarını kontrol eder
 */

const fs = require('fs');
const path = require('path');
const https = require('https');

class TechUpdater {
    constructor() {
        this.projectRoot = process.env.PROJECT_ROOT || process.cwd();
        this.technologies = {};
        this.updates = [];
    }

    /**
     * Proje teknolojilerini tespit et
     */
    async detectTechnologies() {
        console.log('🔍 Teknolojiler tespit ediliyor...');

        // PHP/Laravel
        await this.checkComposer();

        // JavaScript/Node
        await this.checkPackageJson();

        // Diğer
        this.checkOtherTech();

        return this.technologies;
    }

    /**
     * composer.json okuyup Laravel/PHP paketlerini tespit et
     */
    async checkComposer() {
        const composerPath = path.join(this.projectRoot, 'composer.json');

        if (fs.existsSync(composerPath)) {
            const composer = JSON.parse(fs.readFileSync(composerPath, 'utf8'));

            this.technologies.php = {
                version: composer.require?.php || 'Unknown',
                type: 'runtime',
            };

            this.technologies.laravel = {
                version: composer.require?.['laravel/framework'] || 'Unknown',
                type: 'framework',
                current: this.extractVersion(composer.require?.['laravel/framework']),
            };

            // Önemli paketler
            const importantPackages = [
                'spatie/laravel-permission',
                'laravel/sanctum',
                'barryvdh/laravel-debugbar',
                'laravel/telescope',
            ];

            importantPackages.forEach((pkg) => {
                if (composer.require?.[pkg]) {
                    this.technologies[pkg] = {
                        version: composer.require[pkg],
                        current: this.extractVersion(composer.require[pkg]),
                        type: 'package',
                    };
                }
            });
        }
    }

    /**
     * package.json okuyup Node paketlerini tespit et
     */
    async checkPackageJson() {
        const packagePath = path.join(this.projectRoot, 'package.json');

        if (fs.existsSync(packagePath)) {
            const pkg = JSON.parse(fs.readFileSync(packagePath, 'utf8'));

            // Framework'ler
            const frameworks = {
                vite: 'Build Tool',
                tailwindcss: 'CSS Framework',
                alpinejs: 'JavaScript Framework',
                '@vitejs/plugin-vue': 'Vue Plugin',
            };

            Object.entries(frameworks).forEach(([name, desc]) => {
                const version = pkg.dependencies?.[name] || pkg.devDependencies?.[name];
                if (version) {
                    this.technologies[name] = {
                        version: version,
                        current: this.extractVersion(version),
                        type: desc,
                    };
                }
            });
        }
    }

    /**
     * Diğer teknolojileri tespit et
     */
    checkOtherTech() {
        // Tailwind config
        if (fs.existsSync(path.join(this.projectRoot, 'tailwind.config.js'))) {
            this.technologies.tailwind = {
                version: 'Configured',
                type: 'CSS Framework',
            };
        }

        // Vite config
        if (fs.existsSync(path.join(this.projectRoot, 'vite.config.js'))) {
            this.technologies.vite = {
                version: 'Configured',
                type: 'Build Tool',
            };
        }
    }

    /**
     * Versiyonu parse et
     */
    extractVersion(versionString) {
        if (!versionString) return null;
        const match = versionString.match(/(\d+\.\d+\.?\d*)/);
        return match ? match[1] : versionString;
    }

    /**
     * NPM'den son versiyon bilgisi al
     */
    async getLatestNpmVersion(packageName) {
        return new Promise((resolve) => {
            https
                .get(`https://registry.npmjs.org/${packageName}/latest`, (res) => {
                    let data = '';
                    res.on('data', (chunk) => (data += chunk));
                    res.on('end', () => {
                        try {
                            const info = JSON.parse(data);
                            resolve(info.version);
                        } catch (e) {
                            resolve(null);
                        }
                    });
                })
                .on('error', () => resolve(null));
        });
    }

    /**
     * Packagist'ten Laravel paket versiyonu al
     */
    async getLatestComposerVersion(packageName) {
        return new Promise((resolve) => {
            https
                .get(`https://repo.packagist.org/p2/${packageName}.json`, (res) => {
                    let data = '';
                    res.on('data', (chunk) => (data += chunk));
                    res.on('end', () => {
                        try {
                            const info = JSON.parse(data);
                            const versions = Object.keys(info.packages?.[packageName] || {});
                            const latest = versions
                                .filter((v) => !v.includes('dev') && !v.includes('alpha'))
                                .sort()
                                .pop();
                            resolve(latest);
                        } catch (e) {
                            resolve(null);
                        }
                    });
                })
                .on('error', () => resolve(null));
        });
    }

    /**
     * Tüm teknolojileri kontrol et ve güncellemeleri bul
     */
    async checkForUpdates() {
        console.log('🔄 Güncelleme kontrol ediliyor...');

        await this.detectTechnologies();
        this.updates = [];

        // Laravel versiyonu
        if (this.technologies.laravel?.current) {
            const latest = await this.getLatestComposerVersion('laravel/framework');
            if (latest && latest !== this.technologies.laravel.current) {
                this.updates.push({
                    name: 'Laravel',
                    current: this.technologies.laravel.current,
                    latest: latest,
                    type: 'major',
                    priority: 'high',
                });
            }
        }

        // Tailwind
        if (this.technologies.tailwindcss?.current) {
            const latest = await this.getLatestNpmVersion('tailwindcss');
            if (latest && latest !== this.technologies.tailwindcss.current) {
                this.updates.push({
                    name: 'Tailwind CSS',
                    current: this.technologies.tailwindcss.current,
                    latest: latest,
                    type: 'minor',
                    priority: 'medium',
                });
            }
        }

        console.log(`✅ ${this.updates.length} güncelleme bulundu`);

        // Güncellemeleri kaydet
        this.saveUpdates();

        return this.updates;
    }

    /**
     * Güncellemeleri kaydet
     */
    saveUpdates() {
        const updatePath = path.join(__dirname, 'tech-updates.json');
        fs.writeFileSync(
            updatePath,
            JSON.stringify(
                {
                    technologies: this.technologies,
                    updates: this.updates,
                    lastCheck: new Date().toISOString(),
                },
                null,
                2
            )
        );
    }

    /**
     * Otomatik güncelleme kontrolü başlat
     */
    async autoCheck() {
        console.log('🔄 Otomatik teknoloji güncelleme kontrolü aktif');

        // İlk kontrol
        await this.checkForUpdates();

        // Her 24 saatte bir kontrol et
        setInterval(
            async () => {
                console.log('🔄 Günlük teknoloji güncelleme kontrolü...');
                await this.checkForUpdates();
            },
            24 * 60 * 60 * 1000
        );
    }

    /**
     * Güncelleme raporu oluştur
     */
    generateReport() {
        return {
            technologies: Object.keys(this.technologies).length,
            updates_available: this.updates.length,
            updates: this.updates.map((u) => ({
                name: u.name,
                current: u.current,
                latest: u.latest,
                upgrade_command: this.getUpgradeCommand(u),
            })),
        };
    }

    /**
     * Güncelleme komutu üret
     */
    getUpgradeCommand(update) {
        const name = update.name.toLowerCase();

        if (name.includes('laravel')) {
            return `composer require laravel/framework:^${update.latest}`;
        }

        if (name.includes('tailwind')) {
            return `npm install tailwindcss@${update.latest}`;
        }

        return `# Manuel güncelleme gerekli`;
    }
}

module.exports = new TechUpdater();
