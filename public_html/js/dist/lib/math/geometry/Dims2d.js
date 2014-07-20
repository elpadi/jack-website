define(["../NumberPair"],function(NumberPair){function Dims2d(width,height){NumberPair.call(this,width,height);this.alias("width","height","aspectRatio");this.setAliasesValues()}function A(){}A.prototype=NumberPair.prototype;Dims2d.prototype=new A;Dims2d.prototype.constructor=Dims2d;Dims2d.fromObject=function(el){return new Dims2d(el.width,el.height)};Dims2d.prototype.setWidth=function(width){this.setPair(width,this.height)};Dims2d.prototype.setHeight=function(height){this.setPair(this.width,height)};Dims2d.prototype.contain=function(outerDims,aspectRatio){if(arguments.length<2){aspectRatio=isNaN(this.aspectRatio)?outerDims.aspectRatio:this.aspectRatio}if(aspectRatio>outerDims.aspectRatio){this.setPair(outerDims.width,outerDims.width/aspectRatio)}else{this.setPair(outerDims.height*aspectRatio,outerDims.height)}};return Dims2d});