define(["lib/fn/bind"],function(bind){var SectionLinks={init:function($container){this.$links=$container.find(".section-switcher__link");this.$links.on("click",function(e){this.trigger("sectionlinkclicked",e)}.bind(this))},sectionlinkclicked:function(e){var name=e.currentTarget.getAttribute("data-section-name");this.switchByHash(name?name:e.currentTarget.hash)},sectionselected:function(newIndex,oldIndex){this.$links.eq(newIndex).addClass("selected").end().eq(oldIndex).removeClass("selected")}};return SectionLinks});