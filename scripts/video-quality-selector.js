!function(){"use strict";var e=null;e=void 0===window.videojs&&"function"==typeof require?require("video.js"):window.videojs,function(e,t){var r,n={ui:!0},l=t.getComponent("MenuItem"),s=t.extend(l,{constructor:function(e,r){r.selectable=!0,l.call(this,e,r),this.src=r.src,e.on("resolutionchange",t.bind(this,this.update))}});s.prototype.handleClick=function(e){l.prototype.handleClick.call(this,e),this.player_.currentResolution(this.options_.label)},s.prototype.update=function(){var e=this.player_.currentResolution();this.selected(this.options_.label===e.label)},l.registerComponent("ResolutionMenuItem",s);var o=t.getComponent("MenuButton"),i=t.extend(o,{constructor:function(e,r){if(this.label=document.createElement("span"),r.label="Quality",o.call(this,e,r),this.el().setAttribute("aria-label","Quality"),this.controlText("Quality"),r.dynamicLabel)t.addClass(this.label,"vjs-resolution-button-label"),this.el().appendChild(this.label);else{var n=document.createElement("span");t.addClass(n,"vjs-menu-icon"),this.el().appendChild(n)}e.on("updateSources",t.bind(this,this.update))}});i.prototype.createItems=function(){var e=[],t=this.sources&&this.sources.label||{};for(var r in t)t.hasOwnProperty(r)&&e.push(new s(this.player_,{label:r,src:t[r],selected:r===(!!this.currentSelection&&this.currentSelection.label)}));return e},i.prototype.update=function(){return this.sources=this.player_.getGroupedSrc(),this.currentSelection=this.player_.currentResolution(),this.label.innerHTML=this.currentSelection?this.currentSelection.label:"",o.prototype.update.call(this)},i.prototype.buildCSSClass=function(){return o.prototype.buildCSSClass.call(this)+" vjs-resolution-button"},o.registerComponent("ResolutionMenuButton",i),r=function(e){function r(e,t){return e.res&&t.res?+t.res-+e.res:0}function l(e){var t={label:{},res:{},type:{}};return e.map(function(e){s(t,"label",e),s(t,"res",e),s(t,"type",e),o(t,"label",e),o(t,"res",e),o(t,"type",e)}),t}function s(e,t,r){null==e[t][r[t]]&&(e[t][r[t]]=[])}function o(e,t,r){e[t][r[t]].push(r)}function u(e,t){var r=c.default,n="";return"high"===r?(r=t[0].res,n=t[0].label):"low"!==r&&null!=r&&e.res[r]?e.res[r]&&(n=e.res[r][0].label):(r=t[t.length-1].res,n=t[t.length-1].label),{res:r,label:n,sources:e.res[r]}}function a(e){var t={highres:{res:1080,label:"1080",yt:"highres"},hd1080:{res:1080,label:"1080",yt:"hd1080"},hd720:{res:720,label:"720",yt:"hd720"},large:{res:480,label:"480",yt:"large"},medium:{res:360,label:"360",yt:"medium"},small:{res:240,label:"240",yt:"small"},tiny:{res:144,label:"144",yt:"tiny"},auto:{res:0,label:"auto",yt:"auto"}},r=function(t,r,n){return e.tech_.ytPlayer.setPlaybackQuality(r[0]._yt),e.trigger("updateSources"),e};c.customSourcePicker=r,e.tech_.ytPlayer.setPlaybackQuality("auto"),e.tech_.ytPlayer.addEventListener("onPlaybackQualityChange",function(n){for(var l in t)if(l.yt===n.data)return void e.currentResolution(l.label,r)}),e.one("play",function(){var n=[];e.tech_.ytPlayer.getAvailableQualityLevels().map(function(r){n.push({src:e.src().src,type:e.src().type,label:t[r].label,res:t[r].res,_yt:t[r].yt})}),e.groupedSrc=l(n);var s={label:"auto",res:0,sources:e.groupedSrc.label.auto};this.currentResolutionState={label:s.label,sources:s.sources},e.trigger("updateSources"),e.setSourcesSanitized(s.sources,s.label,r)})}var c=t.mergeOptions(n,e),h=this;h.updateSrc=function(e){if(!e)return h.src();e=e.filter(function(e){try{return""!==h.canPlayType(e.type)}catch(e){return!0}}),this.currentSources=e.sort(r),this.groupedSrc=l(this.currentSources);var t=u(this.groupedSrc,this.currentSources);return this.currentResolutionState={label:t.label,sources:t.sources},h.trigger("updateSources"),h.setSourcesSanitized(t.sources,t.label),h.trigger("resolutionchange"),h},h.currentResolution=function(e,t){if(null==e)return this.currentResolutionState;if(this.groupedSrc&&this.groupedSrc.label&&this.groupedSrc.label[e]){var r=this.groupedSrc.label[e],n=h.currentTime(),l=h.paused();return!l&&this.player_.options_.bigPlayButton&&this.player_.bigPlayButton.hide(),h.setSourcesSanitized(r,e,t||c.customSourcePicker),h.one("timeupdate",function(){h.currentTime(n),h.handleTechSeeked_(),l||h.play().handleTechSeeked_(),h.trigger("resolutionchange")}),h}},h.getGroupedSrc=function(){return this.groupedSrc},h.setSourcesSanitized=function(e,t,r){return this.currentResolutionState={label:t,sources:e},"function"==typeof r?r(h,e,t):(h.src(e.map(function(e){return{src:e.src,type:e.type,res:e.res}})),h)},h.ready(function(){if(c.ui){var e=new i(h,c);h.controlBar.resolutionSwitcher=h.controlBar.el_.insertBefore(e.el_,h.controlBar.getChild("fullscreenToggle").el_),h.controlBar.resolutionSwitcher.dispose=function(){this.parentNode.removeChild(this)}}h.options_.sources.length>1&&h.updateSrc(h.options_.sources),"Youtube"===h.techName_&&a(h)})},t.plugin("videoJsResolutionSwitcher",r)}(window,e)}();