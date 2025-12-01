#!/bin/bash

# Yalıhan Emlak - Git Worktree Temizlik Script'i
# Bu script gereksiz worktree'leri ve geçici dalları temizler

echo "🧹 Git Worktree Temizlik İşlemi Başlatılıyor..."
echo "======================================================"

# Ana dizine git
cd /Users/macbookpro/Projects/yalihanai

# Mevcut worktree'leri listele
echo "📋 Mevcut Worktree'ler:"
git worktree list

echo ""
echo "🗑️  Geçici Worktree'leri Temizleme..."

# Git worktree list çıktısından gerçek yolları al ve temizle
echo "🔍 Gerçek worktree yollarını tespit ediyorum..."

# Ana worktree hariç diğer tüm worktree'leri kaldır
git worktree list | grep -v "$(pwd)" | while read -r line; do
    # Satırdan yol bilgisini çıkar (ilk sütun)
    worktree_path=$(echo "$line" | awk '{print $1}')
    if [ -n "$worktree_path" ] && [ "$worktree_path" != "$(pwd)" ]; then
        echo "🗑️  Kaldırılıyor: $worktree_path"
        git worktree remove --force "$worktree_path" 2>/dev/null || echo "⚠️  Kaldırılamadı: $worktree_path"
    fi
done

echo ""
echo "🗑️  Geçici Dalları Temizleme..."

# Ana dal hariç tüm geçici dalları kaldır
git branch | grep -v "main" | grep -v "\*" | while read -r branch; do
    branch=$(echo "$branch" | xargs) # Whitespace'leri temizle
    if [ -n "$branch" ] && [ "$branch" != "main" ]; then
        echo "🗑️  Dal kaldırılıyor: $branch"
        git branch -D "$branch" 2>/dev/null || echo "⚠️  Kaldırılamadı: $branch"
    fi
done

echo ""
echo "🧹 Git Cleanup İşlemleri..."

# Git temizlik işlemleri
git worktree prune
git reflog expire --expire=now --all
git gc --prune=now

echo ""
echo "📋 Temizlik Sonrası Durum:"
echo "======================================================"
git worktree list
echo ""
git branch

echo ""
echo "✅ Git Worktree Temizlik İşlemi Tamamlandı!"
echo "✨ Artık sadece main branch ve ana worktree mevcut."
