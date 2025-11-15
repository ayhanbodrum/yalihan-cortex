import AIService from './services/AIService.js'

document.addEventListener('DOMContentLoaded', () => {
  try {
    AIService.registerProvider('backend', {
      base: '/api/admin/ai',
      absolute: true,
      operations: {
        chat: { path: '/chat', method: 'POST' },
        pricePredict: { path: '/price/predict', method: 'POST' },
        suggestFeatures: { path: '/suggest-features', method: 'POST' },
      },
    })
    AIService.useProvider('backend')
    try { window.AdminAIService = AIService } catch (e) {}
    if (window.AIOrchestrator && typeof window.AIOrchestrator.setMiddlewares === 'function') {
      window.AIOrchestrator.setMiddlewares({
        request: [function (payload) { return payload }],
        response: [function (res) { return res }],
      })
    }
    console.log('✅ AI provider "backend" kayıt edildi ve aktif')
  } catch (e) {
    console.warn('AI provider kaydı yapılamadı:', e)
  }
})