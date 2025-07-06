// DOM Debug Utilities
export const domDebug = {
  // Loading screen kontrolü
  checkLoadingScreen() {
    const loadingScreen = document.getElementById('loading-screen');
    if (loadingScreen) {
      console.log('🔍 Loading Screen Bulundu:', {
        display: window.getComputedStyle(loadingScreen).display,
        opacity: window.getComputedStyle(loadingScreen).opacity,
        pointerEvents: window.getComputedStyle(loadingScreen).pointerEvents,
        zIndex: window.getComputedStyle(loadingScreen).zIndex,
        visible: loadingScreen.offsetParent !== null
      });
      return loadingScreen;
    } else {
      console.log('✅ Loading Screen bulunamadı');
      return null;
    }
  },

  // Modal overlay kontrolü
  checkModalOverlays() {
    const overlays = document.querySelectorAll('.modal-overlay, .modal-loading-overlay');
    console.log(`🔍 ${overlays.length} modal overlay bulundu`);
    
    overlays.forEach((overlay, index) => {
      const style = window.getComputedStyle(overlay);
      console.log(`Overlay ${index + 1}:`, {
        display: style.display,
        opacity: style.opacity,
        pointerEvents: style.pointerEvents,
        zIndex: style.zIndex,
        visible: overlay.offsetParent !== null
      });
    });
    
    return overlays;
  },

  // Pointer events engelleyen elementler
  checkPointerEventsBlockers() {
    const blockers = document.querySelectorAll('*');
    const blockingElements = [];
    
    blockers.forEach(el => {
      const style = window.getComputedStyle(el);
      if (style.pointerEvents === 'none' && el.offsetParent !== null) {
        blockingElements.push({
          element: el,
          tagName: el.tagName,
          className: el.className,
          id: el.id,
          rect: el.getBoundingClientRect()
        });
      }
    });
    
    console.log('🚫 Pointer Events Engelleme:', blockingElements);
    return blockingElements;
  },

  // Z-index yüksek elementler
  checkHighZIndexElements() {
    const elements = document.querySelectorAll('*');
    const highZIndex = [];
    
    elements.forEach(el => {
      const style = window.getComputedStyle(el);
      const zIndex = parseInt(style.zIndex);
      if (zIndex > 1000 && el.offsetParent !== null) {
        highZIndex.push({
          element: el,
          tagName: el.tagName,
          className: el.className,
          id: el.id,
          zIndex: zIndex,
          rect: el.getBoundingClientRect()
        });
      }
    });
    
    console.log('🔝 Yüksek Z-Index Elementler:', highZIndex);
    return highZIndex;
  },

  // Loading state kontrolü
  checkLoadingStates() {
    const loadingElements = document.querySelectorAll('[class*="loading"], [class*="Loading"]');
    console.log(`🔄 ${loadingElements.length} loading class'ı olan element bulundu`);
    
    loadingElements.forEach((el, index) => {
      console.log(`Loading Element ${index + 1}:`, {
        tagName: el.tagName,
        className: el.className,
        id: el.id,
        visible: el.offsetParent !== null,
        rect: el.getBoundingClientRect()
      });
    });
    
    return loadingElements;
  },

  // Tüm overlay'leri temizle (test için)
  clearAllOverlays() {
    const overlays = document.querySelectorAll('.modal-overlay, .modal-loading-overlay, #loading-screen');
    overlays.forEach(overlay => {
      overlay.style.display = 'none';
      overlay.style.pointerEvents = 'none';
    });
    console.log(`🧹 ${overlays.length} overlay temizlendi`);
  },

  // Element'in görünür olup olmadığını kontrol et
  isElementVisible(element) {
    if (!element) return false;
    
    const style = window.getComputedStyle(element);
    const rect = element.getBoundingClientRect();
    
    return {
      display: style.display !== 'none',
      visibility: style.visibility !== 'hidden',
      opacity: parseFloat(style.opacity) > 0,
      hasSize: rect.width > 0 && rect.height > 0,
      inViewport: rect.top < window.innerHeight && rect.bottom > 0,
      pointerEvents: style.pointerEvents !== 'none'
    };
  },

  // Sayfa durumunu özetle
  getPageStatus() {
    console.log('📊 Sayfa Durumu Özeti:');
    
    const loadingScreen = this.checkLoadingScreen();
    const modalOverlays = this.checkModalOverlays();
    const pointerBlockers = this.checkPointerEventsBlockers();
    const highZIndex = this.checkHighZIndexElements();
    const loadingStates = this.checkLoadingStates();
    
    return {
      loadingScreen: loadingScreen ? this.isElementVisible(loadingScreen) : null,
      modalOverlays: modalOverlays.length,
      pointerBlockers: pointerBlockers.length,
      highZIndexElements: highZIndex.length,
      loadingElements: loadingStates.length
    };
  }
};

// Global olarak erişilebilir yap
window.domDebug = domDebug; 