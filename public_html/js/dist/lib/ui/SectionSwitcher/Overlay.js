define(["lib/fn/bind"],function(bind){var Overlay={};this.$overlay.on("click",function(e){if(e.target.id==="sections-overlay"){this.isBusy=true;this.isOverlayVisible&&this.hideOverlay();fadeOut(this.$elements.eq(this.currentIndex),700,function(){this.onSwitchEnd(-1);this.isBusy=false}.bind(this))}}.bind(this));SiteSections.prototype.$overlay=$("#sections-overlay");SiteSections.prototype.isOverlayVisible=false;SiteSections.prototype.hideOverlay=function(){fadeOut(this.$overlay,700);this.isOverlayVisible=false};SiteSections.prototype.showOverlay=function(){fadeIn(this.$overlay,700,null,.65);this.isOverlayVisible=true};var needsOverlay=$(this.$elements.eq(index)).hasClass("needs-overlay");if(!needsOverlay&&this.isOverlayVisible){this.hideOverlay()}if(!this.isOverlayVisible&&needsOverlay){this.showOverlay()}return Overlay});