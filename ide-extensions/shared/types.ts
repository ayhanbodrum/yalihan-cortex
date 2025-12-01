export interface Context7Violation {
    type: string;
    severity: 'high' | 'medium' | 'low';
    line: number;
    column: number;
    message: string;
    filePath: string;
}

export interface DevelopmentIdea {
    title: string;
    description: string;
    category: string;
    priority: string;
    estimated_effort: string;
    business_value: string;
    tags: string[];
}

export interface ProjectHealth {
    overall_score: number;
    code_quality: number;
    performance: number;
    compliance: number;
    security: number;
    trends: string;
    critical_issues: string;
    action_items: string;
}

export interface MCPServerConfig {
    url: string;
    port: number;
    enabled: boolean;
}
