define(["lib/math/NumberPair"],function(NumberPair){function Point(x,y){NumberPair.call(this,x,y);this.alias("x","y");this.setAliasesValues()}function A(){}A.prototype=NumberPair.prototype;Point.prototype=new A;Point.prototype.constructor=Point;Point.prototype.add=function(point){this.setPair(this.x+point.x,this.y+point.y)};Point.prototype.setX=function(x){this.setPair(x,this.y)};Point.prototype.setY=function(y){this.setPair(this.x,y)};return Point});