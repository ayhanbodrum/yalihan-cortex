#!/bin/bash

# 🛡️ Yalıhan Bekçi - Hızlı Başlatma

echo "╔══════════════════════════════════════════╗"
echo "║   🛡️  YALİHAN BEKÇİ                    ║"
echo "╚══════════════════════════════════════════╝"
echo ""

cd "$(dirname "$0")/server"

case "$1" in
  start)
    echo "🚀 Bekçi başlatılıyor..."
    npm run bekci > /tmp/yalihan-bekci.log 2>&1 &
    PID=$!
    echo $PID > ../bekci.pid
    echo "✅ Başlatıldı (PID: $PID)"
    echo "📍 Port: 3334"
    echo "📝 Log: /tmp/yalihan-bekci.log"
    sleep 2
    curl -s http://localhost:3334/ | jq -r '"✅ \(.name) aktif!"'
    ;;

  stop)
    echo "🛑 Bekçi durduruluyor..."
    if [ -f "../bekci.pid" ]; then
      PID=$(cat ../bekci.pid)
      kill $PID 2>/dev/null && echo "✅ Durduruldu (PID: $PID)" || echo "⚠️ Process bulunamadı"
      rm ../bekci.pid
    else
      killall node 2>/dev/null && echo "✅ Tüm node process'leri durduruldu"
    fi
    ;;

  status)
    echo "📊 Bekçi Durumu:"
    echo ""
    if curl -s http://localhost:3334/ > /dev/null 2>&1; then
      curl -s http://localhost:3334/ | jq .
      echo ""
      echo "✅ Bekçi ÇALIŞIYOR"
    else
      echo "❌ Bekçi KAPALI"
    fi
    ;;

  restart)
    $0 stop
    sleep 2
    $0 start
    ;;

  kurallar)
    echo "📋 Öğrenilmiş Context7 Kuralları:"
    curl -s http://localhost:3334/context7/rules | jq -r '.rules.forbidden_list | "🚫 Yasaklı:\n" + (. | map("  - " + .) | join("\n"))'
    ;;

  sistem)
    echo "🏗️ Sistem Yapısı:"
    curl -s http://localhost:3334/system/status | jq -r '.status.systemStructure | "Models: \(.models.count)\nControllers: \(.controllers.count)\nMigrations: \(.migrations.count)\nViews: \(.views.count)"'
    ;;

  *)
    echo "Kullanım: $0 {start|stop|status|restart|kurallar|sistem}"
    echo ""
    echo "Komutlar:"
    echo "  start    - Bekçiyi başlat"
    echo "  stop     - Bekçiyi durdur"
    echo "  status   - Durum kontrol"
    echo "  restart  - Yeniden başlat"
    echo "  kurallar - Context7 kurallarını göster"
    echo "  sistem   - Sistem yapısını göster"
    ;;
esac

