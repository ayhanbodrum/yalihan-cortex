#!/bin/bash

# Dead Code Phase 2 Cleanup Script
# Gerçek dead code'ları güvenli şekilde archive'e taşır

set -euo pipefail

# Source helper libraries
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"
source "$SCRIPT_DIR/lib/logger.sh"

# Setup logging
setup_logging_trap

# Archive directory
ARCHIVE_DIR="archive/dead-code-phase2-$(date +%Y%m%d)"
mkdir -p "$ARCHIVE_DIR"

print_header "Dead Code Phase 2 Cleanup"

# Dead code analysis JSON dosyası
ANALYSIS_FILE=".yalihan-bekci/reports/dead-code-analysis-2025-11-11-123435.json"

if [ ! -f "$ANALYSIS_FILE" ]; then
    print_error "Dead code analysis dosyası bulunamadı: $ANALYSIS_FILE"
    exit 1
fi

print_info "Dead code analizi yükleniyor..."

# Python script ile gerçek dead code'ları filtrele
REAL_DEAD_CODE=$(python3 << 'PYTHON_SCRIPT'
import json, sys

with open('.yalihan-bekci/reports/dead-code-analysis-2025-11-11-123435.json', 'r') as f:
    data = json.load(f)

unused = data.get('unused_classes', [])
real_dead_code = []

for cls in unused:
    class_name = cls.get('class', '')
    file_path = cls.get('file', '')

    # False positive kontrolü (Service Provider, Middleware, Handler, yorum içindeki false positive'ler)
    is_false_positive = (
        'Provider' in class_name or 'Provider' in file_path or
        'Middleware' in class_name or 'Middleware' in file_path or
        'Handler' in class_name or 'Handler' in file_path or
        class_name in ['ExampleController', 'varsa', 'mevcutsa']
    )

    if not is_false_positive:
        real_dead_code.append(cls)

# JSON çıktısı
print(json.dumps(real_dead_code, indent=2))
PYTHON_SCRIPT
)

if [ -z "$REAL_DEAD_CODE" ] || [ "$REAL_DEAD_CODE" == "[]" ]; then
    print_success "Gerçek dead code bulunamadı! Tüm kullanılmayan class'lar false positive."
    exit 0
fi

MOVED=0
SKIPPED=0

# Her bir dead code için kontrol ve taşıma
echo "$REAL_DEAD_CODE" | python3 -c "
import json, sys, os

dead_code = json.load(sys.stdin)
moved = 0
skipped = 0

for cls in dead_code:
    file_path = cls.get('file', '')
    class_name = cls.get('class', '')

    if not file_path or not os.path.exists(file_path):
        print(f'⚠️  Atlandı: {class_name} (dosya bulunamadı: {file_path})')
        skipped += 1
        continue

    # Son kontrol: Route'larda kullanılıyor mu?
    if 'Controller' in class_name:
        # Controller ise route kontrolü yap
        import subprocess
        result = subprocess.run(['grep', '-r', class_name, 'routes/'],
                              capture_output=True, text=True)
        if result.returncode == 0 and result.stdout:
            print(f'⚠️  Atlandı: {class_name} (route\'da kullanılıyor)')
            skipped += 1
            continue

    # Archive'e taşı
    archive_path = f'archive/dead-code-phase2-$(date +%Y%m%d)/{file_path}'
    archive_dir = os.path.dirname(archive_path)
    os.makedirs(archive_dir, exist_ok=True)

    # Dosyayı taşı
    os.rename(file_path, archive_path)
    print(f'✅ Taşındı: {file_path} → {archive_path}')
    moved += 1

print(f'\\n📊 Özet: {moved} taşındı, {skipped} atlandı')
"

print_success "Dead Code Phase 2 Cleanup tamamlandı!"

