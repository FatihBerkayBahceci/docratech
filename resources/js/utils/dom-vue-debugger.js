// resources/js/utils/dom-vue-debugger.js
// DOM & Vue Render Debugger
(function() {
  console.group('%cDOM & Vue Render Debugger', 'color: #6D28D9; font-weight: bold; font-size: 1.2em;');

  // 1. Vue App Mount Edildi mi?
  const appDiv = document.getElementById('app');
  if (!appDiv) {
    console.error('❌ #app root element bulunamadı!');
  } else if (appDiv.children.length === 0) {
    console.error('❌ #app root element DOM\'da BOŞ! Vue app hiç render edilmemiş.');
  } else {
    console.log('✅ #app root elementte içerik var. Vue app render edilmiş görünüyor.');
  }

  // 2. Router-view Render Edildi mi?
  const routerView = document.querySelector('router-view, [data-v-app], .router-view, .login-page, .dashboard-content');
  if (!routerView) {
    console.warn('⚠️ router-view veya ana sayfa componenti DOM\'da bulunamadı.');
  } else {
    console.log('✅ router-view veya ana sayfa componenti DOM\'da bulundu:', routerView);
  }

  // 3. loading-screen, modal-overlay, floating-shapes gibi overlayler
  [
    '#loading-screen',
    '.modal-overlay',
    '.modal-loading-overlay',
    '.floating-shapes',
    '.toast-container',
    '.toast-list'
  ].forEach(sel => {
    document.querySelectorAll(sel).forEach(el => {
      const style = window.getComputedStyle(el);
      const rect = el.getBoundingClientRect();
      console.log(`🔍 ${sel} bulundu:`, {
        display: style.display,
        opacity: style.opacity,
        pointerEvents: style.pointerEvents,
        zIndex: style.zIndex,
        visible: el.offsetParent !== null,
        width: rect.width,
        height: rect.height
      });
    });
  });

  // 4. Tüm pointer-events: none olan ve görünür elementler
  const pointerBlockers = [];
  document.querySelectorAll('*').forEach(el => {
    const style = window.getComputedStyle(el);
    if (style.pointerEvents === 'none' && el.offsetParent !== null) {
      pointerBlockers.push(el);
    }
  });
  if (pointerBlockers.length) {
    console.warn('🚫 pointer-events: none olan ve görünür elementler:', pointerBlockers);
  } else {
    console.log('✅ pointer-events: none olan görünür element yok.');
  }

  // 5. Yüksek z-index'li ve büyük alan kaplayan elementler
  const highZ = [];
  document.querySelectorAll('*').forEach(el => {
    const style = window.getComputedStyle(el);
    const z = parseInt(style.zIndex);
    const rect = el.getBoundingClientRect();
    if (z > 1000 && rect.width > 200 && rect.height > 200 && el.offsetParent !== null) {
      highZ.push({el, z, rect});
    }
  });
  if (highZ.length) {
    console.warn('🔝 Yüksek z-index ve büyük alan kaplayan elementler:', highZ);
  } else {
    console.log('✅ Yüksek z-index ve büyük alan kaplayan element yok.');
  }

  // 6. Tüm input, button, select, textarea erişilebilir mi?
  document.querySelectorAll('input, button, select, textarea').forEach(el => {
    const style = window.getComputedStyle(el);
    console.log('📝', el, {
      disabled: el.disabled,
      readOnly: el.readOnly,
      tabIndex: el.tabIndex,
      pointerEvents: style.pointerEvents,
      visible: el.offsetParent !== null
    });
  });

  // 7. Ekranın ortasında hangi elementler var?
  const centerElements = document.elementsFromPoint(window.innerWidth/2, window.innerHeight/2);
  console.log('📌 Ekranın ortasında üst üste gelen elementler:', centerElements);

  // 8. Vue global hata yakalayıcı var mı?
  if (window.__VUE_DEVTOOLS_GLOBAL_HOOK__) {
    console.log('✅ Vue Devtools hook bulundu.');
  } else {
    console.warn('⚠️ Vue Devtools hook bulunamadı.');
  }

  // 9. Konsolda hata var mı?
  if (window.console && window.console.error) {
    // Not: Konsol hatalarını doğrudan JS ile almak mümkün değil, elle bakılmalı.
    console.info('ℹ️ Konsolda kırmızı hata mesajı olup olmadığını ayrıca kontrol edin.');
  }

  console.groupEnd();
})(); 