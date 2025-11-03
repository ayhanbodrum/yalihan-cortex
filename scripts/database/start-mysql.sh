#!/bin/bash
# Bu betik MAMP MySQL servisini başlatır ve gerekli bilgileri gösterir

echo "MAMP MySQL servisi başlatılıyor..."
open -a MAMP
# Servisin başlaması için biraz bekle
sleep 5

echo "==================================="
echo "MySQL Connection Details:"
echo "Host: 127.0.0.1"
echo "Port: 8889"
echo "Username: root"
echo "Password: root"
echo "Database: emlakpro"
echo "==================================="
echo "Laravel projesi şu konumda: /Users/macbookpro/Projects/Cursor Emlak Pro"
echo "Laravel sunucusunu başlatmak için: cd '/Users/macbookpro/Projects/Cursor Emlak Pro' && php artisan serve --port=8002"
echo "==================================="
