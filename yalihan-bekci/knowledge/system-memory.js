/**
 * System Memory - Son İşlemleri ve Sistem Yapısını Takip Eder
 * Her değişikliği kaydeder, sistem yapısını öğrenir
 */

const fs = require('fs');
const path = require('path');

class SystemMemory {
    constructor() {
        this.memoryPath = path.join(__dirname, 'system-memory.json');
        this.memory = this.loadMemory();
    }

    loadMemory() {
        if (fs.existsSync(this.memoryPath)) {
            return JSON.parse(fs.readFileSync(this.memoryPath, 'utf8'));
        }

        return {
            lastOperations: [],
            systemStructure: {},
            learnings: [],
            lastUpdate: null,
        };
    }

    saveMemory() {
        fs.writeFileSync(this.memoryPath, JSON.stringify(this.memory, null, 2));
    }

    /**
     * Son işlemi kaydet
     */
    recordOperation(operation) {
        this.memory.lastOperations.unshift({
            type: operation.type,
            description: operation.description,
            files: operation.files || [],
            result: operation.result,
            timestamp: new Date().toISOString(),
        });

        // Son 50 işlemi sakla
        if (this.memory.lastOperations.length > 50) {
            this.memory.lastOperations = this.memory.lastOperations.slice(0, 50);
        }

        this.memory.lastUpdate = new Date().toISOString();
        this.saveMemory();
    }

    /**
     * Sistem yapısını öğren
     */
    learnSystemStructure() {
        const projectRoot = process.env.PROJECT_ROOT || process.cwd();

        this.memory.systemStructure = {
            models: this.scanDirectory(path.join(projectRoot, 'app/Models')),
            controllers: this.scanDirectory(path.join(projectRoot, 'app/Http/Controllers')),
            migrations: this.scanDirectory(path.join(projectRoot, 'database/migrations')),
            views: this.scanDirectory(path.join(projectRoot, 'resources/views')),
            routes: this.analyzeRoutes(projectRoot),
            lastScan: new Date().toISOString(),
        };

        this.saveMemory();

        return this.memory.systemStructure;
    }

    scanDirectory(dirPath) {
        if (!fs.existsSync(dirPath)) return { count: 0, files: [] };

        const files = this.getAllFiles(dirPath);
        return {
            count: files.length,
            files: files.map((f) => path.relative(dirPath, f)),
            lastModified: this.getMostRecentFile(files),
        };
    }

    getAllFiles(dirPath, arrayOfFiles = []) {
        const files = fs.readdirSync(dirPath);

        files.forEach((file) => {
            const fullPath = path.join(dirPath, file);
            if (fs.statSync(fullPath).isDirectory()) {
                arrayOfFiles = this.getAllFiles(fullPath, arrayOfFiles);
            } else {
                arrayOfFiles.push(fullPath);
            }
        });

        return arrayOfFiles;
    }

    getMostRecentFile(files) {
        if (files.length === 0) return null;

        const mostRecent = files.reduce((latest, file) => {
            const stat = fs.statSync(file);
            return !latest || stat.mtime > latest.mtime ? { file, mtime: stat.mtime } : latest;
        }, null);

        return mostRecent
            ? {
                  file: path.basename(mostRecent.file),
                  time: mostRecent.mtime,
              }
            : null;
    }

    analyzeRoutes(projectRoot) {
        const routeFiles = ['routes/web.php', 'routes/api.php', 'routes/admin.php'];
        const routes = { files: [], endpoints: 0 };

        routeFiles.forEach((routeFile) => {
            const fullPath = path.join(projectRoot, routeFile);
            if (fs.existsSync(fullPath)) {
                const content = fs.readFileSync(fullPath, 'utf8');
                const routeCount = (content.match(/Route::/g) || []).length;
                routes.files.push({ file: routeFile, routes: routeCount });
                routes.endpoints += routeCount;
            }
        });

        return routes;
    }

    /**
     * Yeni öğrenme kaydet
     */
    recordLearning(learning) {
        this.memory.learnings.unshift({
            topic: learning.topic,
            content: learning.content,
            source: learning.source,
            confidence: learning.confidence || 'high',
            timestamp: new Date().toISOString(),
        });

        // Son 100 öğrenmeyi sakla
        if (this.memory.learnings.length > 100) {
            this.memory.learnings = this.memory.learnings.slice(0, 100);
        }

        this.saveMemory();
    }

    /**
     * Son işlemi getir
     */
    getLastOperation() {
        return this.memory.lastOperations[0] || null;
    }

    /**
     * Sistem durumunu al
     */
    getSystemStatus() {
        return {
            lastOperation: this.getLastOperation(),
            systemStructure: this.memory.systemStructure,
            recentLearnings: this.memory.learnings.slice(0, 10),
            totalOperations: this.memory.lastOperations.length,
            totalLearnings: this.memory.learnings.length,
            lastUpdate: this.memory.lastUpdate,
        };
    }

    /**
     * Otomatik öğrenme başlat
     */
    async autoLearn() {
        console.log('🧠 Sistem yapısı öğreniliyor...');

        // Sistem yapısını öğren
        this.learnSystemStructure();

        // Context7 dökümanlarından öğren
        this.learnFromDocs();

        // Her 1 saatte bir yeniden öğren
        setInterval(
            () => {
                console.log('🔄 Sistem yapısı güncelleniyor...');
                this.learnSystemStructure();
                this.learnFromDocs();
            },
            60 * 60 * 1000
        );
    }

    learnFromDocs() {
        const projectRoot = process.env.PROJECT_ROOT || process.cwd();
        const docPaths = ['docs/ai-training', 'docs/context7', 'docs/reports', 'README.md'];

        docPaths.forEach((docPath) => {
            const fullPath = path.join(projectRoot, docPath);
            if (fs.existsSync(fullPath)) {
                if (fs.statSync(fullPath).isDirectory()) {
                    this.learnFromDirectory(fullPath);
                } else {
                    this.learnFromFile(fullPath);
                }
            }
        });
    }

    learnFromDirectory(dirPath) {
        const files = fs.readdirSync(dirPath);
        files.forEach((file) => {
            if (file.endsWith('.md')) {
                this.learnFromFile(path.join(dirPath, file));
            }
        });
    }

    learnFromFile(filePath) {
        const content = fs.readFileSync(filePath, 'utf8');
        const fileName = path.basename(filePath);

        // Context7 öğrenmeleri
        if (content.includes('Context7') || content.includes('CONTEXT7')) {
            this.recordLearning({
                topic: 'Context7',
                content: `${fileName} içinde Context7 bilgisi var`,
                source: fileName,
                confidence: 'high',
            });
        }

        // Yeni özellikler
        if (content.includes('YENİ') || content.includes('NEW') || content.includes('v3.')) {
            const matches = content.match(/##\s+(.+?)\n/g);
            if (matches) {
                matches.forEach((match) => {
                    if (match.includes('YENİ') || match.includes('NEW')) {
                        this.recordLearning({
                            topic: 'Yeni Özellik',
                            content: match.replace(/##\s+/, '').trim(),
                            source: fileName,
                            confidence: 'medium',
                        });
                    }
                });
            }
        }
    }
}

module.exports = new SystemMemory();
