define([],function(){var easing={"in":{quad:function(x,t,b,c,d){return c*(t/=d)*t+b},cubic:function(x,t,b,c,d){return c*(t/=d)*t*t+b},quart:function(x,t,b,c,d){return c*(t/=d)*t*t*t+b},quint:function(x,t,b,c,d){return c*(t/=d)*t*t*t*t+b},sine:function(x,t,b,c,d){return-c*Math.cos(t/d*(Math.PI/2))+c+b},expo:function(x,t,b,c,d){return t==0?b:c*Math.pow(2,10*(t/d-1))+b},circ:function(x,t,b,c,d){return-c*(Math.sqrt(1-(t/=d)*t)-1)+b},elastic:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0)return b;if((t/=d)==1)return b+c;if(!p)p=d*.3;if(a<Math.abs(c)){a=c;var s=p/4}else var s=p/(2*Math.PI)*Math.asin(c/a);return-(a*Math.pow(2,10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p))+b},back:function(x,t,b,c,d,s){if(s==undefined)s=1.70158;return c*(t/=d)*t*((s+1)*t-s)+b},bounce:function(x,t,b,c,d){return c-easing.out.bounce(x,d-t,0,c,d)+b}},out:{quad:function(x,t,b,c,d){return-c*(t/=d)*(t-2)+b},cubic:function(x,t,b,c,d){return c*((t=t/d-1)*t*t+1)+b},quart:function(x,t,b,c,d){return-c*((t=t/d-1)*t*t*t-1)+b},quint:function(x,t,b,c,d){return c*((t=t/d-1)*t*t*t*t+1)+b},sine:function(x,t,b,c,d){return c*Math.sin(t/d*(Math.PI/2))+b},expo:function(x,t,b,c,d){return t==d?b+c:c*(-Math.pow(2,-10*t/d)+1)+b},circ:function(x,t,b,c,d){return c*Math.sqrt(1-(t=t/d-1)*t)+b},elastic:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0)return b;if((t/=d)==1)return b+c;if(!p)p=d*.3;if(a<Math.abs(c)){a=c;var s=p/4}else var s=p/(2*Math.PI)*Math.asin(c/a);return a*Math.pow(2,-10*t)*Math.sin((t*d-s)*(2*Math.PI)/p)+c+b},back:function(x,t,b,c,d,s){if(s==undefined)s=1.70158;return c*((t=t/d-1)*t*((s+1)*t+s)+1)+b},bounce:function(x,t,b,c,d){if((t/=d)<1/2.75){return c*(7.5625*t*t)+b}else if(t<2/2.75){return c*(7.5625*(t-=1.5/2.75)*t+.75)+b}else if(t<2.5/2.75){return c*(7.5625*(t-=2.25/2.75)*t+.9375)+b}else{return c*(7.5625*(t-=2.625/2.75)*t+.984375)+b}}},inOut:{quad:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t+b;return-c/2*(--t*(t-2)-1)+b},cubic:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t*t+b;return c/2*((t-=2)*t*t+2)+b},quart:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t*t*t+b;return-c/2*((t-=2)*t*t*t-2)+b},quint:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t*t*t*t+b;return c/2*((t-=2)*t*t*t*t+2)+b},sine:function(x,t,b,c,d){return-c/2*(Math.cos(Math.PI*t/d)-1)+b},expo:function(x,t,b,c,d){if(t==0)return b;if(t==d)return b+c;if((t/=d/2)<1)return c/2*Math.pow(2,10*(t-1))+b;return c/2*(-Math.pow(2,-10*--t)+2)+b},circ:function(x,t,b,c,d){if((t/=d/2)<1)return-c/2*(Math.sqrt(1-t*t)-1)+b;return c/2*(Math.sqrt(1-(t-=2)*t)+1)+b},elastic:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0)return b;if((t/=d/2)==2)return b+c;if(!p)p=d*(.3*1.5);if(a<Math.abs(c)){a=c;var s=p/4}else var s=p/(2*Math.PI)*Math.asin(c/a);if(t<1)return-.5*(a*Math.pow(2,10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p))+b;return a*Math.pow(2,-10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p)*.5+c+b},back:function(x,t,b,c,d,s){if(s==undefined)s=1.70158;if((t/=d/2)<1)return c/2*(t*t*(((s*=1.525)+1)*t-s))+b;return c/2*((t-=2)*t*(((s*=1.525)+1)*t+s)+2)+b},bounce:function(x,t,b,c,d){if(t<d/2)return jQuery.easing.easeInBounce(x,t*2,0,c,d)*.5+b;return easing.out.bounce(x,t*2-d,0,c,d)*.5+c*.5+b}}};easing.addToJquery=function(){jQuery.extend(jQuery.easing,{easeInQuad:easing.in.quad,easeOutQuad:easing.out.quad,easeInOutQuad:easing.inOut.quad,easeInCubic:easing.in.cubic,easeOutCubic:easing.out.cubic,easeInOutCubic:easing.inOut.cubic,easeInQuart:easing.in.quart,easeOutQuart:easing.out.quart,easeInOutQuart:easing.inOut.quart,easeInQuint:easing.in.quint,easeOutQuint:easing.out.quint,easeInOutQuint:easing.inOut.quint,easeInSine:easing.in.sine,easeOutSine:easing.out.sine,easeInOutSine:easing.inOut.sine,easeInExpo:easing.in.expo,easeOutExpo:easing.out.expo,easeInOutExpo:easing.inOut.expo,easeInCirc:easing.in.circ,easeOutCirc:easing.out.circ,easeInOutCirc:easing.inOut.circ,easeInElastic:easing.in.elastic,easeOutElastic:easing.out.elastic,easeInOutElastic:easing.inOut.elastic,easeInBack:easing.in.back,easeOutBack:easing.out.back,easeInOutBack:easing.inOut.back,easeInBounce:easing.in.bounce,easeOutBounce:easing.out.bounce,easeInOutBounce:easing.inOut.bounce})};return easing});