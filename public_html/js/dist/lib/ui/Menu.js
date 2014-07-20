define(["jquery","underscore"],function(jquery,underscore){function Menu($container){var _this=this;this.$container=$container;this.$submenus=$container.find(".dropdown-menu__submenu");this.$submenuHandles=$container.find(".dropdown-menu__submenu-handle");this.$container.on("click",".dropdown-menu__submenu-handle",function(e){var $handle=$(e.currentTarget);_this.onSubmenuHandleClick(e,$handle,_this.getSubmenuByHandle($handle))})}Menu.prototype.$container=null;Menu.prototype.$submenus=null;Menu.prototype.SUBMENU_EXPANDED_CLASSNAME="dropdown-menu__submenu--expanded";Menu.prototype.onSubmenuHandleClick=function(e,$handle,$submenu){e.preventDefault();this.hideOtherSubmenus($submenu);this.toggleSubmenu($submenu)};Menu.prototype.hideAllSubmenus=function(){this.$submenus.removeClass(this.SUBMENU_EXPANDED_CLASSNAME)};Menu.prototype.hideOtherSubmenus=function($submenu){this.$submenus.not($submenu.parentsUntil("nav","ul").add($submenu)).removeClass(this.SUBMENU_EXPANDED_CLASSNAME)};Menu.prototype.getSubmenuByHandle=function($handle){return $handle.next()};Menu.prototype.onSubmenuChange=function($submenu){};Menu.prototype.areAllSubmenusClosed=function(){return this.$container.find("."+this.SUBMENU_EXPANDED_CLASSNAME).length===0};Menu.prototype.showSubmenu=function($submenu){$submenu.addClass(this.SUBMENU_EXPANDED_CLASSNAME);this.onSubmenuChange($submenu)};Menu.prototype.hideSubmenu=function($submenu){$submenu.removeClass(this.SUBMENU_EXPANDED_CLASSNAME);this.onSubmenuChange($submenu)};Menu.prototype.toggleSubmenu=function($submenu){$submenu.toggleClass(this.SUBMENU_EXPANDED_CLASSNAME);this.onSubmenuChange($submenu)};return Menu});