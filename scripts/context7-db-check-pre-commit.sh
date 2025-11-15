# Context7 Database Schema Compliance Check - Pre-Commit Hook Integration
#
# Bu script pre-commit hook tarafından çağrılır ve veritabanı şema uyumluluğunu kontrol eder.
#
# Kullanım: scripts/context7-db-check-pre-commit.sh
#
# Context7 Standard: C7-DB-PRE-COMMIT-CHECK-2025-11-09

#!/bin/bash

# Colors
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
NC='\033[0m' # No Color

echo -e "${YELLOW}🔍 Context7 Database Schema Compliance Check...${NC}"

# Run PHP compliance checker
php scripts/context7-database-compliance-check.php
EXIT_CODE=$?

if [ $EXIT_CODE -ne 0 ]; then
    echo -e "\n${RED}❌ Context7 Database Schema uyumsuzlukları tespit edildi!${NC}"
    echo -e "${YELLOW}💡 Migration yaparak sorunları çözebilirsiniz.${NC}"
    echo -e "${YELLOW}   Örnek: php artisan make:migration add_status_column_to_TABLENAME_table${NC}\n"
    exit 1
fi

echo -e "${GREEN}✅ Database schema Context7 kurallarına uygun!${NC}"
exit 0

