define(["lib/fn/bind"],function(bind){return{init:function($container){this.arrows={$next:$container.find(".section-switcher__nav__next"),$prev:$container.find(".section-switcher__nav__prev")};$container.on("click",".section-switcher__nav a",function(e){this.trigger("arrowclicked",e)}.bind(this))},arrowclicked:function(e){this.switchByHash(e.currentTarget.hash)},sectionselected:function(newIndex){var prevIndex=this.getPrevIndex(newIndex);var nextIndex=this.getNextIndex(newIndex);this.$container.toggleClass("section-switcher--left-edge",prevIndex===false).toggleClass("section-switcher--right-edge",nextIndex===false);this.arrows.$prev.attr("href","#"+this.$elements.eq(prevIndex).attr("id"));this.arrows.$next.attr("href","#"+this.$elements.eq(nextIndex).attr("id"))}}});