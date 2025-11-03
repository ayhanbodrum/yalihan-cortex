#!/usr/bin/env node

/**
 * Laravel MCP Server - EmlakPro Custom
 * Laravel Artisan komutları ve database erişimi
 */

const { Server } = require("@modelcontextprotocol/sdk/server/index.js");
const {
    StdioServerTransport,
} = require("@modelcontextprotocol/sdk/server/stdio.js");
const {
    CallToolRequestSchema,
    ListToolsRequestSchema,
} = require("@modelcontextprotocol/sdk/types.js");
const { exec } = require("child_process");
const fs = require("fs");
const path = require("path");

class LaravelMCP {
    constructor() {
        this.projectRoot =
            process.env.PROJECT_ROOT ||
            "/Users/macbookpro/Projects/yalihanemlakwarp";
        this.server = new Server(
            {
                name: "laravel-emlakpro",
                version: "1.0.0",
            },
            {
                capabilities: {
                    tools: {},
                },
            }
        );

        this.setupHandlers();
    }

    setupHandlers() {
        // List available tools
        this.server.setRequestHandler(ListToolsRequestSchema, async () => ({
            tools: [
                {
                    name: "artisan_command",
                    description: "Laravel Artisan komutlarını çalıştırır",
                    inputSchema: {
                        type: "object",
                        properties: {
                            command: {
                                type: "string",
                                description:
                                    "Artisan komutu (örn: migrate, make:model)",
                            },
                            args: {
                                type: "array",
                                items: { type: "string" },
                                description: "Komut argümanları",
                            },
                        },
                        required: ["command"],
                    },
                },
                {
                    name: "get_model_info",
                    description: "Laravel model bilgilerini getirir",
                    inputSchema: {
                        type: "object",
                        properties: {
                            model: {
                                type: "string",
                                description: "Model adı (örn: Ilan, Kisi)",
                            },
                        },
                        required: ["model"],
                    },
                },
                {
                    name: "run_migration",
                    description: "Database migration çalıştırır",
                    inputSchema: {
                        type: "object",
                        properties: {
                            fresh: {
                                type: "boolean",
                                description:
                                    "Fresh migration (tüm tabloları siler)",
                                default: false,
                            },
                        },
                    },
                },
                {
                    name: "context7_check",
                    description: "Context7 kurallarını kontrol eder",
                    inputSchema: {
                        type: "object",
                        properties: {
                            file: {
                                type: "string",
                                description: "Kontrol edilecek dosya yolu",
                            },
                        },
                    },
                },
                {
                    name: "mysql_query",
                    description:
                        "MySQL veritabanında sorgu çalıştırır (Eloquent üzerinden)",
                    inputSchema: {
                        type: "object",
                        properties: {
                            query: {
                                type: "string",
                                description: "SQL sorgusu veya Eloquent komutu",
                            },
                            model: {
                                type: "string",
                                description:
                                    "Model adı (örn: Ilan, Kisi, User)",
                            },
                            operation: {
                                type: "string",
                                enum: ["count", "get", "find", "raw"],
                                description: "İşlem tipi",
                            },
                        },
                        required: ["operation"],
                    },
                },
                {
                    name: "get_table_info",
                    description: "MySQL tablo bilgilerini getirir",
                    inputSchema: {
                        type: "object",
                        properties: {
                            table: {
                                type: "string",
                                description: "Tablo adı",
                            },
                        },
                        required: ["table"],
                    },
                },
            ],
        }));

        // Handle tool calls
        this.server.setRequestHandler(
            CallToolRequestSchema,
            async (request) => {
                const { name, arguments: args } = request.params;

                switch (name) {
                    case "artisan_command":
                        return await this.runArtisanCommand(args);
                    case "get_model_info":
                        return await this.getModelInfo(args);
                    case "run_migration":
                        return await this.runMigration(args);
                    case "context7_check":
                        return await this.checkContext7(args);
                    case "mysql_query":
                        return await this.runMySQLQuery(args);
                    case "get_table_info":
                        return await this.getTableInfo(args);
                    default:
                        throw new Error(`Unknown tool: ${name}`);
                }
            }
        );
    }

    async runArtisanCommand(args) {
        const command = args.command;
        const cmdArgs = args.args || [];
        const fullCommand = `cd ${
            this.projectRoot
        } && php artisan ${command} ${cmdArgs.join(" ")}`;

        return new Promise((resolve) => {
            exec(fullCommand, (error, stdout, stderr) => {
                resolve({
                    content: [
                        {
                            type: "text",
                            text: JSON.stringify(
                                {
                                    command: fullCommand,
                                    success: !error,
                                    output: stdout,
                                    error: stderr,
                                },
                                null,
                                2
                            ),
                        },
                    ],
                });
            });
        });
    }

    async getModelInfo(args) {
        const modelName = args.model;
        const modelPath = path.join(
            this.projectRoot,
            "app",
            "Models",
            `${modelName}.php`
        );

        if (!fs.existsSync(modelPath)) {
            return {
                content: [
                    {
                        type: "text",
                        text: JSON.stringify(
                            { error: `Model ${modelName} bulunamadı` },
                            null,
                            2
                        ),
                    },
                ],
            };
        }

        const content = fs.readFileSync(modelPath, "utf8");

        // Extract relationships, fillable, etc.
        const fillableMatch = content.match(
            /protected\s+\$fillable\s*=\s*\[(.*?)\]/s
        );
        const relationshipsMatch = content.match(
            /public\s+function\s+(\w+)\(\)/g
        );

        return {
            content: [
                {
                    type: "text",
                    text: JSON.stringify(
                        {
                            model: modelName,
                            path: modelPath,
                            fillable: fillableMatch ? fillableMatch[1] : null,
                            relationships: relationshipsMatch || [],
                            fileSize: fs.statSync(modelPath).size,
                        },
                        null,
                        2
                    ),
                },
            ],
        };
    }

    async runMigration(args) {
        const fresh = args.fresh ? "--fresh" : "";
        const command = `cd ${this.projectRoot} && php artisan migrate ${fresh}`;

        return new Promise((resolve) => {
            exec(command, (error, stdout, stderr) => {
                resolve({
                    content: [
                        {
                            type: "text",
                            text: JSON.stringify(
                                {
                                    command: command,
                                    success: !error,
                                    output: stdout,
                                    error: stderr,
                                },
                                null,
                                2
                            ),
                        },
                    ],
                });
            });
        });
    }

    async checkContext7(args) {
        const filePath = args.file || "";
        const command = `cd ${this.projectRoot} && php artisan context7:validate-migration ${filePath}`;

        return new Promise((resolve) => {
            exec(command, (error, stdout, stderr) => {
                resolve({
                    content: [
                        {
                            type: "text",
                            text: JSON.stringify(
                                {
                                    file: filePath,
                                    success: !error,
                                    violations: stdout,
                                    error: stderr,
                                },
                                null,
                                2
                            ),
                        },
                    ],
                });
            });
        });
    }

    async runMySQLQuery(args) {
        const { operation, model, query } = args;

        let command;
        switch (operation) {
            case "count":
                command = model
                    ? `cd ${this.projectRoot} && php artisan tinker --execute="echo App\\\\Models\\\\${model}::count();"`
                    : `cd ${this.projectRoot} && php artisan tinker --execute="echo DB::table('${query}')->count();"`;
                break;
            case "get":
                command = model
                    ? `cd ${this.projectRoot} && php artisan tinker --execute="App\\\\Models\\\\${model}::limit(10)->get()->toJson();"`
                    : `cd ${this.projectRoot} && php artisan tinker --execute="DB::table('${query}')->limit(10)->get()->toJson();"`;
                break;
            case "raw":
                command = `cd ${this.projectRoot} && php artisan tinker --execute="DB::select('${query}');"`;
                break;
            default:
                throw new Error(`Unknown operation: ${operation}`);
        }

        return new Promise((resolve) => {
            exec(command, (error, stdout, stderr) => {
                resolve({
                    content: [
                        {
                            type: "text",
                            text: JSON.stringify(
                                {
                                    operation: operation,
                                    model: model,
                                    query: query,
                                    success: !error,
                                    result: stdout,
                                    error: stderr,
                                },
                                null,
                                2
                            ),
                        },
                    ],
                });
            });
        });
    }

    async getTableInfo(args) {
        const table = args.table;
        const command = `cd ${this.projectRoot} && php artisan tinker --execute="Schema::getColumnListing('${table}');"`;

        return new Promise((resolve) => {
            exec(command, (error, stdout, stderr) => {
                resolve({
                    content: [
                        {
                            type: "text",
                            text: JSON.stringify(
                                {
                                    table: table,
                                    success: !error,
                                    columns: stdout,
                                    error: stderr,
                                },
                                null,
                                2
                            ),
                        },
                    ],
                });
            });
        });
    }

    async run() {
        const transport = new StdioServerTransport();
        await this.server.connect(transport);
        console.error("🚀 Laravel MCP Server hazır!");
    }
}

const server = new LaravelMCP();
server.run().catch(console.error);
