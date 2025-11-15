#!/bin/bash

# 📚 Dynamic Documentation Index Generator
# Otomatik olarak her klasör için README.md oluşturur

echo "📚 Dynamic Index Generator - Starting..."
echo ""

# Function: Generate index for a directory
generate_index() {
    local dir=$1
    local readme="$dir/README.md"
    local dir_name=$(basename "$dir")

    # Emoji seç
    local emoji="📚"
    case "$dir_name" in
        "active") emoji="⭐" ;;
        "ai-training") emoji="🤖" ;;
        "technical") emoji="🔧" ;;
        "archive") emoji="📦" ;;
        "docs") emoji="📖" ;;
    esac

    echo "$emoji Generating index for: $dir_name"

    # Create README.md
    cat > "$readme" << EOF
# $emoji $dir_name Dokümantasyonu

**Otomatik oluşturuldu:** $(date '+%d %B %Y, %H:%M')
**Klasör:** \`$dir\`

---

## 📄 Dosyalar

EOF

    # List markdown files
    local file_count=0
    find "$dir" -maxdepth 1 -name "*.md" ! -name "README.md" | sort | while read file; do
        if [ -f "$file" ]; then
            # Extract title from first line
            local title=$(head -n 1 "$file" 2>/dev/null | sed 's/#*//g' | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')

            # If no title, use filename
            if [ -z "$title" ]; then
                title=$(basename "$file" .md)
            fi

            # Get file size
            local size=$(ls -lh "$file" | awk '{print $5}')

            # Add to README
            echo "- **[$title]($(basename "$file"))** ($size)" >> "$readme"
            file_count=$((file_count + 1))
        fi
    done

    # Add summary
    cat >> "$readme" << EOF

---

## 📊 Özet

- **Toplam Dosya:** $file_count adet
- **Son Güncelleme:** $(date '+%d.%m.%Y')

---

**🎯 Bu index otomatik oluşturulmuştur. Güncellemek için:**

\`\`\`bash
./scripts/generate-doc-index.sh
\`\`\`
EOF

    echo "  ✅ Created: $readme ($file_count files)"
}

# Main execution
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Generate indices for key directories
if [ -d "docs/active" ]; then
    generate_index "docs/active"
fi

if [ -d "docs/ai-training" ]; then
    generate_index "docs/ai-training"
fi

if [ -d "docs/technical" ]; then
    generate_index "docs/technical"
fi

if [ -d "docs" ]; then
    generate_index "docs"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Index generation complete!"
echo ""
echo "📚 Generated indices:"
echo "  • docs/README.md"
echo "  • docs/active/README.md"
echo "  • docs/ai-training/README.md"
echo "  • docs/technical/README.md"
