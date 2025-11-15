# Context7 MCP Kullanım Kılavuzu (Yalıhan Bekçi Uyumlu)

## Başlatma
- Lokal (stdio): `CONTEXT7_API_KEY=<anahtarınız> npm run mcp:context7`
- HTTP (uzak): MCP istemcide `url: https://mcp.context7.com/mcp` ve `CONTEXT7_API_KEY` header.

## Güvenlik
- API anahtarını `ENV` ile geçin, kodda hardcode etmeyin.
- Rate-limit ve 401/403 durumlarını kontrol edin; gerekiyorsa anahtar oluşturun.

## API
- `GET /api/v1/search?query=<terim>`: kütüphane arama
- `GET /api/v1/{repo}/{library}?type=txt&tokens=3000&topic=<başlık>`: doküman çekme

## Performans
- `tokens` ile yanıt boyutunu sınırlayın.
- Uzak HTTP modunda API key ile daha yüksek oranlar.

## Entegrasyon
- Trae/Cursor/VS Code için MCP konfigürasyon örnekleri README’deki formatla uyumlu.