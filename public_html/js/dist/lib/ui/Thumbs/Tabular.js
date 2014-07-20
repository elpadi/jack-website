define(["require","../../underscore.extra","../../../components/zepto/zepto","../Thumbs"],function(require){var _=require("../../underscore.extra").underscore;var $=require("../../../components/zepto/zepto");var Thumbs=require("../Thumbs");function TabularThumbs(){}TabularThumbs.prototype=new Thumbs.Thumbs;TabularThumbs.prototype.constructor=TabularThumbs;TabularThumbs.prototype.imagesPerRow=8;TabularThumbs.prototype.getLinkWrapper=function(index){var a=document.createElement("a");a.href=this.fullSources[index];a.className="image";return a};TabularThumbs.prototype.getCellWrapper=function(index){var td=document.createElement("td");var lastIndex=this.imagesPerRow-1;td.className=_.getFromMap({0:"first",lastIndex:"last"},index%this.imagesPerRow,"");return td};TabularThumbs.prototype.addNewRow=function(){return $(document.createElement("row")).appendTo(this.$container)};TabularThumbs.prototype.insertThumbCell=function(td,index){var $row=index%this.imagesPerRow?this.$container.find("tr").last():this.addNewRow();$row.append(td)};TabularThumbs.prototype.getSelectedThumb=function(e){return $(e.target).closest("td")};TabularThumbs.prototype.addThumb=function($thumbImg,index){var a=this.getLinkWrapper(index);var td=this.getCellWrapper(index);$thumbImg.appendTo(a);td.appendChild(a);this.insertThumbCell(td,index)};return{Thumbs:TabularThumbs}});